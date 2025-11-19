<?php

namespace App\Services;

use App\DTOs\IssueDTO;
use App\Models\Issue;
use App\Repositories\Interfaces\IssueRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class IssueService
{
    public function __construct(
        private readonly IssueRepositoryInterface $issueRepository,
    ) {
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->issueRepository->paginate($perPage, $filters);
    }

    public function create(array $data): IssueDTO
    {
        $payload = $this->preparePayload($data);
        $issue = $this->issueRepository->create($payload);

        return IssueDTO::fromModel($issue);
    }

    public function update(int $id, array $data): IssueDTO
    {
        $issue = $this->issueRepository->findById($id);

        if (!$issue) {
            throw new InvalidArgumentException('Sayı bulunamadı.');
        }

        $payload = $this->preparePayload($data, $issue);
        $issue = $this->issueRepository->update($issue, $payload);

        return IssueDTO::fromModel($issue);
    }

    public function delete(int $id): void
    {
        $issue = $this->issueRepository->findById($id);

        if (!$issue) {
            throw new InvalidArgumentException('Sayı bulunamadı.');
        }

        $this->deletePdfIfExists($issue->pdf_path);
        $this->issueRepository->delete($issue);
    }

    public function find(int $id): IssueDTO
    {
        $issue = $this->issueRepository->findById($id);

        if (!$issue) {
            throw new InvalidArgumentException('Sayı bulunamadı.');
        }

        return IssueDTO::fromModel($issue);
    }

    private function preparePayload(array $data, ?Issue $existing = null): array
    {
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Sayı başlığı gereklidir.');
        }

        if (empty($data['year'])) {
            throw new InvalidArgumentException('Yıl bilgisi gereklidir.');
        }

        if (empty($data['month'])) {
            throw new InvalidArgumentException('Ay bilgisi gereklidir.');
        }

        $payload = [
            'title' => $data['title'],
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
            'description' => $data['description'] ?? null,
            'slug' => $this->generateUniqueSlug($data['title'], $existing?->id),
        ];

        if (!empty($data['remove_cover_image'])) {
            $payload['cover_image_id'] = null;
        } elseif (array_key_exists('cover_image_id', $data)) {
            $payload['cover_image_id'] = $this->normalizeCoverId($data['cover_image_id']);
        }

        if (!empty($data['remove_pdf']) && $existing?->pdf_path) {
            $this->deletePdfIfExists($existing->pdf_path);
            $payload['pdf_path'] = null;
            $payload['pdf_original_name'] = null;
            $payload['pdf_size'] = null;
        }

        if (!empty($data['pdf']) && $data['pdf'] instanceof UploadedFile) {
            if ($existing?->pdf_path) {
                $this->deletePdfIfExists($existing->pdf_path);
            }

            $payload = array_merge($payload, $this->storePdf($data['pdf']));
        }

        return $payload;
    }

    private function storePdf(UploadedFile $file): array
    {
        $path = $file->store('issues', 'public');

        return [
            'pdf_path' => $path,
            'pdf_original_name' => $file->getClientOriginalName(),
            'pdf_size' => $file->getSize(),
        ];
    }

    private function deletePdfIfExists(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: Str::random(8);
        $slug = $baseSlug;
        $suffix = 1;

        while ($this->issueRepository->slugExists($slug, $ignoreId)) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function normalizeCoverId($value): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        return (int) $value;
    }
}
