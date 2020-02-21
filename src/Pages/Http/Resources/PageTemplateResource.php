<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'identifier' => $this->resource::identifier(),
            'name' => $this->resource::name(),
        ];
    }
}
