<?php

namespace OptimusCMS\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageContent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'value'
    ];

    /**
     * Get the page relationship.
     *
     * @return BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
