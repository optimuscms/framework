<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->resource::name(),
            'label' => $this->resource::label(),
        ];
    }
}
