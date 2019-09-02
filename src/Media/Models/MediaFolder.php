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
        'name', 'parent_id',
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
        if (! empty($filters['parent'])) {
            $parent = $filters['parent'];
            $query->where('parent_id', $parent === 'root' ? null : $parent);
        }
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
