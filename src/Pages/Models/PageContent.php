<?php

namespace OptimusCMS\Pages\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $fillable = [
        'key', 'value'
    ];
}
