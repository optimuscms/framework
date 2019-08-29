<?php

namespace OptimusCMS\Posts;

use Illuminate\Http\Request;
use Spatie\Sluggable\HasSlug;
use OptimusCMS\Media\HasMedia;
use Optix\Draftable\Draftable;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    use HasMedia, HasSlug, Draftable;

    protected $dates = ['published_at'];

    protected $fillable = [
        'title', 'body', 'published_at',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function scopeLatest(Builder $query)
    {
        $query->orderBy('published_at', 'desc');
    }

    public function scopeFilter(Builder $query, Request $request)
    {
        //
    }

    public function tags()
    {
        return $this->belongsToMany(PostTag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }
}
