<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public const DESCRIPTION_MAX = 1000;
    public const AVATAR_MAX = 500;

    /**
     * Константы для gender.
     */
    public const GENDER_UNKNOWN = 0;
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;

    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            $table->string('login')->unique();
            $table->string('avatar', static::AVATAR_MAX)->nullable();
            $table->string('description', static::DESCRIPTION_MAX)->nullable();

            // Контактная информация
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();                   // Ограничение длины для телефона
            $table->string('website')->nullable();                     // Личный сайт

            // Социальные сети
            $table->json('social_links')->nullable();                  // {"telegram": "...", "github": "..."}

            // Персональные данные
            $table->unsignedTinyInteger('gender')->default(static::GENDER_UNKNOWN);
            $table->date('birthdate_at')->nullable();                  // date вместо dateTime

            // Настройки
            $table->boolean('is_public')->default(true);               // Публичный профиль
            $table->string('timezone')->default('UTC');
            $table->string('locale', 10)->default('ru');

            // Связь с пользователем (One-to-One)
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete()
            ;

            // Реферальная система
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('profiles')
                ->nullOnDelete()
            ;

            $table->timestamps();

            $table->index('is_public');
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
