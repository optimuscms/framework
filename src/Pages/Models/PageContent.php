<?php

namespace OptimusCMS\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageContent extends Model
{
    protected $fillable = [
        'key', 'value',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
