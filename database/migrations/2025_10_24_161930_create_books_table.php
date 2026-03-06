<?php

use App\Enums\BookStatusEnum;
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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->integer('total_copies')->default(0);
            $table->integer('available_copies')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->string('cover_image')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->unsignedTinyInteger('status')->default(BookStatusEnum::AVAILABLE->value);
            $table->foreignId('author_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->index(['title','author_id']);
            $table->index('slug');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
