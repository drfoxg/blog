<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>  $this->id,
            'title' =>  $this->title,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'description_meta' => $this->description_meta,
            'description' => $this->description,
            'published_at' => $this->published_at,
        ];
    }
}
