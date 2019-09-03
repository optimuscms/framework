<?php

namespace OptimusCMS\Meta\Http\Resources;

use OptimusCMS\Meta\Models\Meta;
use Illuminate\Http\Resources\Json\JsonResource;
use OptimusCMS\Media\Http\Resources\MediaResource;

class MetaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => $this->og_image,
            'additional_tags' => $this->additional_tags,
        ];
    }
}
