<?php

namespace App\Services;

use App\DTOs\MenuPageDTO;
use App\Models\MenuPage;
use App\Repositories\Interfaces\MenuPageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MenuPageService
{
    public function __construct(
        private readonly MenuPageRepositoryInterface $menuPageRepository,
    ) {
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->menuPageRepository->paginate($perPage, $filters);
    }

    public function create(array $data): MenuPageDTO
    {
        $payload = $this->preparePayload($data);
        $page = $this->menuPageRepository->create($payload);

        return MenuPageDTO::fromModel($page);
    }

    public function update(int $id, array $data): MenuPageDTO
    {
        $page = $this->menuPageRepository->findById($id);

        if (!$page) {
            throw new InvalidArgumentException('Sayfa bulunamadı.');
        }

        $payload = $this->preparePayload($data, $page);
        $page = $this->menuPageRepository->update($page, $payload);

        return MenuPageDTO::fromModel($page);
    }

    public function delete(int $id): void
    {
        $page = $this->menuPageRepository->findById($id);

        if (!$page) {
            throw new InvalidArgumentException('Sayfa bulunamadı.');
        }

        $this->menuPageRepository->delete($page);
    }

    public function find(int $id): MenuPageDTO
    {
        $page = $this->menuPageRepository->findById($id);

        if (!$page) {
            throw new InvalidArgumentException('Sayfa bulunamadı.');
        }

        return MenuPageDTO::fromModel($page);
    }

    private function preparePayload(array $data, ?MenuPage $existing = null): array
    {
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Sayfa başlığı gereklidir.');
        }

        $payload = [
            'title' => $data['title'],
            'slug' => $this->generateUniqueSlug($data['title'], $existing?->id),
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'] ?? null,
            'position' => isset($data['position']) ? (int) $data['position'] : ($existing->position ?? 0),
            'is_active' => array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : ($existing->is_active ?? true),
        ];

        if (!empty($data['slug'])) {
            $payload['slug'] = $this->generateUniqueSlug($data['slug'], $existing?->id, fromInput: true);
        }

        return $payload;
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null, bool $fromInput = false): string
    {
        $base = $fromInput ? Str::slug($value) : Str::slug($value);
        $slug = $base ?: Str::random(8);
        $suffix = 1;

        while ($this->menuPageRepository->slugExists($slug, $ignoreId)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
