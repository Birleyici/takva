<?php

namespace App\DTOs;

use App\Models\VideoCategory;
use Illuminate\Support\Collection;

class VideoCategoryDTO
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $slug = null;
    public ?string $description = null;
    public bool $is_active = true;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?VideoCategory $category): self
    {
        if (!$category) {
            return new self();
        }

        return self::fromArray($category->toArray());
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->slug = $data['slug'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->is_active = (bool) ($data['is_active'] ?? true);
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        return $dto;
    }

    /**
     * @param  Collection<int, VideoCategory|array<string, mixed>>  $categories
     * @return array<int, VideoCategoryDTO>
     */
    public static function collection(Collection $categories): array
    {
        return $categories->map(function ($category) {
            if ($category instanceof VideoCategory) {
                return self::fromModel($category);
            }

            return self::fromArray($category);
        })->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
