<?php

namespace App\Repositories\Interfaces;

use App\Models\HeroSlide;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface HeroSlideRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?HeroSlide;

    public function create(array $data): HeroSlide;

    public function update(HeroSlide $slide, array $data): HeroSlide;

    public function delete(HeroSlide $slide): void;
}
