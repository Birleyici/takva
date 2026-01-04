<?php

namespace App\Http\Controllers\Management;

use App\DTOs\HeroSlideDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreHeroSlideRequest;
use App\Http\Requests\Management\UpdateHeroSlideRequest;
use App\Services\HeroSlideService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HeroSlideController extends Controller
{
    public function __construct(
        private readonly HeroSlideService $heroSlideService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min(50, $perPage));

        $filters = [
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : null,
        ];

        $paginator = $this->heroSlideService->paginate($perPage, $filters);

        $data = array_map(
            fn (HeroSlideDTO $dto) => $dto->toArray(),
            HeroSlideDTO::collection($paginator->getCollection()),
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

    public function store(StoreHeroSlideRequest $request): JsonResponse
    {
        $slideDTO = $this->heroSlideService->create($request->validated());

        return response()->json([
            'message' => 'Slider basariyla olusturuldu.',
            'data' => $slideDTO->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $slideDTO = $this->heroSlideService->find($id);

            return response()->json([
                'data' => $slideDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateHeroSlideRequest $request, int $id): JsonResponse
    {
        try {
            $slideDTO = $this->heroSlideService->update($id, $request->validated());

            return response()->json([
                'message' => 'Slider basariyla guncellendi.',
                'data' => $slideDTO->toArray(),
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
            $this->heroSlideService->delete($id);

            return response()->json([
                'message' => 'Slider basariyla silindi.',
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
