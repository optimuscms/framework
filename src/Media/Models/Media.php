<?php

namespace OptimusCMS\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'folder_id', 'name', 'file_name', 'disk', 'mime_type', 'size'
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
     * Retrieve all the media in the specified folder.
     *
     * @param Builder $query
     * @param $folder
     * @return void
     */
    public function scopeInFolder(Builder $query, $folder)
    {
        if ($folder instanceof MediaFolder) {
            $folder = $folder->getKey();
        }

        $query->where('folder_id', $folder);
    }

    /**
     * Get the media's folder.
     *
     * @return BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }
}
