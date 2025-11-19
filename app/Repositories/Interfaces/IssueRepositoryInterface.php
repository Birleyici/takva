<?php

namespace App\Repositories\Interfaces;

use App\Models\Issue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IssueRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?Issue;

    public function findBySlug(string $slug): ?Issue;

    public function create(array $data): Issue;

    public function update(Issue $issue, array $data): Issue;

    public function delete(Issue $issue): void;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;
}
