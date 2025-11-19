<?php

namespace App\Services;

use App\DTOs\ArticleDTO;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\MediaAssetRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ArticleService
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly AuthorRepositoryInterface $authorRepository,
        private readonly MediaAssetRepositoryInterface $mediaRepository,
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
        $article = $this->articleRepository->findById($id);

        if (!$article) {
            throw new InvalidArgumentException('Makale bulunamadı.');
        }

        $payload = $this->preparePayload($data, $article->slug, $article->id);
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

        $slugInput = $data['slug'] ?? $data['title'];
        $payload['slug'] = $this->generateUniqueSlug($slugInput, $currentSlug, $articleId);

        $payload['category_id'] = $this->resolveCategoryId($data['category_id'] ?? null);
        $payload['author_id'] = $this->resolveAuthorId($data['author_id'] ?? null);
        $payload['feature_image_id'] = $this->resolveMediaId($data['feature_image_id'] ?? null);

        if ($payload['is_published'] && empty($payload['published_at'])) {
            $payload['published_at'] = now();
        }

        return $payload;
    }

    private function generateUniqueSlug(string $value, ?string $currentSlug = null, ?int $articleId = null): string
    {
        $baseSlug = Str::slug($value);
        if ($baseSlug === '') {
            $baseSlug = Str::uuid()->toString();
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
        if ($authorId === null || $authorId === '') {
            return null;
        }

        $author = $this->authorRepository->findById((int) $authorId);

        if (!$author) {
            throw new InvalidArgumentException('Seçilen yazar bulunamadı.');
        }

        return $author->id;
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
}
