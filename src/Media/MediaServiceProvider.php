<?php

namespace OptimusCMS\Media;

use Illuminate\Support\ServiceProvider;

// Todo: Doc block...

class MediaServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMigrations();
        $this->registerConfig();
        $this->registerRoutes();
    }

    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(
            __DIR__.'/database/migrations'
        );
    }

    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/media.php', 'media'
        );
    }

    protected function registerRoutes()
    {
        $this->app['router']
             ->name('admin.api.')
             ->prefix('admin/api')
             ->namespace('OptimusCMS\Media\Http\Controllers')
             //->middleware('web', 'auth:admin')
             ->group(function ($router) {
                 // Media
                 $router->apiResource('media', 'MediaController');

                 // Folders
                 $router->apiResource('media-folders', 'MediaFoldersController');
            });
    }
}
