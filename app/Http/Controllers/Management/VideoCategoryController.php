<?php

namespace App\Http\Controllers\Management;

use App\DTOs\VideoCategoryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreVideoCategoryRequest;
use App\Http\Requests\Management\UpdateVideoCategoryRequest;
use App\Services\VideoCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class VideoCategoryController extends Controller
{
    public function __construct(
        private readonly VideoCategoryService $videoCategoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min(50, $perPage));
        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : null;

        $paginator = $this->videoCategoryService->paginate($perPage, [
            'search' => $search ?: null,
            'is_active' => $isActive,
        ]);

        $data = array_map(
            fn (VideoCategoryDTO $dto) => $dto->toArray(),
            VideoCategoryDTO::collection($paginator->getCollection())
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

    public function store(StoreVideoCategoryRequest $request): JsonResponse
    {
        $categoryDTO = $this->videoCategoryService->create($request->validated());

        return response()->json([
            'message' => 'Video kategorisi başarıyla oluşturuldu.',
            'data' => $categoryDTO->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $categoryDTO = $this->videoCategoryService->find($id);

            return response()->json([
                'data' => $categoryDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateVideoCategoryRequest $request, int $id): JsonResponse
    {
        try {
            $categoryDTO = $this->videoCategoryService->update($id, $request->validated());

            return response()->json([
                'message' => 'Video kategorisi başarıyla güncellendi.',
                'data' => $categoryDTO->toArray(),
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
            $this->videoCategoryService->delete($id);

            return response()->json([
                'message' => 'Video kategorisi başarıyla silindi.',
            ], 200);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
