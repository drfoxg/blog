<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();                          // SEO-friendly URL
            $table->string('description_meta', 150)->nullable();       // Meta description (150 символов для SEO)
            $table->text('description')->nullable();                   // Краткое описание/превью
            $table->longText('content');
            $table->string('thumbnail')->nullable();                   // Главное изображение

            $table->string('status')->default('draft');                // draft, published, archived
            $table->unsignedInteger('views_count')->default(0);        // Счётчик просмотров
            $table->boolean('is_featured')->default(false);            // Избранный пост
            $table->boolean('allow_comments')->default(true);          // Разрешены ли комментарии

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->default(1)
                  ->constrained()
                  ->restrictOnDelete();

            $table->dateTime('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();                                     // Мягкое удаление

            // Индексы
            $table->index('status');
            $table->index('published_at');
            $table->index('is_featured');

            // Составной индекс для выборки опубликованных
            $table->index(
                ['status', 'deleted_at', 'published_at'],
                'posts_feed_idx'
            );

            //! ⚠️ Антипаттерн
            //  комментарии уже ссылаются на post_id
            //  пост не должен знать про комментарии
            // $table->unsignedBigInteger('comment_id');

        });

        // MySQL-специфичные оптимизации
        if (DB::getDriverName() === 'mysql') {
            // Descending index для сортировки новых постов
            DB::statement('CREATE INDEX posts_published_desc ON posts (published_at DESC)');

            // Fulltext для поиска (InnoDB, MySQL 5.6+)
            DB::statement('ALTER TABLE posts ADD FULLTEXT INDEX posts_fulltext (title, description, content)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
