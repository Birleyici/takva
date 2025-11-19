<?php

namespace App\Services\Legacy;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Issue;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class LegacyImportService
{
    private const DEFAULT_SECTIONS = ['categories', 'authors', 'articles', 'issues', 'settings'];

    private ConnectionInterface $legacy;

    private array $categoryMap = [];
    private array $categorySlugMap = [];
    private array $authorSlugMap = [];
    private array $authorNameMap = [];
    private array $mediaCache = [];
    private array $missingSamples = [];
    private int $missingFileCount = 0;

    public function __construct(DatabaseManager $db)
    {
        $this->legacy = $db->connection('legacy');
    }

    public function testConnection(): array
    {
        $this->legacy->select('SELECT 1');

        return [
            'categories' => (int) $this->legacy->table('tbl_grup')->where('SilindiMi', 0)->count(),
            'authors' => (int) $this->legacy->table('tbl_yazar')->where('SilindiMi', 0)->count(),
            'articles' => (int) $this->legacy->table('tbl_urun')->where('SilindiMi', 0)->count(),
            'issues' => (int) $this->legacy->table('tbl_yuklemeler')->where('TurId', 6)->where('SilindiMi', 0)->count(),
        ];
    }

    public function import(array $sections, bool $fresh, callable $logger): array
    {
        Storage::disk('public')->makeDirectory('legacy');

        $sections = $this->normalizeSections($sections);

        $this->categoryMap = [];
        $this->categorySlugMap = [];
        $this->authorSlugMap = [];
        $this->authorNameMap = [];
        $this->mediaCache = [];
        $this->missingSamples = [];
        $this->missingFileCount = 0;

        $logger('Legacy database connection verified.');

        if ($fresh) {
            $this->truncateTables($sections, $logger);
        } else {
            $this->primeExistingMaps();
        }

        $summary = [];

        if (in_array('categories', $sections, true)) {
            $summary['categories'] = $this->importCategories($logger);
        }

        if (in_array('authors', $sections, true)) {
            $summary['authors'] = $this->importAuthors($logger);
        }

        if (in_array('articles', $sections, true)) {
            $summary['articles'] = $this->importArticles($logger);
        }

        if (in_array('issues', $sections, true)) {
            $summary['issues'] = $this->importIssues($logger);
        }

        if (in_array('settings', $sections, true)) {
            $summary['settings'] = $this->importSiteSettings($logger);
        }

        $this->reportMissingFiles($logger);

        return $summary;
    }

    private function normalizeSections(array $sections): array
    {
        if (empty($sections)) {
            return self::DEFAULT_SECTIONS;
        }

        $allowed = array_flip(self::DEFAULT_SECTIONS);
        $normalized = [];

        foreach ($sections as $section) {
            $section = strtolower(trim((string) $section));
            if ($section === '' || !isset($allowed[$section])) {
                continue;
            }

            if (!in_array($section, $normalized, true)) {
                $normalized[] = $section;
            }
        }

        return $normalized ?: self::DEFAULT_SECTIONS;
    }

    private function truncateTables(array $sections, callable $logger): void
    {
        Schema::disableForeignKeyConstraints();

        if (in_array('articles', $sections, true)) {
            Article::query()->truncate();
        }

        if (in_array('issues', $sections, true)) {
            Issue::query()->truncate();
        }

        if (in_array('categories', $sections, true)) {
            Category::query()->truncate();
        }

        if (in_array('authors', $sections, true)) {
            Author::query()->truncate();
        }

        if (in_array('settings', $sections, true)) {
            SiteSetting::query()->truncate();
        }

        if ($this->shouldResetMedia($sections)) {
            MediaAsset::query()->truncate();
        }

        Schema::enableForeignKeyConstraints();

        $this->categoryMap = [];
        $this->categorySlugMap = [];
        $this->authorSlugMap = [];
        $this->authorNameMap = [];
        $this->mediaCache = [];

        $logger('Target tables truncated.');
    }

    private function shouldResetMedia(array $sections): bool
    {
        return in_array('articles', $sections, true)
            || in_array('issues', $sections, true)
            || in_array('authors', $sections, true);
    }

