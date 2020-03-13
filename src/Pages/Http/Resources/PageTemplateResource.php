<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OptimusCMS\Pages\Contracts\PageTemplate;
use stdClass;

class PageTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var PageTemplate $template */
        $template = $this->resource;

        return [
            'id' => $template::getId(),
            'name' => $template::getName(),
            'meta' => empty($meta = $template::getMeta())
                ? new stdClass()
                : $meta,
        ];
    }
}
