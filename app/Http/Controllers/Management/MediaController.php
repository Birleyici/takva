<?php

namespace App\Http\Controllers\Management;

use App\DTOs\MediaAssetDTO;
use App\Http\Controllers\Controller;
use App\Services\MediaAssetService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaAssetService $mediaService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 24);
        $perPage = max(6, min(60, $perPage));

        $paginator = $this->mediaService->paginate($perPage);

        $data = array_map(
            fn (MediaAssetDTO $dto) => $dto->toArray(),
            MediaAssetDTO::collection($paginator->getCollection()),
        );

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ]);

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $validated['file'];
        $mediaDTO = $this->mediaService->upload($file);

        return response()->json([
            'message' => 'Medya başarıyla yüklendi.',
            'data' => $mediaDTO->toArray(),
        ], 201);
    }

    public function destroy(int $mediaId): JsonResponse
    {
        try {
            $this->mediaService->delete($mediaId);

            return response()->json([
                'message' => 'Medya kaydı silindi.',
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
