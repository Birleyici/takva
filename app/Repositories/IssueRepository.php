<?php

namespace App\Repositories;

use App\Models\Issue;
use App\Repositories\Interfaces\IssueRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IssueRepository implements IssueRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Issue::query()
            ->with('coverImage')
            ->orderByDesc('year')
            ->orderByDesc('month');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['year'])) {
            $query->where('year', (int) $filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->where('month', (int) $filters['month']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Issue
    {
        return Issue::query()->with('coverImage')->find($id);
    }

    public function findBySlug(string $slug): ?Issue
    {
        return Issue::query()->with('coverImage')->where('slug', $slug)->first();
    }

    public function create(array $data): Issue
    {
        return Issue::create($data)->load('coverImage');
    }

    public function update(Issue $issue, array $data): Issue
    {
        $issue->fill($data);
        $issue->save();

        return $issue->load('coverImage');
    }

    public function delete(Issue $issue): void
    {
        $issue->delete();
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = Issue::query()->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
