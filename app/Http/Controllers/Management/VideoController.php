<?php

namespace App\Http\Controllers\Management;

use App\DTOs\VideoDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreVideoRequest;
use App\Http\Requests\Management\UpdateVideoRequest;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class VideoController extends Controller
{
    public function __construct(
        private readonly VideoService $videoService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min(50, $perPage));

        $filters = [
            'search' => $request->input('search'),
            'is_active' => $request->has('is_active')
                ? $request->boolean('is_active')
                : null,
        ];

        $paginator = $this->videoService->paginate($perPage, $filters);

        $data = array_map(
            fn (VideoDTO $dto) => $dto->toArray(),
            VideoDTO::collection($paginator->getCollection())
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

    public function store(StoreVideoRequest $request): JsonResponse
    {
        $videoDTO = $this->videoService->create($request->validated());

        return response()->json([
            'message' => 'Video başarıyla oluşturuldu.',
            'data' => $videoDTO->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $videoDTO = $this->videoService->find($id);

            return response()->json([
                'data' => $videoDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateVideoRequest $request, int $id): JsonResponse
    {
        try {
            $videoDTO = $this->videoService->update($id, $request->validated());

            return response()->json([
                'message' => 'Video başarıyla güncellendi.',
                'data' => $videoDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->videoService->delete($id);

            return response()->json([
                'message' => 'Video başarıyla silindi.',
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
