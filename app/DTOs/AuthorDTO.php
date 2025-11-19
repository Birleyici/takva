<?php

namespace App\DTOs;

use App\Models\Author;
use Illuminate\Support\Collection;

class AuthorDTO
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $slug = null;
    public ?MediaAssetDTO $profile_image = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?Author $author): self
    {
        if (!$author) {
            return new self();
        }

        $author->loadMissing('profileImage');

        return self::fromArray(array_merge(
            $author->toArray(),
            [
                'profile_image' => $author->profileImage?->toArray(),
            ],
        ));
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->slug = $data['slug'] ?? null;
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        $profileImage = $data['profile_image'] ?? null;
        if ($profileImage instanceof MediaAssetDTO) {
            $dto->profile_image = $profileImage;
        } elseif (is_array($profileImage)) {
            $dto->profile_image = MediaAssetDTO::fromArray($profileImage);
        } else {
            $dto->profile_image = $profileImage ? MediaAssetDTO::fromModel($profileImage) : null;
        }

        return $dto;
    }

    /**
     * @param \Illuminate\Support\Collection<int, Author|array<string, mixed>> $authors
     * @return array<int, AuthorDTO>
     */
    public static function collection(Collection $authors): array
    {
        return $authors->map(function ($author) {
            if ($author instanceof Author) {
                return self::fromModel($author);
            }

            return self::fromArray($author);
        })->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'profile_image' => $this->profile_image?->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
