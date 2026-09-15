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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title')->index();
            $table->string('ISBN')->unique();
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('available_quantity')->default(1);
            $table->enum('status', ['available', 'borrowed', 'reserved'])->default('available');
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->softDeletes();
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
