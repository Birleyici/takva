<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (!Schema::hasColumn('issues', 'read_count')) {
                $table->unsignedBigInteger('read_count')->default(0)->after('pdf_size');
            }

            if (!Schema::hasColumn('issues', 'download_count')) {
                $table->unsignedBigInteger('download_count')->default(0)->after('read_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'download_count')) {
                $table->dropColumn('download_count');
            }

            if (Schema::hasColumn('issues', 'read_count')) {
                $table->dropColumn('read_count');
            }
        });
    }
};
