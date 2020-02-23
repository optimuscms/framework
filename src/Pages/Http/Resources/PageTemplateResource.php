<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        $template = $this->resource;

        return [
            'id' => $template::id(),
            'name' => $template::name(),
        ];
    }
}
