<?php

namespace OptimusCMS\Pages;

use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    /** @var string */
    protected $controllerNamespace = 'OptimusCMS\Pages\Http\Controllers';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Routes
        $this->registerAdminRoutes();
    }

    /**
     * Register the routes provided by the package.
     *
     * @return void
     */
    protected function registerAdminRoutes()
    {
        $this->app['router']
             ->name('admin.api.')
             ->prefix('admin/api')
             ->namespace($this->controllerNamespace)
             ->middleware('web') // , 'auth:admin')
             ->group(function ($router) {
                 // Pages
                 $router
                     ->prefix('pages')
                     ->name('pages.')
                     ->group(function ($router) {
                         $router->get('/', 'PagesController@index')->name('index');
                         $router->post('/', 'PagesController@store')->name('store');
                         $router->get('{pageId}', 'PagesController@show')->name('show');
                         $router->patch('{pageId}', 'PagesController@update')->name('update');
                         $router->delete('{pageId}', 'PagesController@destroy')->name('destroy');

                         // Move
                         $router->post('{pageId}/move', 'PagesController@move')->name('move');
                     });

                 // Page templates
                 $router
                     ->prefix('page-templates')
                     ->name('page-templates.')
                     ->group(function ($router) {
                         $router->get('/', 'PageTemplatesController@index')->name('index');
                         $router->get('{templateId}', 'PageTemplatesController@show')->name('show');
                     });
             });
    }
}
