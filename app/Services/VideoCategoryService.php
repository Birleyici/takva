<?php

namespace App\Services;

use App\DTOs\VideoCategoryDTO;
use App\Models\VideoCategory;
use App\Repositories\Interfaces\VideoCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class VideoCategoryService
{
    public function __construct(
        private readonly VideoCategoryRepositoryInterface $videoCategoryRepository
    ) {
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->videoCategoryRepository->paginate($perPage, $filters);
    }

    public function create(array $data): VideoCategoryDTO
    {
        $payload = $this->preparePayload($data);
        $category = $this->videoCategoryRepository->create($payload);

        return VideoCategoryDTO::fromModel($category);
    }

    public function update(int $id, array $data): VideoCategoryDTO
    {
        $category = $this->getCategoryOrFail($id);

        $payload = $this->preparePayload($data, $category);
        $category = $this->videoCategoryRepository->update($category, $payload);

        return VideoCategoryDTO::fromModel($category);
    }

    public function delete(int $id): void
    {
        $category = $this->getCategoryOrFail($id);
        $this->videoCategoryRepository->delete($category);
    }

    public function find(int $id): VideoCategoryDTO
    {
        $category = $this->getCategoryOrFail($id);

        return VideoCategoryDTO::fromModel($category);
    }

    private function preparePayload(array $data, ?VideoCategory $existing = null): array
    {
        $name = $data['name'] ?? $existing?->name;

        if (empty($name)) {
            throw new InvalidArgumentException('Video kategori adı gereklidir.');
        }

        $payload = [
            'name' => $name,
            'description' => array_key_exists('description', $data)
                ? $data['description']
                : ($existing?->description),
            'is_active' => array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : ($existing?->is_active ?? true),
        ];

        $slug = $data['slug'] ?? $name;
        $payload['slug'] = $this->generateUniqueSlug($slug, $existing?->id);

        return $payload;
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $attempt = 1;

        while ($this->videoCategoryRepository->slugExists($slug, $ignoreId)) {
            $slug = "{$baseSlug}-{$attempt}";
            $attempt++;
        }

        return $slug;
    }

    private function getCategoryOrFail(int $id): VideoCategory
    {
        $category = $this->videoCategoryRepository->findById($id);

        if (!$category) {
            throw new InvalidArgumentException('Video kategorisi bulunamadı.');
        }

        return $category;
    }
}
