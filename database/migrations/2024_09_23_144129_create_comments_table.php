<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public const CONTENT_MAX = 3000;
    public const AUTHOR_MAX = 255;

    /**
     * Статусы комментариев.
     */
    public const STATUS_PENDING = 0;    // На модерации
    public const STATUS_APPROVED = 1;   // Одобрен
    public const STATUS_SPAM = 2;       // Спам
    public const STATUS_TRASH = 3;      // В корзине

    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // Автор - может быть гость или зарегистрированный пользователь
            $table->string('author', static::AUTHOR_MAX);
            $table->string('email')->nullable();                       // Email автора (для гостей)

            $table->foreignId('user_id')                               // Если комментирует залогиненный пользователь
                ->nullable()
                ->constrained()
                ->cascadeOnDelete()
            ;

            $table->text('content');                                   // text вместо string для длинных комментариев
            $table->unsignedInteger('likes_count')->default(0);
            $table->tinyInteger('status')->default(static::STATUS_PENDING);

            // Внешние ключи с правильными constraints
            $table->foreignId('post_id')
                ->constrained()
                ->cascadeOnDelete()                                 // Удаляем комментарии при удалении поста
            ;

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->cascadeOnDelete()                                 // Удаляем ответы при удалении родительского комментария
            ;

            $table->string('ip_address', 45)->nullable();              // IP адрес для модерации
            $table->text('user_agent')->nullable();                    // User agent для модерации

            $table->timestamps();

            // Индексы
            $table->index('status');
            $table->index('created_at');
            $table->index(['post_id', 'status']);                      // Составной индекс

            // ! like — зарезервированное слово в SQL
            // $table->unsignedInteger('likes')->index();
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
