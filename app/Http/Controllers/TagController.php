<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function show(Tag $tag)
    {
        return $tag;
    }

    public function store(Request $request)
    {
        $data = [
            'title' => 'New tag'.random_int(1, 1000),
            'slug' => 'new-tag-'.random_int(1, 1000),
        ];

        return Tag::create($data);
    }

    public function update(Tag $tag)
    {
        $tag->update(['title' => 'New tag title '.Carbon::now()]);

        return $tag;
    }

    public function delete(Tag $tag)
    {
        $tag->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }
}
