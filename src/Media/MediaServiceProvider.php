<?php

namespace OptimusCMS\Media;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\Image;
use OptimusCMS\Media\Models\Media;
use Optix\Media\Facades\Conversion;
use Optix\Media\MediaServiceProvider as OptixMediaServiceProvider;

class MediaServiceProvider extends ServiceProvider
{
    protected $controllerNamespace = 'OptimusCMS\Media\Http\Controllers';

    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Config
        $this->publishes([
            __DIR__.'/config/media.php' => config_path('media.php'),
        ], 'config');

        // Routes
        $this->registerAdminRoutes();

        // Conversions
        Conversion::register(
            Media::THUMBNAIL_CONVERSION,
            function (Image $image) {
                return $image->fit(400, 300);
            }
        );
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/media.php', 'media'
        );

        $this->app->register(OptixMediaServiceProvider::class);
    }

    protected function registerAdminRoutes()
    {
        $this->app['router']
             ->name('admin.api.')
             ->prefix('admin/api')
             ->middleware('web', 'auth:admin')
             ->namespace($this->controllerNamespace)
             ->group(function ($router) {
                 $router->apiResource('media', 'MediaController');
                 $router->apiResource('media-folders', 'MediaFoldersController');
             });
    }
}
