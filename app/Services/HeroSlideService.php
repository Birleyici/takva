<?php

namespace App\Services;

use App\DTOs\HeroSlideDTO;
use App\Models\HeroSlide;
use App\Models\MediaAsset;
use App\Repositories\Interfaces\HeroSlideRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class HeroSlideService
{
    private const REQUIRED_RATIO = 16 / 9;
    private const RATIO_TOLERANCE = 0.02;

    public function __construct(
        private readonly HeroSlideRepositoryInterface $heroSlideRepository,
    ) {
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->heroSlideRepository->paginate($perPage, $filters);
    }

    public function create(array $data): HeroSlideDTO
    {
        $payload = $this->preparePayload($data);
        $slide = $this->heroSlideRepository->create($payload);

        return HeroSlideDTO::fromModel($slide);
    }

    public function update(int $id, array $data): HeroSlideDTO
    {
        $slide = $this->getSlideOrFail($id);

        $payload = $this->preparePayload($data, $slide);
        $slide = $this->heroSlideRepository->update($slide, $payload);

        return HeroSlideDTO::fromModel($slide);
    }

    public function delete(int $id): void
    {
        $slide = $this->getSlideOrFail($id);
        $this->heroSlideRepository->delete($slide);
    }

    public function find(int $id): HeroSlideDTO
    {
        $slide = $this->getSlideOrFail($id);

        return HeroSlideDTO::fromModel($slide);
    }

    private function preparePayload(array $data, ?HeroSlide $existing = null): array
    {
        $imageId = array_key_exists('image_id', $data)
            ? $this->normalizeId($data['image_id'])
            : $existing?->image_id;

        if (empty($imageId)) {
            throw new InvalidArgumentException('Slider gorseli gereklidir.');
        }

        $shouldValidateImage = array_key_exists('image_id', $data) || !$existing;
        if ($shouldValidateImage) {
            $this->assertImageRatio($imageId);
        }

        $payload = [
            'image_id' => $imageId,
            'display_date' => $this->normalizeDate(
                array_key_exists('display_date', $data) ? $data['display_date'] : $existing?->display_date
            ),
            'link_url' => array_key_exists('link_url', $data)
                ? $this->normalizeLinkUrl($data['link_url'])
                : ($existing?->link_url),
            'sort_order' => array_key_exists('sort_order', $data)
                ? (int) $data['sort_order']
                : ($existing?->sort_order ?? 0),
            'is_active' => array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : ($existing?->is_active ?? true),
        ];

        return $payload;
    }

    private function normalizeId($value): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        return (int) $value;
    }

    private function normalizeDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }

    private function normalizeLinkUrl($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function assertImageRatio(int $imageId): void
    {
        $media = MediaAsset::query()->find($imageId);

        if (!$media || !$media->width || !$media->height) {
            throw new InvalidArgumentException('Slider gorseli bulunamadi.');
        }

        $ratio = $media->width / $media->height;
        $delta = abs($ratio - self::REQUIRED_RATIO);

        if ($delta > (self::REQUIRED_RATIO * self::RATIO_TOLERANCE)) {
            throw new InvalidArgumentException('Slider gorseli 16:9 oraninda olmalidir.');
        }
    }

    private function getSlideOrFail(int $id): HeroSlide
    {
        $slide = $this->heroSlideRepository->findById($id);

        if (!$slide) {
            throw new InvalidArgumentException('Slider bulunamadi.');
        }

        return $slide;
    }
}
