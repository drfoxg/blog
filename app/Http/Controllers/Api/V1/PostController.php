<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Post\StoreRequest;
use App\Http\Requests\Api\V1\Post\UpdateRequest;
use App\Http\Resources\Post\PostResources;

class PostController extends Controller
{
    public function index()
    {
        return PostResources::collection(Post::all());
    }

    public function show(Post $post)
    {
        return PostResources::make($post);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $post = Post::create($data);

        return PostResources::make($post)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateRequest $request, Post $post)
    {
        $data = $request->validated();

        $post->update($data);

        return PostResources::make($post);

    }

    public function delete(Post $post)
    {
        $post->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }
}
