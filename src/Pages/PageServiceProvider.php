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

    protected function registerAdminRoutes()
    {
        $this->app['router']
             ->name('admin.api.')
             ->prefix('admin/api')
             ->namespace($this->controllerNamespace)
             ->middleware('web', 'auth:admin')
             ->group(function ($router) {
                 // Pages
                 $router->prefix('pages')->group(function ($router) {
                     $router->get('/', 'PagesController@index');
                     $router->post('/', 'PagesController@store');
                     $router->get('{pageId}', 'PagesController@show');
                     $router->patch('{pageId}', 'PagesController@update');
                     $router->delete('{pageId}', 'PagesController@destroy');

                     // Sort
                     $router->post('{pageId}/sort', 'PagesController@sort');
                 });

                 // Page templates
                 $router->prefix('page-templates')->group(function ($router) {
                     $router->get('/', 'PageTemplatesController@index');
                     $router->show('{templateId}', 'PageTemplatesController@show');
                 });
             });
    }
}
