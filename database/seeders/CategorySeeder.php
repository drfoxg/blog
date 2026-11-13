<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Без категории',
                'slug' => 'uncategorized',
                'description' => 'Посты без определённой категории',
                'is_active' => true,
                'parent_id' => null,
                'sort_order' => 0,
            ]
        );
    }
}
