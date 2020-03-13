<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OptimusCMS\Meta\Http\Resources\MetaResource;
use OptimusCMS\Pages\Contracts\PageTemplate;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Pages\PageTemplates;
use stdClass;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Page $page */
        $page = $this->resource;

        /** @var PageTemplate $template */
        $template = PageTemplates::get($page->template_id);

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
            'template_data' => empty($data = $template::getData($page))
                ? new stdClass()
                : $data,
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
