<?php

namespace OptimusCMS\Posts;

use Illuminate\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(
            __DIR__.'/database/migrations'
        );
    }
}
