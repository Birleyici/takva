<?php

namespace App\DTOs;

use App\Models\HeroSlide;
use Illuminate\Support\Collection;

class HeroSlideDTO
{
    public ?int $id = null;
    public ?MediaAssetDTO $image = null;
    public ?string $image_url = null;
    public ?string $display_date = null;
    public ?string $display_date_label = null;
    public ?string $link_url = null;
    public ?int $sort_order = null;
    public ?bool $is_active = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?HeroSlide $slide): self
    {
        if (!$slide) {
            return new self();
        }

        $slide->loadMissing('image');

        return self::fromArray(array_merge(
            $slide->toArray(),
            [
                'image' => $slide->image?->toArray(),
            ],
        ));
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->image_url = $data['image_url'] ?? null;
        $dto->display_date = $data['display_date'] ?? null;
        $dto->display_date_label = $data['display_date_label'] ?? null;
        $dto->link_url = $data['link_url'] ?? null;
        $dto->sort_order = $data['sort_order'] ?? null;
        $dto->is_active = array_key_exists('is_active', $data) ? (bool) $data['is_active'] : null;
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        $image = $data['image'] ?? null;
        if ($image instanceof MediaAssetDTO) {
            $dto->image = $image;
        } elseif (is_array($image)) {
            $dto->image = MediaAssetDTO::fromArray($image);
        } else {
            $dto->image = $image ? MediaAssetDTO::fromModel($image) : null;
        }

        return $dto;
    }

    /**
     * @param  Collection<int, HeroSlide|array<string, mixed>>  $slides
     * @return array<int, HeroSlideDTO>
     */
    public static function collection(Collection $slides): array
    {
        return $slides->map(function ($slide) {
            if ($slide instanceof HeroSlide) {
                return self::fromModel($slide);
            }

            return self::fromArray($slide);
        })->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image?->toArray(),
            'image_url' => $this->image_url,
            'display_date' => $this->display_date,
            'display_date_label' => $this->display_date_label,
            'link_url' => $this->link_url,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
