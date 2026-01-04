<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Article::query()
            ->with(['category', 'author', 'featureImage'])
            ->leftJoin('issues', 'articles.issue_id', '=', 'issues.id')
            ->select('articles.*')
            ->orderByDesc('issues.number')
            ->orderByDesc('issues.year')
            ->orderByDesc('issues.month')
            ->orderByDesc('articles.published_at')
            ->orderByDesc('articles.created_at');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('articles.title', 'like', "%{$search}%")
                    ->orWhere('articles.excerpt', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (!empty($filters['author_id'])) {
            $query->where('author_id', (int) $filters['author_id']);
        }

        if (array_key_exists('is_published', $filters) && $filters['is_published'] !== null) {
            $query->where('is_published', (bool) $filters['is_published']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Article
    {
        return Article::query()->with(['category', 'author', 'featureImage'])->find($id);
    }

    public function create(array $data): Article
    {
        return Article::create($data)->load(['category', 'author', 'featureImage']);
    }

    public function update(Article $article, array $data): Article
    {
        $article->fill($data);
        $article->save();

        return $article->load(['category', 'author', 'featureImage']);
    }

    public function delete(Article $article): void
    {
        $article->delete();
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = Article::query()->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
