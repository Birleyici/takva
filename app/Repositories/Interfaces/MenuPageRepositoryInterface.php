<?php

namespace App\Repositories\Interfaces;

use App\Models\MenuPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MenuPageRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?MenuPage;

    public function findBySlug(string $slug): ?MenuPage;

    public function create(array $data): MenuPage;

    public function update(MenuPage $page, array $data): MenuPage;

    public function delete(MenuPage $page): void;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;
}
