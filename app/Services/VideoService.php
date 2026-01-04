<?php

namespace App\Services;

use App\DTOs\VideoDTO;
use App\Models\Video;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class VideoService
{
    public function __construct(
        private readonly VideoRepositoryInterface $videoRepository,
    ) {
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->videoRepository->paginate($perPage, $filters);
    }

    public function create(array $data): VideoDTO
    {
        $payload = $this->preparePayload($data);
        $video = $this->videoRepository->create($payload);

        return VideoDTO::fromModel($video);
    }

    public function update(int $id, array $data): VideoDTO
    {
        $video = $this->videoRepository->findById($id);

        if (!$video) {
            throw new InvalidArgumentException('Video bulunamadı.');
        }

        $payload = $this->preparePayload($data, $video);
        $video = $this->videoRepository->update($video, $payload);

        return VideoDTO::fromModel($video);
    }

    public function delete(int $id): void
    {
        $video = $this->videoRepository->findById($id);

        if (!$video) {
            throw new InvalidArgumentException('Video bulunamadı.');
        }

        $this->videoRepository->delete($video);
    }

    public function find(int $id): VideoDTO
    {
        $video = $this->videoRepository->findById($id);

        if (!$video) {
            throw new InvalidArgumentException('Video bulunamadı.');
        }

        return VideoDTO::fromModel($video);
    }

    private function preparePayload(array $data, ?Video $existing = null): array
    {
        $title = trim((string) ($data['title'] ?? $existing?->title ?? ''));

        if ($title === '') {
            throw new InvalidArgumentException('Video başlığı gereklidir.');
        }

        $youtubeUrl = (string) ($data['youtube_url'] ?? $existing?->youtube_url ?? '');
        $youtubeId = $this->extractYoutubeId($youtubeUrl);

        if (!$youtubeId) {
            throw ValidationException::withMessages([
                'youtube_url' => ['Geçerli bir YouTube bağlantısı giriniz.'],
            ]);
        }

        $videoCategoryId = array_key_exists('video_category_id', $data)
            ? $data['video_category_id']
            : ($existing?->video_category_id ?? null);

        if ($videoCategoryId === '' || $videoCategoryId === null) {
            $videoCategoryId = null;
        }

        return [
            'title' => $title,
            'slug' => $this->generateUniqueSlug($title, $existing?->id),
            'youtube_url' => $youtubeUrl,
            'youtube_id' => $youtubeId,
            'video_category_id' => $videoCategoryId,
            'description' => array_key_exists('description', $data)
                ? $data['description']
                : ($existing?->description ?? null),
            'is_active' => array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : ($existing?->is_active ?? true),
        ];
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: Str::random(8);
        $slug = $baseSlug;
        $suffix = 1;

        while ($this->videoRepository->slugExists($slug, $ignoreId)) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function extractYoutubeId(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $value)) {
            return $value;
        }

        $patterns = [
            '/youtu\\.be\\/([A-Za-z0-9_-]{11})/i',
            '/youtube\\.com\\/watch\\?v=([A-Za-z0-9_-]{11})/i',
            '/youtube\\.com\\/embed\\/([A-Za-z0-9_-]{11})/i',
            '/youtube\\.com\\/shorts\\/([A-Za-z0-9_-]{11})/i',
            '/youtube\\.com\\/live\\/([A-Za-z0-9_-]{11})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value, $matches)) {
                return $matches[1];
            }
        }

        if (preg_match('/[?&]v=([A-Za-z0-9_-]{11})/i', $value, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
