<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('slug', 180)->unique()->after('name');
        });

        DB::table('authors')
            ->orderBy('id')
            ->chunkById(100, function ($authors) {
                foreach ($authors as $author) {
                    $baseSlug = Str::slug($author->name);
                    $slug = $baseSlug ?: Str::random(8);
                    $suffix = 1;

                    while (
                        DB::table('authors')
                            ->where('slug', $slug)
                            ->where('id', '!=', $author->id)
                            ->exists()
                    ) {
                        $slug = "{$baseSlug}-{$suffix}";
                        $suffix++;
                    }

                    DB::table('authors')
                        ->where('id', $author->id)
                        ->update(['slug' => $slug]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropUnique('authors_slug_unique');
            $table->dropColumn('slug');
        });
    }
};
