<?php

namespace App\Repositories;

use App\Models\MediaAsset;
use App\Repositories\Interfaces\MediaAssetRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MediaAssetRepository implements MediaAssetRepositoryInterface
{
    public function paginate(int $perPage = 24): LengthAwarePaginator
    {
        return MediaAsset::query()->latest()->paginate($perPage);
    }

    public function create(array $data): MediaAsset
    {
        return MediaAsset::create($data);
    }

    public function findById(int $id): ?MediaAsset
    {
        return MediaAsset::query()->find($id);
    }

    public function delete(MediaAsset $media): void
    {
        $media->delete();
    }
}
