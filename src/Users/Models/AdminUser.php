<?php

namespace OptimusCMS\Users\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the user's gravatar url.
     *
     * @return string
     */
    public function getGravatarUrlAttribute()
    {
        return vsprintf('https://www.gravatar.com/avatar/%s', [
            md5(strtolower(trim($this->email))),
        ]);
    }
}