    private function primeExistingMaps(): void
    {
        foreach (Category::query()->get(['id', 'slug']) as $category) {
            $this->categorySlugMap[$category->slug] = $category->id;
        }

        foreach (Author::query()->get(['id', 'slug', 'name']) as $author) {
            $this->authorSlugMap[$author->slug] = $author->id;
            $nameKey = Str::slug($author->name ?? '');
            if ($nameKey !== '') {
                $this->authorNameMap[$nameKey] = $author->id;
            }
        }

        foreach (MediaAsset::query()->get(['id', 'file_name']) as $media) {
            if ($media->file_name) {
                $this->mediaCache[$media->file_name] = $media->id;
            }
        }
    }

    private function importCategories(callable $logger): int
    {
        $count = 0;

        $this->legacy->table('tbl_grup as g')
            ->join('tbl_grupdil as gd', 'gd.GrupId', '=', 'g.Id')
            ->where('gd.DilId', 1)
            ->where('g.SilindiMi', 0)
            ->orderBy('g.Id')
            ->select([
                'g.Id as legacy_id',
                'gd.Adi',
                'gd.Aciklama',
                'gd.SeoLink',
                'g.AktifMi',
            ])
            ->chunk(100, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = $this->decode($row->Adi) ?: 'Kategori '.$row->legacy_id;
                    $baseSlug = $this->normalizeSlug($row->SeoLink ?: $name, 'category-'.$row->legacy_id);

                    $category = null;

                    if (isset($this->categorySlugMap[$baseSlug])) {
                        $category = Category::query()->find($this->categorySlugMap[$baseSlug]);
                    }

                    if (!$category) {
                        $category = Category::query()->where('slug', $baseSlug)->first();
                    }

                    if (!$category) {
                        $slug = $this->makeUniqueSlug($baseSlug, Category::class, 'category-'.$row->legacy_id);
                        $category = new Category();
                        $category->slug = $slug;
                    }

                    $category->name = $name;
                    $category->description = $this->nullify($this->decode($row->Aciklama));
                    $category->is_active = (bool) $row->AktifMi;
                    $category->save();

                    $this->categoryMap[$row->legacy_id] = $category->id;
                    $this->categorySlugMap[$category->slug] = $category->id;

                    $count++;
                }
            });

        $logger("Imported {$count} categories.");

        return $count;
    }

    private function importAuthors(callable $logger): int
    {
        $count = 0;

        $this->legacy->table('tbl_yazar')
            ->where('SilindiMi', 0)
            ->orderBy('Id')
            ->select([
                'Id as legacy_id',
                'Adi',
                'Seolink',
                'Foto',
            ])
            ->chunk(100, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $name = $this->decode($row->Adi);
                    if (!$name) {
                        continue;
                    }

                    $baseSlug = $this->normalizeSlug($row->Seolink ?: $name, 'author-'.$row->legacy_id);

                    $author = null;

                    if (isset($this->authorSlugMap[$baseSlug])) {
                        $author = Author::query()->find($this->authorSlugMap[$baseSlug]);
                    }

                    if (!$author) {
                        $author = Author::query()->where('slug', $baseSlug)->first();
                    }

                    if (!$author) {
                        $slug = $this->makeUniqueSlug($baseSlug, Author::class, 'author-'.$row->legacy_id);
                        $author = new Author();
                        $author->slug = $slug;
                    }

                    $author->name = $name;
                    $author->profile_image_id = $this->registerMedia($row->Foto ?? null, $name, 'author');
                    $author->save();

                    $this->authorSlugMap[$author->slug] = $author->id;
                    $nameKey = Str::slug($author->name);
                    if ($nameKey !== '') {
                        $this->authorNameMap[$nameKey] = $author->id;
                    }

                    $count++;
                }
            });

        $logger("Imported {$count} authors.");

        return $count;
    }

    private function importArticles(callable $logger): int
    {
        $count = 0;

        $this->legacy->table('tbl_urun as u')
            ->join('tbl_urundil as ud', 'ud.UrunId', '=', 'u.Id')
            ->where('ud.DilId', 1)
            ->where('u.SilindiMi', 0)
            ->orderBy('u.Id')
            ->select([
                'u.Id as legacy_id',
                'u.GrupId',
                'u.Foto',
                'u.YayinZmn',
                'u.KayitZamani',
                'u.AktifMi',
                'u.SilindiMi',
                'ud.Adi',
                'ud.SeoLink',
                'ud.SeoDesc',
                'ud.Aciklama',
            ])
            ->chunk(50, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    [$title, $authorName] = $this->splitTitleAndAuthor($row->Adi);
                    if (!$title) {
                        continue;
                    }

                    $baseSlug = $this->normalizeSlug($row->SeoLink ?: $title, 'article-'.$row->legacy_id);
                    $article = Article::query()->where('slug', $baseSlug)->first();

                    if (!$article) {
                        $slug = $this->makeUniqueSlug($baseSlug, Article::class, 'article-'.$row->legacy_id);
                        $article = new Article();
                        $article->slug = $slug;
                    }

                    $article->title = $title;
                    $article->excerpt = $this->buildExcerpt($row->SeoDesc ?? null, $row->Aciklama ?? null);
                    $article->content = $this->formatContent($row->Aciklama ?? null);
                    $article->category_id = $this->resolveCategoryId((int) $row->GrupId);
                    $article->author_id = $this->resolveAuthorId($authorName);
                    $article->feature_image_id = $this->registerMedia($row->Foto ?? null, $title, 'article');
                    $article->is_published = (bool) $row->AktifMi && !(bool) $row->SilindiMi;

                    $publishedAt = $this->parseTimestamp($row->YayinZmn, $row->KayitZamani);
                    $article->published_at = $publishedAt;

                    if (!$article->exists && $publishedAt) {
                        $article->created_at = $publishedAt;
                    }

                    $article->save();
                    $count++;
                }
            });

        $logger("Imported {$count} articles.");

        return $count;
    }

    private function importIssues(callable $logger): int
    {
        $processed = 0;

        $rows = $this->legacy->table('tbl_yuklemeler as y')
            ->join('tbl_yuklemelerdil as yd', 'yd.YId', '=', 'y.Id')
            ->where('y.TurId', 6)
            ->where('y.SilindiMi', 0)
            ->where('yd.DilId', 1)
            ->orderBy('y.Sira')
            ->orderBy('y.Id')
            ->select([
                'y.Id as legacy_id',
                'y.OnIzlemeFoto',
                'y.KZ',
                'yd.Adi',
                'yd.Aciklama',
                'yd.Yolu',
            ])
            ->get();

        foreach ($rows as $row) {
            $pdfFile = trim((string) $row->Yolu);
            if ($pdfFile === '' || !Str::endsWith(Str::lower($pdfFile), '.pdf')) {
                continue;
            }

            $title = $this->decode($row->Adi) ?: 'Sayı '.$row->legacy_id;
            $baseSlug = $this->normalizeSlug($title, 'issue-'.$row->legacy_id);
            $issue = Issue::query()->where('slug', $baseSlug)->first();

            if (!$issue) {
                $slug = $this->makeUniqueSlug($baseSlug, Issue::class, 'issue-'.$row->legacy_id);
                $issue = new Issue();
                $issue->slug = $slug;
            }

            $issue->title = $title;
            $issue->description = $this->nullify($this->decode($row->Aciklama));

            $timestamp = $this->parseTimestamp($row->KZ, $row->KZ) ?? now();
            $issue->year = (int) $timestamp->year;
            $issue->month = (int) $timestamp->month;
            $issue->cover_image_id = $this->registerMedia($row->OnIzlemeFoto ?? null, $title, 'issue cover');

            $pdfPath = $this->legacyPath($pdfFile);
            $disk = Storage::disk('public');
            $exists = $disk->exists($pdfPath);
            if (!$exists) {
                $this->noteMissingFile($pdfPath, 'issue pdf');
            }

            $issue->pdf_path = $pdfPath;
            $issue->pdf_original_name = basename($pdfFile) ?: $title;
            $issue->pdf_size = $exists ? $disk->size($pdfPath) : null;

            if (!$issue->exists) {
                $issue->created_at = $timestamp;
            }

            $issue->save();
            $processed++;
        }

        $logger("Imported {$processed} magazine issues.");

        return $processed;
    }

    private function importSiteSettings(callable $logger): int
    {
        $row = $this->legacy->table('tbl_iletisim')->orderBy('Id')->first();

        if (!$row) {
            $logger('No contact row found in legacy database.');
            return 0;
        }

        $settings = SiteSetting::query()->first() ?? new SiteSetting();

        $settings->contact_email = $this->nullify($row->Eposta ?? '') ?: $settings->contact_email;
        $settings->contact_phone = $this->nullify($row->Tel ?? '') ?: $settings->contact_phone;
        $settings->contact_address = $this->nullify($row->Adres ?? '') ?: $settings->contact_address;
        $settings->contact_map_embed = $this->nullify($row->AdresLink ?? '') ?: $settings->contact_map_embed;
        $settings->contact_hero_text = $this->nullify($row->Baslik ?? '') ?: $settings->contact_hero_text;
        $settings->social_twitter = $this->nullify($row->Twitter ?? '') ?: $settings->social_twitter;
        $settings->social_instagram = $this->nullify($row->Instagram ?? '') ?: $settings->social_instagram;
        $settings->social_facebook = $this->nullify($row->Facebook ?? '') ?: $settings->social_facebook;
        $settings->social_whatsapp = $this->nullify($row->Whatsapp ?? '') ?: $settings->social_whatsapp;
        $settings->save();

        $logger('Site settings updated from legacy data.');

        return 1;
    }

    private function resolveCategoryId(?int $legacyId): ?int
    {
        if (!$legacyId) {
            return null;
        }

        if (isset($this->categoryMap[$legacyId])) {
            return $this->categoryMap[$legacyId];
        }

        $row = $this->legacy->table('tbl_grup as g')
            ->join('tbl_grupdil as gd', 'gd.GrupId', '=', 'g.Id')
            ->where('g.Id', $legacyId)
            ->where('gd.DilId', 1)
            ->select([
                'g.Id as legacy_id',
                'gd.Adi',
                'gd.Aciklama',
                'gd.SeoLink',
                'g.AktifMi',
            ])
            ->first();

        if (!$row) {
            return null;
        }

        $name = $this->decode($row->Adi) ?: 'Kategori '.$legacyId;
        $baseSlug = $this->normalizeSlug($row->SeoLink ?: $name, 'category-'.$legacyId);
        $category = $this->categorySlugMap[$baseSlug] ?? null
            ? Category::query()->find($this->categorySlugMap[$baseSlug])
            : Category::query()->where('slug', $baseSlug)->first();

        if (!$category) {
            $slug = $this->makeUniqueSlug($baseSlug, Category::class, 'category-'.$legacyId);
            $category = new Category();
            $category->slug = $slug;
        }

        $category->name = $name;
        $category->description = $this->nullify($this->decode($row->Aciklama));
        $category->is_active = (bool) $row->AktifMi;
        $category->save();

        $this->categoryMap[$legacyId] = $category->id;
        $this->categorySlugMap[$category->slug] = $category->id;

        return $category->id;
    }

    private function resolveAuthorId(?string $name): ?int
    {
        $name = $this->nullify($name);
        if ($name === null) {
            return null;
        }

        $key = Str::slug($name);

        if ($key !== '' && isset($this->authorNameMap[$key])) {
            return $this->authorNameMap[$key];
        }

        $author = null;

        if ($key !== '') {
            $author = Author::query()->where('slug', $key)->first();
        }

        if (!$author) {
            $author = Author::query()->where('name', $name)->first();
        }

        if (!$author) {
            $slug = $this->makeUniqueSlug($key ?: $name, Author::class, 'author-'.Str::random(6));
            $author = new Author();
            $author->slug = $slug;
            $author->name = $name;
            $author->save();
        }

        $this->authorSlugMap[$author->slug] = $author->id;
        if ($key !== '') {
            $this->authorNameMap[$key] = $author->id;
        }

        return $author->id;
    }

    private function splitTitleAndAuthor(?string $value): array
    {
        $decoded = $this->decode($value);

        if (!$decoded) {
            return [null, null];
        }

        if (!str_contains($decoded, '|')) {
            return [$decoded, null];
        }

        $parts = array_map('trim', explode('|', $decoded));
        $author = array_pop($parts);
        $title = trim(implode(' | ', $parts));

        if ($title === '') {
            $title = $decoded;
        }

        return [$title, $author ?: null];
    }

    private function buildExcerpt(?string $seoDesc, ?string $content): ?string
    {
        $text = $this->nullify($this->decode($seoDesc));

        if ($text === null && $content) {
            $plain = strip_tags($this->decode($content) ?? '');
            $plain = preg_replace('/\s+/u', ' ', $plain ?? '');
            $text = $this->nullify($plain);
        }

        if ($text === null) {
            return null;
        }

        return Str::limit($text, 255, '…');
    }

    private function formatContent(?string $content): ?string
    {
        if ($content === null || $content === '') {
            return null;
        }

        return html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function parseTimestamp(?string $value, ?string $fallback = null): ?Carbon
    {
        foreach ([$value, $fallback] as $candidate) {
            if (!$candidate || $candidate === '0000-00-00 00:00:00') {
                continue;
            }

            try {
                return Carbon::parse($candidate);
            } catch (Throwable) {
                continue;
            }
        }

        return null;
    }

    private function registerMedia(?string $fileName, ?string $originalName = null, string $context = 'media'): ?int
    {
        $fileName = trim((string) $fileName);

        if ($fileName === '' || strtolower($fileName) === 'noimage.jpg') {
            return null;
        }

        if (isset($this->mediaCache[$fileName])) {
            return $this->mediaCache[$fileName];
        }

        $media = MediaAsset::query()->where('file_name', $fileName)->first();

        if (!$media) {
            $filePath = $this->legacyPath($fileName);
            $disk = Storage::disk('public');
            $exists = $disk->exists($filePath);
            $size = $exists ? $disk->size($filePath) : null;
            $width = null;
            $height = null;

            if ($exists && $this->isImage($fileName)) {
                $dimensions = @getimagesize($disk->path($filePath));
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
            }

            if (!$exists) {
                $this->noteMissingFile($filePath, $context);
            }

            $media = MediaAsset::create([
                'disk' => 'public',
                'original_name' => $originalName ?: $fileName,
                'file_name' => $fileName,
                'mime_type' => $this->guessMime($fileName),
                'size' => $size,
                'file_path' => $filePath,
                'width' => $width,
                'height' => $height,
            ]);
        }

        return $this->mediaCache[$fileName] = $media->id;
    }

    private function legacyPath(string $fileName): string
    {
        $fileName = ltrim($fileName, '\\/');

        return 'legacy/'.$fileName;
    }

    private function guessMime(string $fileName): ?string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            default => null,
        };
    }

    private function isImage(string $fileName): bool
    {
        return Str::contains(strtolower($fileName), ['.jpg', '.jpeg', '.png', '.gif', '.webp']);
    }

    private function noteMissingFile(string $path, string $context): void
    {
        $this->missingFileCount++;
        if (count($this->missingSamples) < 25) {
            $this->missingSamples[] = $path.' ('.$context.')';
        }
    }

    private function reportMissingFiles(callable $logger): void
    {
        if ($this->missingFileCount === 0) {
            return;
        }

        $logger('⚠️  Missing '.$this->missingFileCount.' legacy files. Copy them into storage/app/public/legacy:');
        foreach ($this->missingSamples as $sample) {
            $logger(' - '.$sample);
        }
    }

    private function decode(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function nullify(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeSlug(?string $value, string $fallback): string
    {
        $decoded = $this->decode($value) ?: $fallback;
        $slug = Str::slug($decoded);

        if ($slug === '') {
            $slug = Str::slug($fallback);
        }

        if ($slug === '') {
            $slug = 'legacy-'.Str::random(8);
        }

        return $slug;
    }

    private function makeUniqueSlug(string $baseSlug, string $modelClass, string $fallback): string
    {
        $slug = Str::slug($baseSlug);

        if ($slug === '') {
            $slug = Str::slug($fallback);
        }

        if ($slug === '') {
            $slug = 'legacy-'.Str::random(8);
        }

        $original = $slug;
        $suffix = 1;

        while ($modelClass::query()->where('slug', $slug)->exists()) {
            $slug = $original.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
