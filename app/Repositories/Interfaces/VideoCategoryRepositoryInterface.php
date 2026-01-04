<?php

namespace App\Repositories\Interfaces;

use App\Models\VideoCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VideoCategoryRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?VideoCategory;

    public function findBySlug(string $slug): ?VideoCategory;

    public function create(array $data): VideoCategory;

    public function update(VideoCategory $category, array $data): VideoCategory;

    public function delete(VideoCategory $category): void;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;
}
