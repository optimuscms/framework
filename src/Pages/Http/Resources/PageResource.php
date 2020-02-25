<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OptimusCMS\Meta\Http\Resources\MetaResource;

class PageResource extends JsonResource
{
    public function toArray($request)
    {
        $page = $this->resource;

        $template = $page->templateHandler();

        return [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'path' => $page->path,
            'has_fixed_path' => $page->has_fixed_path,
            'parent_id' => $page->parent_id,
            'children_count' => $this->when(
                ! is_null($page->children_count),
                $page->children_count
            ),
            'template_id' => $template::getId(),
            'template_name' => $template::getName(),
            'template_data' => $template->getData($page),
            'has_fixed_template' => $page->has_fixed_template,
            'is_standalone' => $page->is_standalone,
            'is_deletable' => $page->is_deletable,
            'is_published' => $page->isPublished(),
            'meta' => new MetaResource($page->meta),
            'created_at' => (string) $page->created_at,
            'updated_at' => (string) $page->updated_at,
        ];
    }
}
