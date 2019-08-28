<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use OptimusCMS\Media\Http\Resources\MediaResource;

class PageResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'uri' => $this->uri,
            'has_fixed_uri' => $this->has_fixed_uri,
            'parent_id' => $this->parent_id,
            'template' => $this->template,
            'has_fixed_template' => $this->has_fixed_template,
            'contents' => ContentResource::collection($this->contents),
            'media' => MediaResource::collection($this->media),
            'meta' => $this->meta,
            'children_count' => $this->when(
                ! is_null($this->children_count),
                $this->children_count
            ),
            'is_stand_alone' => $this->is_stand_alone,
            'is_published' => $this->isPublished(),
            'is_deletable' => $this->is_deletable,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at
        ];
    }
}
