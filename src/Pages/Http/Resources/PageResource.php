<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OptimusCMS\Meta\Http\Resources\MetaResource;

class PageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'path' => $this->path,
            'has_fixed_path' => $this->has_fixed_path,
            'parent_id' => $this->parent_id,
            'children_count' => $this->when(
                ! is_null($this->children_count),
                $this->children_count
            ),
            'template' => [
                'name' => $this->template_name,
                'data' => value(function () {
                    $page = $this->resource;

                    return $page->template()->toArray($page);
                }),
                'is_fixed' => $this->has_fixed_template,
            ],
            'is_standalone' => $this->is_standalone,
            'is_deletable' => $this->is_deletable,
            'is_published' => $this->isPublished(),
            'meta' => new MetaResource($this->meta),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
