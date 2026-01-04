<?php

namespace App\Repositories;

use App\Models\VideoCategory;
use App\Repositories\Interfaces\VideoCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VideoCategoryRepository implements VideoCategoryRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = VideoCategory::query()
            ->orderByDesc('created_at');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?VideoCategory
    {
        return VideoCategory::query()->find($id);
    }

    public function findBySlug(string $slug): ?VideoCategory
    {
        return VideoCategory::query()->where('slug', $slug)->first();
    }

    public function create(array $data): VideoCategory
    {
        return VideoCategory::create($data);
    }

    public function update(VideoCategory $category, array $data): VideoCategory
    {
        $category->fill($data);
        $category->save();

        return $category;
    }

    public function delete(VideoCategory $category): void
    {
        $category->delete();
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = VideoCategory::query()->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
