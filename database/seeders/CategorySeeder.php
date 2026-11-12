<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

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
