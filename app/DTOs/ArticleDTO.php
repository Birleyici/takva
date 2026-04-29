<?php

namespace App\DTOs;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleDTO
{
    public ?int $id = null;
    public ?string $title = null;
    public ?string $slug = null;
    public ?string $excerpt = null;
    public ?string $content = null;
    public bool $is_published = true;
    public ?string $published_at = null;
    public ?int $issue_id = null;
    public ?CategoryDTO $category = null;
    public ?AuthorDTO $author = null;
    public ?MediaAssetDTO $feature_image = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?Article $article): self
    {
        if (!$article) {
            return new self();
        }

        $article->loadMissing(['category', 'author', 'featureImage']);

        return self::fromArray(
            array_merge(
                $article->toArray(),
                [
                    'category' => $article->category?->toArray(),
                    'author' => $article->author?->toArray(),
                    'feature_image' => $article->featureImage?->toArray(),
                ],
            ),
        );
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->title = $data['title'] ?? null;
        $dto->slug = $data['slug'] ?? null;
        $dto->excerpt = $data['excerpt'] ?? null;
        $dto->content = $data['content'] ?? null;
        $dto->is_published = (bool) ($data['is_published'] ?? true);
        $dto->published_at = $data['published_at'] ?? null;
        $dto->issue_id = $data['issue_id'] ?? null;
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        $category = $data['category'] ?? null;
        if ($category instanceof CategoryDTO) {
            $dto->category = $category;
        } elseif (is_array($category)) {
            $dto->category = CategoryDTO::fromArray($category);
        }

        $author = $data['author'] ?? null;
        if ($author instanceof AuthorDTO) {
            $dto->author = $author;
        } elseif (is_array($author)) {
            $dto->author = AuthorDTO::fromArray($author);
        }

        $featureImage = $data['feature_image'] ?? null;
        if ($featureImage instanceof MediaAssetDTO) {
            $dto->feature_image = $featureImage;
        } elseif (is_array($featureImage)) {
            $dto->feature_image = MediaAssetDTO::fromArray($featureImage);
        }

        return $dto;
    }

    /**
     * @param \Illuminate\Support\Collection<int, Article|array<string, mixed>> $articles
     * @return array<int, ArticleDTO>
     */
    public static function collection(Collection $articles): array
    {
        return $articles->map(function ($article) {
            if ($article instanceof Article) {
                return self::fromModel($article);
            }

            return self::fromArray($article);
        })->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at,
            'issue_id' => $this->issue_id,
            'category' => $this->category?->toArray(),
            'author' => $this->author?->toArray(),
            'feature_image' => $this->feature_image?->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
