<?php

namespace OptimusCMS\Media\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use OptimusCMS\Media\Models\Media;

class MediaResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'folder_id' => $this->folder_id,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'caption' => $this->caption,
            'alt_text' => $this->alt_text,
            'extension' => $this->extension,
            'url' => $this->getUrl(),
            'thumbnail_url' => $this->getUrl(Media::THUMBNAIL_CONVERSION),
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'group' => $this->whenPivotLoaded('mediables', function () {
                return $this->pivot->group;
            }),
            'updated_at' => (string) $this->updated_at,
            'created_at' => (string) $this->created_at,
        ];
    }
}
