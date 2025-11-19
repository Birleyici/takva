<?php

namespace App\Http\Controllers\Management;

use App\DTOs\AuthorDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreAuthorRequest;
use App\Http\Requests\Management\UpdateAuthorRequest;
use App\Services\AuthorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class AuthorController extends Controller
{
    public function __construct(
        private readonly AuthorService $authorService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min(50, $perPage));
        $search = trim((string) $request->input('search', ''));

        $paginator = $this->authorService->paginate($perPage, [
            'search' => $search ?: null,
        ]);

        $data = array_map(
            fn (AuthorDTO $dto) => $dto->toArray(),
            AuthorDTO::collection($paginator->getCollection()),
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

    public function store(StoreAuthorRequest $request): JsonResponse
    {
        $authorDTO = $this->authorService->create($request->validated());

        return response()->json([
            'message' => 'Yazar başarıyla oluşturuldu.',
            'data' => $authorDTO->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $authorDTO = $this->authorService->find($id);

            return response()->json([
                'data' => $authorDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateAuthorRequest $request, int $id): JsonResponse
    {
        try {
            $authorDTO = $this->authorService->update($id, $request->validated());

            return response()->json([
                'message' => 'Yazar başarıyla güncellendi.',
                'data' => $authorDTO->toArray(),
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
            $this->authorService->delete($id);

            return response()->json([
                'message' => 'Yazar başarıyla silindi.',
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
