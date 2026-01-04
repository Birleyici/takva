<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $issueMap = $this->buildIssueNumberMap();

        if (!$issueMap) {
            return;
        }

        DB::table('articles')
            ->orderBy('id')
            ->chunkById(200, function ($articles) use ($issueMap) {
                foreach ($articles as $article) {
                    $excerpt = $article->excerpt ?? '';
                    $number = $this->extractNumber($excerpt);
                    if (!$number || !isset($issueMap[$number])) {
                        continue;
                    }

                    $issueId = $issueMap[$number]['id'];
                    if ((int) $article->issue_id !== (int) $issueId) {
                        DB::table('articles')
                            ->where('id', $article->id)
                            ->update(['issue_id' => $issueId]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data fix migration has no rollback.
    }

    private function buildIssueNumberMap(): array
    {
        $issues = DB::table('issues')
            ->select(['id', 'title', 'slug', 'year', 'month'])
            ->get();

        $map = [];

        foreach ($issues as $issue) {
            $number = $this->extractNumber($issue->title ?? '');
            if (!$number) {
                $number = $this->extractNumber(str_replace('-', ' ', $issue->slug ?? ''));
            }

            if (!$number) {
                continue;
            }

            if (!isset($map[$number])) {
                $map[$number] = [
                    'id' => $issue->id,
                    'year' => (int) ($issue->year ?? 0),
                    'month' => (int) ($issue->month ?? 0),
                ];
                continue;
            }

            $current = $map[$number];
            $isNewer = ($issue->year ?? 0) > $current['year']
                || (($issue->year ?? 0) === $current['year'] && ($issue->month ?? 0) > $current['month']);

            if ($isNewer) {
                $map[$number] = [
                    'id' => $issue->id,
                    'year' => (int) ($issue->year ?? 0),
                    'month' => (int) ($issue->month ?? 0),
                ];
            }
        }

        return $map;
    }

    private function extractNumber(string $value): ?int
    {
        if ($value === '') {
            return null;
        }

        if (preg_match('/(?:^|\\D)(\\d{1,3})\\s*\\.?\\s*(sayı|sayi)\\b/iu', $value, $matches)) {
            $number = (int) ltrim($matches[1], '0');
            return $number === 0 ? null : $number;
        }

        if (preg_match('/\\b(sayı|sayi)\\s*(\\d{1,3})\\b/iu', $value, $matches)) {
            $number = (int) ltrim($matches[2], '0');
            return $number === 0 ? null : $number;
        }

        return null;
    }
};
