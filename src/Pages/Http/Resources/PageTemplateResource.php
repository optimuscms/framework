<?php

namespace OptimusCMS\Pages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        // Todo: Verify this works...

        return [
            'name' => self::$name,
            'label' => self::$label,
        ];
    }
}
