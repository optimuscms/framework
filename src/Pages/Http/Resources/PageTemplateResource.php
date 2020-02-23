<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class PageTemplateResource extends JsonResource
{
    public function toArray($request)
    {
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
