<?php

namespace OptimusCMS\Media\Models;

use Illuminate\Database\Eloquent\Builder;
use Optix\Media\Models\Media as BaseMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends BaseMedia
{
    /** @var string */
    const THUMBNAIL_CONVERSION = '400x300';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'folder_id',
        'name',
        'alt_text',
        'caption',
        'file_name',
        'disk',
        'mime_type',
        'size',
    ];

    /**
     * Apply filters to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeApplyFilters(Builder $query, array $filters)
    {
        // Folder
        if (! empty($filters['folder'])) {
            $query->inFolder($filters['folder']);
        }
    }

    /**
     * Only retrieve media in the specified folder.
     *
     * @param Builder $query
     * @param $folder
     * @return void
     */
    public function scopeInFolder(Builder $query, $folder)
    {
        if ($folder instanceof MediaFolder) {
            $folder = $folder->id;
        }

        $query->where(
            'folder_id',
            $folder === 'root' ? null : $folder
        );
    }

    /**
     * Get the folder relationship.
     *
     * @return BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }
}
