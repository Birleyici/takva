<?php

namespace App\Repositories\Interfaces;

use App\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactMessageRepositoryInterface
{
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    public function find(int $id): ?ContactMessage;

    public function create(array $data): ContactMessage;

    public function update(ContactMessage $message, array $data): ContactMessage;

    public function delete(ContactMessage $message): void;
}
