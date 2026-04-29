<?php

namespace App\Services;

use App\DTOs\ArticleDTO;
use App\Models\Article;
use App\Models\Author;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\IssueRepositoryInterface;
use App\Repositories\Interfaces\MediaAssetRepositoryInterface;
use Illuminate\Container\Attributes\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ArticleService
{
    private ?Author $resolvedAuthor = null;

    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly IssueRepositoryInterface $issueRepository,
        private readonly AuthorRepositoryInterface $authorRepository,
        private readonly MediaAssetRepositoryInterface $mediaRepository,
        private readonly ArticleContentNormalizer $contentNormalizer,
    ) {
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->articleRepository->paginate($perPage, $filters);
    }

    public function create(array $data): ArticleDTO
    {
        $payload = $this->preparePayload($data);
        $article = $this->articleRepository->create($payload);

        return ArticleDTO::fromModel($article);
    }

    public function update(int $id, array $data): ArticleDTO
    {
        //data logla
        FacadesLog::info('Updating article data', ['data' => $data]);

        $article = $this->articleRepository->findById($id);

        //article komple logla
        FacadesLog::info('Updating article', ['article' => $article, 'data' => $data]);

        if (!$article) {
            throw new InvalidArgumentException('Makale bulunamadı.');
        }

        $payload = $this->preparePayload($data, $article->slug, $article->id);
       
        FacadesLog::info('Prepared payload for update', ['payload' => $payload]);   
        $article = $this->articleRepository->update($article, $payload);

        return ArticleDTO::fromModel($article);
    }

    public function delete(int $id): void
    {
        $article = $this->articleRepository->findById($id);

        if (!$article) {
            throw new InvalidArgumentException('Makale bulunamadı.');
        }

        $this->articleRepository->delete($article);
    }

    public function find(int $id): ArticleDTO
    {
        $article = $this->articleRepository->findById($id);

        if (!$article) {
            throw new InvalidArgumentException('Makale bulunamadı.');
        }

        $this->ensureArticleContentIsNormalized($article);

        return ArticleDTO::fromModel($article);
    }

    private function preparePayload(array $data, ?string $currentSlug = null, ?int $articleId = null): array
    {
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Makale başlığı gereklidir.');
        }

        $payload = [
            'title' => $data['title'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'is_published' => array_key_exists('is_published', $data)
                ? (bool) $data['is_published']
                : true,
            'published_at' => $data['published_at'] ?? null,
        ];

        $payload['content'] = $this->contentNormalizer->normalize($payload['content']);
        $payload['category_id'] = $this->resolveCategoryId($data['category_id'] ?? null);
        $payload['issue_id'] = $this->resolveIssueId($data['issue_id'] ?? null);
        $payload['author_id'] = $this->resolveAuthorId($data['author_id'] ?? null);

        $slugInput = $data['slug'] ?? $data['title'];
        $authorSlug = $this->resolvedAuthor?->slug;
        $payload['slug'] = $this->generateUniqueSlug($slugInput, $authorSlug, $currentSlug, $articleId);
        $payload['feature_image_id'] = $this->resolveMediaId($data['feature_image_id'] ?? null);

        if ($payload['is_published'] && empty($payload['published_at'])) {
            $payload['published_at'] = now();
        }

        return $payload;
    }

    private function generateUniqueSlug(string $value, ?string $authorSlug = null, ?string $currentSlug = null, ?int $articleId = null): string
    {
        $baseSlug = Str::slug($value);
        if ($baseSlug === '') {
            $baseSlug = Str::uuid()->toString();
        }

        if ($authorSlug) {
            $authorSlug = Str::slug($authorSlug);
            if ($authorSlug !== '' && !Str::endsWith($baseSlug, '-'.$authorSlug)) {
                $baseSlug = trim($baseSlug.'-'.$authorSlug, '-');
            }
        }

        if ($currentSlug === $baseSlug) {
            return $baseSlug;
        }

        $slug = $baseSlug;
        $attempt = 1;

        while ($this->articleRepository->slugExists($slug, $articleId)) {
            $slug = $baseSlug.'-'.$attempt;
            $attempt++;
        }

        return $slug;
    }

    private function resolveCategoryId($categoryId): ?int
    {
        if ($categoryId === null || $categoryId === '') {
            return null;
        }

        $category = $this->categoryRepository->findById((int) $categoryId);

        if (!$category) {
            throw new InvalidArgumentException('Seçilen kategori bulunamadı.');
        }

        return $category->id;
    }

    private function resolveAuthorId($authorId): ?int
    {
        $this->resolvedAuthor = null;

        if ($authorId === null || $authorId === '') {
            return null;
        }

        $author = $this->authorRepository->findById((int) $authorId);

        if (!$author) {
            throw new InvalidArgumentException('Seçilen yazar bulunamadı.');
        }

        $this->resolvedAuthor = $author;

        return $author->id;
    }

    private function resolveIssueId($issueId): ?int
    {
        if ($issueId === null || $issueId === '') {
            return null;
        }

        $issue = $this->issueRepository->findById((int) $issueId);

        if (!$issue) {
            throw new InvalidArgumentException('Seçilen sayı bulunamadı.');
        }

        return $issue->id;
    }

    private function resolveMediaId($mediaId): ?int
    {
        if ($mediaId === null || $mediaId === '') {
            return null;
        }

        $media = $this->mediaRepository->findById((int) $mediaId);

        if (!$media) {
            throw new InvalidArgumentException('Seçilen medya kaydı bulunamadı.');
        }

        return $media->id;
    }

    private function ensureArticleContentIsNormalized(Article $article): void
    {
        $normalized = $this->contentNormalizer->normalize($article->content);

        if ($normalized !== $article->content) {
            $article->content = $normalized;
            $article->save();
        }
    }
}
