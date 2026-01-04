<?php

use App\Models\Issue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            DB::table('articles')
                ->orderBy('id')
                ->chunkById(200, function ($articles) {
                    foreach ($articles as $article) {
                        $excerpt = $article->excerpt ?? '';
                        if (!preg_match('/(\\d{1,3})\\s*\\.?\\s*Sayı/i', $excerpt, $matches)) {
                            continue;
                        }

                        $numberRaw = $matches[1];
                        $number = (int) ltrim($numberRaw, '0');
                        if ($number === 0) {
                            continue;
                        }

                        $issue = $this->findIssueByNumber($number, $numberRaw);
                        if (!$issue) {
                            continue;
                        }

                        if ((int) $article->issue_id !== (int) $issue->id) {
                            DB::table('articles')
                                ->where('id', $article->id)
                                ->update(['issue_id' => $issue->id]);
                        }
                    }
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left empty; no reliable rollback for data fix.
    }

    private function findIssueByNumber(int $number, string $numberRaw): ?Issue
    {
        $padded = str_pad((string) $number, 2, '0', STR_PAD_LEFT);
        $rawTrimmed = ltrim($numberRaw, '0');
        $rawTrimmed = $rawTrimmed === '' ? '0' : $rawTrimmed;

        $patterns = [];
        foreach ([$number, $padded, $rawTrimmed] as $candidate) {
            $patterns[] = "{$candidate}.%";
            $patterns[] = "{$candidate} %";
            $patterns[] = "{$candidate}-%";
            $patterns[] = "{$candidate}%sayı%";
        }

        $query = Issue::query();
        $query->where(function ($q) use ($patterns) {
            foreach ($patterns as $pattern) {
                $q->orWhere('title', 'like', $pattern)
                    ->orWhere('slug', 'like', $pattern);
            }
        });

        return $query
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->first();
    }
};
