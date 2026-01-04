<?php

namespace App\Repositories\Interfaces;

use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VideoRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?Video;

    public function findBySlug(string $slug): ?Video;

    public function create(array $data): Video;

    public function update(Video $video, array $data): Video;

    public function delete(Video $video): void;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;
}
