<?php

use App\Models\Issue;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('issue_id')
                ->nullable()
                ->after('category_id')
                ->constrained('issues')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        $this->backfillIssueIds();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('issue_id');
        });
    }

    private function backfillIssueIds(): void
    {
        DB::table('articles')
            ->whereNull('issue_id')
            ->orderBy('id')
            ->chunkById(200, function ($articles) {
                foreach ($articles as $article) {
                    $excerpt = $article->excerpt ?? '';
                    if (!preg_match('/^(\\d{1,2})\\s*\\.?\\s*Sayı/i', $excerpt, $matches)) {
                        continue;
                    }

                    $month = (int) $matches[1];
                    $year = null;
                    if (!empty($article->published_at)) {
                        $year = Carbon::parse($article->published_at)->year;
                    }

                    $issueQuery = Issue::query()->where('month', $month);
                    if ($year) {
                        $issueQuery->where('year', $year);
                    }

                    $issue = $issueQuery
                        ->orderByDesc('year')
                        ->orderByDesc('month')
                        ->first();

                    if ($issue) {
                        DB::table('articles')
                            ->where('id', $article->id)
                            ->update(['issue_id' => $issue->id]);
                    }
                }
            });
    }
};
