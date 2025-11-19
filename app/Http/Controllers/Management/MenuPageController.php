<?php

namespace App\Http\Controllers\Management;

use App\DTOs\MenuPageDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreMenuPageRequest;
use App\Http\Requests\Management\UpdateMenuPageRequest;
use App\Services\MenuPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class MenuPageController extends Controller
{
    public function __construct(
        private readonly MenuPageService $menuPageService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min(50, $perPage));
        $filters = [
            'search' => $request->input('search'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : null,
        ];

        $paginator = $this->menuPageService->paginate($perPage, $filters);

        $data = array_map(
            fn (MenuPageDTO $dto) => $dto->toArray(),
            MenuPageDTO::collection($paginator->getCollection()),
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

    public function store(StoreMenuPageRequest $request): JsonResponse
    {
        $pageDTO = $this->menuPageService->create($request->validated());

        return response()->json([
            'message' => 'Sayfa başarıyla oluşturuldu.',
            'data' => $pageDTO->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $pageDTO = $this->menuPageService->find($id);

            return response()->json([
                'data' => $pageDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateMenuPageRequest $request, int $id): JsonResponse
    {
        try {
            $pageDTO = $this->menuPageService->update($id, $request->validated());

            return response()->json([
                'message' => 'Sayfa başarıyla güncellendi.',
                'data' => $pageDTO->toArray(),
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
            $this->menuPageService->delete($id);

            return response()->json([
                'message' => 'Sayfa başarıyla silindi.',
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
