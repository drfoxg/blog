<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use Carbon\Carbon;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function show(Post $post)
    {
        return $post;
    }

    public function store(Request $request)
    {
        $data = [
            'title' => 'Nice title',
            'content' => 'Some content',
            'description_meta' => 'Short meta description',
            'description' => 'Short description',
            'user_id' => 1,
            'category_id' => 1,
        ];

        $post = Post::create($data);

        return $post;
    }

    public function update(Post $post)
    {
        $post->update(['description' => 'New desc '. Carbon::now()]);

        return $post;
    }

    public function delete(Post $post)
    {
        $post->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }
}
