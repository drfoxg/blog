<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->unique();
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();                   // Описание тега
            $table->string('color', 7)->nullable();                    // Цвет тега (HEX: #FF5733)
            $table->unsignedInteger('posts_count')->default(0);        // Кэш количества постов
            $table->timestamps();

            $table->index('posts_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
