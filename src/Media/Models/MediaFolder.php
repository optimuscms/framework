<?php

namespace OptimusCMS\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaFolder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'parent_id',
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
        // Parent
        if (! empty($filters['parent'])) {
            $query->inFolder($filters['parent']);
        }
    }

    /**
     * Only retrieve folders in the specified folder.
     *
     * @param Builder $query
     * @param self|int $folder
     * @return void
     */
    public function scopeInFolder(Builder $query, $folder)
    {
        if ($folder instanceof self) {
            $folder = $folder->id;
        }

        $query->where(
            'parent_id',
            $folder === 'root' ? null : $folder
        );
    }

    /**
     * Determine if the folder is a descendant of the given folder.
     *
     * @param self|int $folder
     * @return bool
     */
    public function isDescendantOf($folder)
    {
        if ($folder instanceof self) {
            $folder = $folder->id;
        }

        if (! $this->parent_id) {
            return false;
        }

        if ($this->parent_id === $folder) {
            return true;
        }

        $parentFolder = $this->parent;

        return $parentFolder
            && $parentFolder->isDescendantOf($folder);
    }

    /**
     * Get the media relationship.
     *
     * @return HasMany
     */
    public function media()
    {
        return $this->hasMany(Media::class, 'folder_id');
    }
}
