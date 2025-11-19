<?php

namespace App\Repositories;

use App\Models\MenuPage;
use App\Repositories\Interfaces\MenuPageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MenuPageRepository implements MenuPageRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = MenuPage::query()->orderBy('position')->orderBy('title');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== null) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?MenuPage
    {
        return MenuPage::query()->find($id);
    }

    public function findBySlug(string $slug): ?MenuPage
    {
        return MenuPage::query()->where('slug', $slug)->first();
    }

    public function create(array $data): MenuPage
    {
        return MenuPage::create($data);
    }

    public function update(MenuPage $page, array $data): MenuPage
    {
        $page->fill($data);
        $page->save();

        return $page;
    }

    public function delete(MenuPage $page): void
    {
        $page->delete();
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = MenuPage::query()->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
