<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use Carbon\Carbon;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function store(Request $request)
    {
        $data = [
            'title' => 'New category title',
            'slug' => 'new-category-title-' . random_int(1, 1000),
            'is_active' => true,
        ];

        $category = Category::create($data);

        return $category;
    }

    public function update(Category $category)
    {
        $category->update(['title' => 'New category title '. Carbon::now()]);

        return $category;
    }

    public function delete(Category $category)
    {
        $category->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }
}
