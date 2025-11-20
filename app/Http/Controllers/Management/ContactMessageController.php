<?php

namespace App\Http\Controllers\Management;

use App\DTOs\ContactMessageDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\UpdateContactMessageRequest;
use App\Services\ContactMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ContactMessageController extends Controller
{
    public function __construct(
        private readonly ContactMessageService $contactMessageService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 12);
        $perPage = max(1, min(50, $perPage));
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');
        $status = in_array($status, ['read', 'unread'], true) ? $status : null;

        $paginator = $this->contactMessageService->paginate($perPage, [
            'search' => $search ?: null,
            'status' => $status,
        ]);

        $data = array_map(
            fn (ContactMessageDTO $dto) => $dto->toArray(),
            ContactMessageDTO::collection($paginator->getCollection())
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

    public function show(int $id): JsonResponse
    {
        try {
            $message = $this->contactMessageService->find($id);

            return response()->json([
                'data' => $message->toArray(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateContactMessageRequest $request, int $id): JsonResponse
    {
        try {
            $message = $this->contactMessageService->updateStatus($id, (bool) $request->boolean('is_read'));

            return response()->json([
                'message' => 'Mesaj durumu güncellendi.',
                'data' => $message->toArray(),
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
            $this->contactMessageService->delete($id);

            return response()->json([
                'message' => 'Mesaj silindi.',
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}
