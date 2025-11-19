<?php

namespace App\Http\Controllers\Management;

use App\DTOs\ArticleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreArticleRequest;
use App\Http\Requests\Management\UpdateArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min(50, $perPage));
        $filters = [
            'search' => $request->input('search'),
            'category_id' => $request->input('category_id'),
            'author_id' => $request->input('author_id'),
            'is_published' => $request->has('is_published')
                ? $request->boolean('is_published')
                : null,
        ];

        $paginator = $this->articleService->paginate($perPage, $filters);

        $data = array_map(
            fn (ArticleDTO $dto) => $dto->toArray(),
            ArticleDTO::collection($paginator->getCollection()),
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

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $articleDTO = $this->articleService->create($request->validated());

        return response()->json([
            'message' => 'Makale başarıyla oluşturuldu.',
            'data' => $articleDTO->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $articleDTO = $this->articleService->find($id);

            return response()->json([
                'data' => $articleDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        try {
            $articleDTO = $this->articleService->update($id, $request->validated());

            return response()->json([
                'message' => 'Makale başarıyla güncellendi.',
                'data' => $articleDTO->toArray(),
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
            $this->articleService->delete($id);

            return response()->json([
                'message' => 'Makale başarıyla silindi.',
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
