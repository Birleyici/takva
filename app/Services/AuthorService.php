<?php

namespace App\Services;

use App\DTOs\AuthorDTO;
use App\Models\Author;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\MediaAssetRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AuthorService
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
        private readonly MediaAssetRepositoryInterface $mediaRepository,
    ) {
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->authorRepository->paginate($perPage, $filters);
    }

    public function create(array $data): AuthorDTO
    {
        $payload = $this->preparePayload($data);
        $author = $this->authorRepository->create($payload);

        return AuthorDTO::fromModel($author);
    }

    public function update(int $id, array $data): AuthorDTO
    {
        $author = $this->authorRepository->findById($id);

        if (!$author) {
            throw new InvalidArgumentException('Yazar bulunamadı.');
        }

        $payload = $this->preparePayload($data, $author);
        $author = $this->authorRepository->update($author, $payload);

        return AuthorDTO::fromModel($author);
    }

    public function delete(int $id): void
    {
        $author = $this->authorRepository->findById($id);

        if (!$author) {
            throw new InvalidArgumentException('Yazar bulunamadı.');
        }

        $this->authorRepository->delete($author);
    }

    public function find(int $id): AuthorDTO
    {
        $author = $this->authorRepository->findById($id);

        if (!$author) {
            throw new InvalidArgumentException('Yazar bulunamadı.');
        }

        return AuthorDTO::fromModel($author);
    }

    private function preparePayload(array $data, ?Author $author = null): array
    {
        if (empty($data['name'])) {
            throw new InvalidArgumentException('Yazar adı gereklidir.');
        }

        $payload = [
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name'], $author?->id),
        ];

        if (array_key_exists('profile_image_id', $data)) {
            $payload['profile_image_id'] = $this->resolveMediaId($data['profile_image_id'], $author?->profile_image_id);
        }

        return $payload;
    }

    private function resolveMediaId($incomingId, ?int $currentMediaId = null): ?int
    {
        if ($incomingId === null || $incomingId === '' || $incomingId === false) {
            return null;
        }

        $mediaId = (int) $incomingId;

        if ($currentMediaId !== null && $currentMediaId === $mediaId) {
            return $mediaId;
        }

        $media = $this->mediaRepository->findById($mediaId);

        if (!$media) {
            throw new InvalidArgumentException('Seçilen medya kaydı bulunamadı.');
        }

        return $media->id;
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: Str::random(8);
        $suffix = 1;

        while (
            Author::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
