<?php

namespace OptimusCMS\Posts;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = [
        'body', 'author_name', 'author_email', 'author_website',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
