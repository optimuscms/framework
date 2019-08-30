<?php

namespace OptimusCMS\Pages;

use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    protected $controllerNamespace = 'OptimusCMS\Pages\Http\Controllers';

    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Routes
        $this->registerAdminRoutes();
    }

    public function register()
    {
        $this->app->singleton(TemplateRegistry::class);
    }

    protected function registerAdminRoutes()
    {
        $this->app['router']
             ->name('admin.api.')
             ->prefix('admin/api')
             ->namespace($this->controllerNamespace)
             ->middleware('web', 'auth:admin')
             ->group(function ($router) {
                 // Pages
                 $router->apiResource('pages', 'PagesController');
                 $router->patch('pages', 'PagesController@reorder');

                 // Templates
                 $router->apiResource('page-templates', 'TemplatesController')
                        ->only(['index', 'show']);
             });
    }
}
