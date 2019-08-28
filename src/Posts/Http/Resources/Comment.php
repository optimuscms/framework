<?php

namespace OptimusCMS\Posts\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Comment extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'author_name' => $this->author_name,
            'author_email' => $this->author_email,
            'author_website' => $this->author_website,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at
        ];
    }
}
