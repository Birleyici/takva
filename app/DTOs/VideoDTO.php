<?php

namespace App\DTOs;

use App\Models\Video;
use Illuminate\Support\Collection;

class VideoDTO
{
    public ?int $id = null;
    public ?string $title = null;
    public ?string $slug = null;
    public ?string $youtube_url = null;
    public ?string $youtube_id = null;
    public ?int $video_category_id = null;
    public ?string $description = null;
    public ?bool $is_active = null;
    public ?string $thumbnail_url = null;
    public ?string $embed_url = null;
    public ?VideoCategoryDTO $category = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?Video $video): self
    {
        if (!$video) {
            return new self();
        }

        $video->loadMissing('category');

        return self::fromArray(array_merge(
            $video->toArray(),
            [
                'category' => $video->category?->toArray(),
            ],
        ));
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->title = $data['title'] ?? null;
        $dto->slug = $data['slug'] ?? null;
        $dto->youtube_url = $data['youtube_url'] ?? null;
        $dto->youtube_id = $data['youtube_id'] ?? null;
        $dto->video_category_id = $data['video_category_id'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->is_active = $data['is_active'] ?? null;
        $dto->thumbnail_url = $data['thumbnail_url'] ?? null;
        $dto->embed_url = $data['embed_url'] ?? null;
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        $category = $data['category'] ?? null;
        if ($category instanceof VideoCategoryDTO) {
            $dto->category = $category;
        } elseif (is_array($category)) {
            $dto->category = VideoCategoryDTO::fromArray($category);
        } else {
            $dto->category = $category ? VideoCategoryDTO::fromModel($category) : null;
        }

        return $dto;
    }

    /**
     * @param  Collection<int, Video|array<string, mixed>>  $videos
     * @return array<int, VideoDTO>
     */
    public static function collection(Collection $videos): array
    {
        return $videos->map(function ($video) {
            if ($video instanceof Video) {
                return self::fromModel($video);
            }

            return self::fromArray($video);
        })->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'youtube_url' => $this->youtube_url,
            'youtube_id' => $this->youtube_id,
            'video_category_id' => $this->video_category_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'thumbnail_url' => $this->thumbnail_url,
            'embed_url' => $this->embed_url,
            'category' => $this->category?->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
