<?php

namespace App\Services;

use App\DTOs\ContactMessageDTO;
use App\Repositories\Interfaces\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class ContactMessageService
{
    public function __construct(
        private readonly ContactMessageRepositoryInterface $contactMessageRepository
    ) {
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        return $this->contactMessageRepository->paginate($perPage, $filters);
    }

    public function find(int $id): ContactMessageDTO
    {
        $message = $this->contactMessageRepository->find($id);

        if (!$message) {
            throw new InvalidArgumentException('Mesaj bulunamadı.');
        }

        return ContactMessageDTO::fromModel($message);
    }

    public function create(array $data): ContactMessageDTO
    {
        $payload = [
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'is_read' => false,
            'read_at' => null,
        ];

        $message = $this->contactMessageRepository->create($payload);

        return ContactMessageDTO::fromModel($message);
    }

    public function updateStatus(int $id, bool $isRead): ContactMessageDTO
    {
        $message = $this->contactMessageRepository->find($id);

        if (!$message) {
            throw new InvalidArgumentException('Mesaj bulunamadı.');
        }

        $payload = [
            'is_read' => $isRead,
            'read_at' => $isRead ? now() : null,
        ];

        $message = $this->contactMessageRepository->update($message, $payload);

        return ContactMessageDTO::fromModel($message);
    }

    public function delete(int $id): void
    {
        $message = $this->contactMessageRepository->find($id);

        if (!$message) {
            throw new InvalidArgumentException('Mesaj bulunamadı.');
        }

        $this->contactMessageRepository->delete($message);
    }
}
