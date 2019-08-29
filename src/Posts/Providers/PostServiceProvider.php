<?php

namespace OptimusCMS\Posts\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    protected $controllerNamespace = 'OptimusCMS\Posts\Http\Controllers';

    public function boot()
    {
        $this->loadMigrationsFrom(
            __DIR__.'/../../database/migrations'
        );

        $this->publishes([
            __DIR__.'/../../config/post.php' => config_path('post.php'),
        ], 'config');

        $this->mapAdminRoutes();
    }

    protected function mapAdminRoutes()
    {
        Route::prefix('api')
            ->middleware('api', 'auth:admin')
            ->namespace($this->controllerNamespace)
            ->group(function () {
                Route::apiResource('posts', 'PostsController');
                Route::apiResource('post-tags', 'TagsController');
                Route::apiResource('post-comments', 'CommentsController')->except([
                    'store', 'update',
                ]);
            });
    }
}
