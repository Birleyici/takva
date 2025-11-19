<?php

namespace App\Http\Controllers\Management;

use App\DTOs\IssueDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreIssueRequest;
use App\Http\Requests\Management\UpdateIssueRequest;
use App\Services\IssueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class IssueController extends Controller
{
    public function __construct(
        private readonly IssueService $issueService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min(50, $perPage));
        $filters = [
            'search' => $request->input('search'),
            'year' => $request->input('year'),
            'month' => $request->input('month'),
        ];

        $paginator = $this->issueService->paginate($perPage, $filters);

        $data = array_map(
            fn (IssueDTO $dto) => $dto->toArray(),
            IssueDTO::collection($paginator->getCollection()),
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

    public function store(StoreIssueRequest $request): JsonResponse
    {
        $issueDTO = $this->issueService->create($request->validated());

        return response()->json([
            'message' => 'Sayı başarıyla oluşturuldu.',
            'data' => $issueDTO->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $issueDTO = $this->issueService->find($id);

            return response()->json([
                'data' => $issueDTO->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateIssueRequest $request, int $id): JsonResponse
    {
        try {
            $issueDTO = $this->issueService->update($id, $request->validated());

            return response()->json([
                'message' => 'Sayı başarıyla güncellendi.',
                'data' => $issueDTO->toArray(),
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
            $this->issueService->delete($id);

            return response()->json([
                'message' => 'Sayı başarıyla silindi.',
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
