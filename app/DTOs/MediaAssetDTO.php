<?php

namespace App\DTOs;

use App\Models\MediaAsset;
use Illuminate\Support\Collection;

class MediaAssetDTO
{
    public ?int $id = null;
    public ?string $original_name = null;
    public ?string $file_name = null;
    public ?string $file_path = null;
    public ?string $disk = null;
    public ?string $mime_type = null;
    public ?int $size = null;
    public ?int $width = null;
    public ?int $height = null;
    public ?string $url = null;
    public ?string $created_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?MediaAsset $media): self
    {
        if (!$media) {
            return new self();
        }

        return self::fromArray($media->toArray());
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->original_name = $data['original_name'] ?? null;
        $dto->file_name = $data['file_name'] ?? null;
        $dto->file_path = $data['file_path'] ?? null;
        $dto->disk = $data['disk'] ?? null;
        $dto->mime_type = $data['mime_type'] ?? null;
        $dto->size = $data['size'] ?? null;
        $dto->width = $data['width'] ?? null;
        $dto->height = $data['height'] ?? null;
        $dto->url = $data['url'] ?? null;
        $dto->created_at = $data['created_at'] ?? null;

        return $dto;
    }

    /**
     * @param \Illuminate\Support\Collection<int, MediaAsset|\Illuminate\Database\Eloquent\Model|array<string, mixed>> $mediaCollection
     * @return array<int, MediaAssetDTO>
     */
    public static function collection(Collection $mediaCollection): array
    {
        return $mediaCollection
            ->map(function ($item) {
                if ($item instanceof MediaAsset) {
                    return self::fromModel($item);
                }

                return self::fromArray($item);
            })
            ->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'original_name' => $this->original_name,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'disk' => $this->disk,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'url' => $this->url,
            'created_at' => $this->created_at,
        ];
    }
}
