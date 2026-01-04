<?php

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
        if (!Schema::hasColumn('issues', 'number')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->unsignedSmallInteger('number')->nullable()->after('slug');
            });
        }

        $this->backfillIssueNumbers();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('issues', 'number')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropColumn('number');
            });
        }
    }

    private function backfillIssueNumbers(): void
    {
        if (!Schema::hasColumn('issues', 'number')) {
            return;
        }

        DB::table('issues')
            ->select(['id', 'slug'])
            ->whereNull('number')
            ->orderBy('id')
            ->chunkById(200, function ($issues) {
                foreach ($issues as $issue) {
                    $number = $this->extractNumberFromSlug($issue->slug ?? '');
                    if ($number === null) {
                        continue;
                    }

                    DB::table('issues')
                        ->where('id', $issue->id)
                        ->update(['number' => $number]);
                }
            });
    }

    private function extractNumberFromSlug(string $slug): ?int
    {
        if ($slug === '') {
            return null;
        }

        if (preg_match('/\\bsayi[-_ ]*0*(\\d{1,4})\\b/i', $slug, $matches)) {
            $number = (int) $matches[1];
            return $number > 0 ? $number : null;
        }

        return null;
    }
};
