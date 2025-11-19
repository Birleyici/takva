<?php

namespace App\Repositories\Interfaces;

use App\Models\MediaAsset;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MediaAssetRepositoryInterface
{
    public function paginate(int $perPage = 24): LengthAwarePaginator;

    public function create(array $data): MediaAsset;

    public function findById(int $id): ?MediaAsset;

    public function delete(MediaAsset $media): void;
}
