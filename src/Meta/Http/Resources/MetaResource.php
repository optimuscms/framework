<?php

namespace OptimusCMS\Meta\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OptimusCMS\Media\Http\Resources\MediaResource;
use OptimusCMS\Meta\Models\Meta;

class MetaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => new MediaResource(
                $this->getFirstMedia(Meta::OG_IMAGE_MEDIA_GROUP)
            ),
            'additional_tags' => $this->additional_tags,
        ];
    }
}
