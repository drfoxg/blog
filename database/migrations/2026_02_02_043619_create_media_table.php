<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Таблица для хранения медиа-файлов (изображений) постов
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->string('filename');                                // Оригинальное имя файла
            $table->string('path');                                    // Путь к файлу
            $table->string('disk')->default('public');                 // Диск хранения
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');                        // Размер в байтах
            $table->unsignedInteger('width')->nullable();              // Ширина (для изображений)
            $table->unsignedInteger('height')->nullable();             // Высота (для изображений)

            $table->string('alt')->nullable();                         // Alt текст
            $table->string('title')->nullable();                       // Заголовок

            // Полиморфная связь - можно привязать к любой модели
            $table->morphs('mediable');                                // mediable_type, mediable_id

            $table->string('collection')->default('default');          // Коллекция (images, documents, etc.)
            $table->unsignedInteger('sort_order')->default(0);

            $table->foreignId('user_id')                               // Кто загрузил
                ->nullable()
                ->constrained()
                ->nullOnDelete()
            ;

            $table->timestamps();

            $table->index('collection');
            $table->index(['mediable_type', 'mediable_id', 'collection']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
