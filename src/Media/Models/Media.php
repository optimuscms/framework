<?php

namespace OptimusCMS\Media\Models;

use Illuminate\Database\Eloquent\Builder;
use Optix\Media\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    const THUMBNAIL_CONVERSION = '400x300';

    protected $fillable = [
        'folder_id', 'name', 'caption', 'alt_text', 'file_name', 'disk', 'mime_type', 'size',
    ];

    public function scopeApplyFilters(Builder $query, array $filters)
    {
        if (! empty($filters['folder'])) {
            $folder = $filters['folder'];
            $query->where('folder_id', $folder === 'root' ? null : $folder);
        }
    }

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }
}
