<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource::id(),
            'name' => $this->resource::name(),
        ];
    }
}
