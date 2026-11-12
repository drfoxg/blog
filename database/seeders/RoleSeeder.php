<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'title' => 'Администратор',
                'slug' => 'admin',
                'description' => 'Полный доступ к системе',
                'permissions' => json_encode(['*']),
                'is_default' => false,
            ],
            [
                'title' => 'Модератор',
                'slug' => 'moderator',
                'description' => 'Модерация контента',
                'permissions' => json_encode([
                    'posts.view', 'posts.edit',
                    'comments.view', 'comments.edit', 'comments.delete',
                ]),
                'is_default' => false,
            ],
            [
                'title' => 'Автор',
                'slug' => 'author',
                'description' => 'Создание и редактирование своих постов',
                'permissions' => json_encode([
                    'posts.view', 'posts.create', 'posts.edit-own',
                ]),
                'is_default' => false,
            ],
            [
                'title' => 'Пользователь',
                'slug' => 'user',
                'description' => 'Базовые права пользователя',
                'permissions' => json_encode([
                    'posts.view',
                    'comments.create',
                ]),
                'is_default' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
