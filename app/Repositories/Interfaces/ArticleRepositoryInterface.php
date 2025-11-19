<?php

namespace App\Repositories\Interfaces;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?Article;

    public function create(array $data): Article;

    public function update(Article $article, array $data): Article;

    public function delete(Article $article): void;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;
}
