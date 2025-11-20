<?php

namespace App\Repositories;

use App\Models\ContactMessage;
use App\Repositories\Interfaces\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactMessageRepository implements ContactMessageRepositoryInterface
{
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = ContactMessage::query()->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'read') {
                $query->where('is_read', true);
            } elseif ($filters['status'] === 'unread') {
                $query->where('is_read', false);
            }
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?ContactMessage
    {
        return ContactMessage::query()->find($id);
    }

    public function create(array $data): ContactMessage
    {
        return ContactMessage::query()->create($data);
    }

    public function update(ContactMessage $message, array $data): ContactMessage
    {
        $message->fill($data);
        $message->save();

        return $message->refresh();
    }

    public function delete(ContactMessage $message): void
    {
        $message->delete();
    }
}
