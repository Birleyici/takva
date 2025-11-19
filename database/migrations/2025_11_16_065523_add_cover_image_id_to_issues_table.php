<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (!Schema::hasColumn('issues', 'cover_image_id')) {
                $table->foreignId('cover_image_id')
                    ->nullable()
                    ->after('description')
                    ->constrained('media_assets')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'cover_image_id')) {
                $table->dropConstrainedForeignId('cover_image_id');
            }
        });
    }
};
