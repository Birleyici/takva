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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->string('slug', 220)->unique();
            $table->string('excerpt', 255)->nullable();
            $table->longText('content')->nullable();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('authors')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('feature_image_id')
                ->nullable()
                ->constrained('media_assets')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
