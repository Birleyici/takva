<?php

namespace App\Services;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage, $filters);
    }

    public function create(array $data): CategoryDTO
    {
        $payload = $this->preparePayload($data);
        $category = $this->categoryRepository->create($payload);

        return CategoryDTO::fromModel($category);
    }

    public function update(int $id, array $data): CategoryDTO
    {
        $category = $this->getCategoryOrFail($id);

        $payload = $this->preparePayload($data, $category);
        $category = $this->categoryRepository->update($category, $payload);

        return CategoryDTO::fromModel($category);
    }

    public function delete(int $id): void
    {
        $category = $this->getCategoryOrFail($id);
        $this->categoryRepository->delete($category);
    }

    public function find(int $id): CategoryDTO
    {
        $category = $this->getCategoryOrFail($id);

        return CategoryDTO::fromModel($category);
    }

    private function preparePayload(array $data, ?Category $existing = null): array
    {
        $name = $data['name'] ?? $existing?->name;

        if (empty($name)) {
            throw new InvalidArgumentException('Kategori adı gereklidir.');
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

        while ($this->categoryRepository->slugExists($slug, $ignoreId)) {
            $slug = "{$baseSlug}-{$attempt}";
            $attempt++;
        }

        return $slug;
    }

    private function getCategoryOrFail(int $id): Category
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new InvalidArgumentException('Kategori bulunamadı.');
        }

        return $category;
    }
}
