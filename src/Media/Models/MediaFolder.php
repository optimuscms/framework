<?php

namespace OptimusCMS\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MediaFolder extends Model
{
    protected $fillable = [
        'name', 'parent_id',
    ];

    public function scopeApplyFilters(Builder $query, array $filters)
    {
        if (! empty($filters['parent'])) {
            $parent = $filters['parent'];
            $query->where('parent_id', $parent === 'root' ? null : $parent);
        }
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'folder_id');
    }
}
