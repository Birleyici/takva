<?php

namespace App\DTOs;

use App\Models\ContactMessage;
use Illuminate\Support\Collection;

class ContactMessageDTO
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $subject = null;
    public ?string $message = null;
    public ?string $ip_address = null;
    public ?string $user_agent = null;
    public bool $is_read = false;
    public ?string $read_at = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?ContactMessage $message): self
    {
        if (!$message) {
            return new self();
        }

        return self::fromArray($message->toArray());
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->email = $data['email'] ?? null;
        $dto->subject = $data['subject'] ?? null;
        $dto->message = $data['message'] ?? null;
        $dto->ip_address = $data['ip_address'] ?? null;
        $dto->user_agent = $data['user_agent'] ?? null;
        $dto->is_read = (bool) ($data['is_read'] ?? false);
        $dto->read_at = $data['read_at'] ?? null;
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        return $dto;
    }

    /**
     * @param \Illuminate\Support\Collection<int, ContactMessage>|\Illuminate\Support\Collection<int, array<string, mixed>> $messages
     * @return array<int, ContactMessageDTO>
     */
    public static function collection(Collection $messages): array
    {
        return $messages->map(fn ($message) => $message instanceof ContactMessage
            ? self::fromModel($message)
            : self::fromArray($message)
        )->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
