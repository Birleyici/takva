<?php

namespace App\DTOs;

use App\Models\Issue;
use Illuminate\Support\Collection;

class IssueDTO
{
    public ?int $id = null;
    public ?string $title = null;
    public ?string $slug = null;
    public ?int $year = null;
    public ?int $month = null;
    public ?string $month_name = null;
    public ?string $description = null;
    public ?MediaAssetDTO $cover_image = null;
    public ?string $pdf_url = null;
    public ?string $pdf_original_name = null;
    public ?int $pdf_size = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?Issue $issue): self
    {
        if (!$issue) {
            return new self();
        }

        $issue->loadMissing('coverImage');

        return self::fromArray(array_merge(
            $issue->toArray(),
            [
                'cover_image' => $issue->coverImage?->toArray(),
            ],
        ));
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->title = $data['title'] ?? null;
        $dto->slug = $data['slug'] ?? null;
        $dto->year = $data['year'] ?? null;
        $dto->month = $data['month'] ?? null;
        $dto->month_name = $data['month_name'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->pdf_url = $data['pdf_url'] ?? null;
        $dto->pdf_original_name = $data['pdf_original_name'] ?? null;
        $dto->pdf_size = $data['pdf_size'] ?? null;
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        $cover = $data['cover_image'] ?? null;
        if ($cover instanceof MediaAssetDTO) {
            $dto->cover_image = $cover;
        } elseif (is_array($cover)) {
            $dto->cover_image = MediaAssetDTO::fromArray($cover);
        } else {
            $dto->cover_image = $cover ? MediaAssetDTO::fromModel($cover) : null;
        }

        return $dto;
    }

    /**
     * @param  Collection<int, Issue|array<string, mixed>>  $issues
     * @return array<int, IssueDTO>
     */
    public static function collection(Collection $issues): array
    {
        return $issues->map(function ($issue) {
            if ($issue instanceof Issue) {
                return self::fromModel($issue);
            }

            return self::fromArray($issue);
        })->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'year' => $this->year,
            'month' => $this->month,
            'month_name' => $this->month_name,
            'description' => $this->description,
            'cover_image' => $this->cover_image?->toArray(),
            'pdf_url' => $this->pdf_url,
            'pdf_original_name' => $this->pdf_original_name,
            'pdf_size' => $this->pdf_size,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
