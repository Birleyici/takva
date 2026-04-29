<?php

namespace App\Http\Controllers\Management;

use App\DTOs\CategoryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreCategoryRequest;
use App\Http\Requests\Management\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min(200, $perPage));
        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : null;

        $paginator = $this->categoryService->paginate($perPage, [
            'search' => $search ?: null,
            'is_active' => $isActive,
        ]);

        $data = array_map(
            fn (CategoryDTO $dto) => $dto->toArray(),
            CategoryDTO::collection($paginator->getCollection())
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

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $categoryDTO = $this->categoryService->create($request->validated());

        return response()->json([
            'message' => 'Kategori başarıyla oluşturuldu.',
            'data' => $categoryDTO->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $categoryDTO = $this->categoryService->find($id);

            return response()->json([
                'data' => $categoryDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        try {
            $categoryDTO = $this->categoryService->update($id, $request->validated());

            return response()->json([
                'message' => 'Kategori başarıyla güncellendi.',
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
            $this->categoryService->delete($id);

            return response()->json([
                'message' => 'Kategori başarıyla silindi.',
            ], 200);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
