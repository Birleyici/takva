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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->longText('description')->nullable();
            $table->foreignId('cover_image_id')
                ->nullable()
                ->constrained('media_assets')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('pdf_path')->nullable();
            $table->string('pdf_original_name')->nullable();
            $table->unsignedBigInteger('pdf_size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
