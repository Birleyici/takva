<?php

namespace App\DTOs;

use App\Models\MenuPage;
use Illuminate\Support\Collection;

class MenuPageDTO
{
    public ?int $id = null;
    public ?string $title = null;
    public ?string $slug = null;
    public ?string $summary = null;
    public ?string $content = null;
    public ?int $position = null;
    public ?bool $is_active = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?MenuPage $page): self
    {
        if (!$page) {
            return new self();
        }

        return self::fromArray($page->toArray());
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->title = $data['title'] ?? null;
        $dto->slug = $data['slug'] ?? null;
        $dto->summary = $data['summary'] ?? null;
        $dto->content = $data['content'] ?? null;
        $dto->position = $data['position'] ?? null;
        $dto->is_active = $data['is_active'] ?? null;
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        return $dto;
    }

    /**
     * @param Collection<int, MenuPage|array<string, mixed>> $pages
     * @return array<int, MenuPageDTO>
     */
    public static function collection(Collection $pages): array
    {
        return $pages->map(function ($page) {
            if ($page instanceof MenuPage) {
                return self::fromModel($page);
            }

            return self::fromArray($page);
        })->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'content' => $this->content,
            'position' => $this->position,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
