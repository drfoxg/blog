<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const CONTENT_MAX = 3000;
    const AUTHOR_MAX = 1000;

    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('author', static::AUTHOR_MAX);
            $table->string('content', static::CONTENT_MAX);

            //! like — зарезервированное слово в SQL
            // $table->unsignedInteger('likes')->index();
            $table->unsignedInteger('likes_count')->index();

            $table->tinyInteger('status')->index();
            $table->unsignedBigInteger('post_id')->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
