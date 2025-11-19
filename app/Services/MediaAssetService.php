<?php

namespace App\Services;

use App\DTOs\MediaAssetDTO;
use App\Repositories\Interfaces\MediaAssetRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MediaAssetService
{
    public function __construct(
        private readonly MediaAssetRepositoryInterface $mediaRepository
    ) {
    }

    public function paginate(int $perPage = 24): LengthAwarePaginator
    {
        return $this->mediaRepository->paginate($perPage);
    }

    public function upload(UploadedFile $file, string $disk = 'public'): MediaAssetDTO
    {
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Geçersiz dosya yüklendi.');
        }

        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
        $fileName = Str::uuid()->toString().'.'.$extension;
        $filePath = "media/{$fileName}";

        Storage::disk($disk)->putFileAs('media', $file, $fileName);

        $imageSize = @getimagesize($file->getRealPath());

        $media = $this->mediaRepository->create([
            'disk' => $disk,
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'file_path' => $filePath,
            'width' => $imageSize[0] ?? null,
            'height' => $imageSize[1] ?? null,
        ]);

        return MediaAssetDTO::fromModel($media);
    }

    public function delete(int $id): void
    {
        $media = $this->mediaRepository->findById($id);

        if (!$media) {
            throw new InvalidArgumentException('Medya kaydı bulunamadı.');
        }

        if ($media->file_path) {
            Storage::disk($media->disk ?? 'public')->delete($media->file_path);
        }

        $this->mediaRepository->delete($media);
    }
}
