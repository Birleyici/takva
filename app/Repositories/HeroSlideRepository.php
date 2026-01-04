<?php

namespace App\Repositories;

use App\Models\HeroSlide;
use App\Repositories\Interfaces\HeroSlideRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HeroSlideRepository implements HeroSlideRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = HeroSlide::query()
            ->with('image')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');

        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?HeroSlide
    {
        return HeroSlide::query()
            ->with('image')
            ->find($id);
    }

    public function create(array $data): HeroSlide
    {
        return HeroSlide::create($data)->load('image');
    }

    public function update(HeroSlide $slide, array $data): HeroSlide
    {
        $slide->fill($data);
        $slide->save();

        return $slide->load('image');
    }

    public function delete(HeroSlide $slide): void
    {
        $slide->delete();
    }
}
