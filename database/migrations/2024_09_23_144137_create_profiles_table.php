<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const DESCRIPTION_MAX = 1000;
    const AVATAR_MAX = 1000;

    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('login')->unique();
            $table->string('avatar', static::AVATAR_MAX);
            $table->string('description', static::DESCRIPTION_MAX);
            $table->text('address');
            $table->string('phone');
            $table->unsignedSmallInteger('gender');
            $table->dateTime('bithdate_at');
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('parent_id')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
