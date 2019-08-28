<?php

namespace OptimusCMS\Pages\Tests;

use Mockery;
use OptimusCMS\Pages\Template;
use OptimusCMS\Users\Models\AdminUser;
use OptimusCMS\Pages\TemplateRegistry;
use OptimusCMS\Pages\PageServiceProvider;
use OptimusCMS\Users\UserServiceProvider;
use OptimusCMS\Media\MediaServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            UserServiceProvider::class,
            PageServiceProvider::class,
            MediaServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/database/factories');

        foreach (['users', 'media'] as $package) {
            $this->loadMigrationsFrom(
                __DIR__ . "/../vendor/optimuscms/{$package}/database/migrations"
            );
        }
    }

    protected function signIn()
    {
        $user = AdminUser::create([
            'name' => 'Admin',
            'email' => 'admin@optimus.test',
            'username' => 'admin',
            'password' => bcrypt('password')
        ]);

        $this->actingAs($user, 'admin');

        return $user;
    }

    protected function registerTemplate(Template $template)
    {
        $this->app[TemplateRegistry::class]->register($template);
    }

    protected function registerTemplates(array $templates)
    {
        $this->app[TemplateRegistry::class]->registerMany($templates);
    }

    protected function mockTemplate(string $name)
    {
        $template = Mockery::mock(Template::class);

        $template->shouldReceive('name')->andReturn($name);

        return $template;
    }

    public function expectedPageJsonStructure()
    {
        return [
            'id',
            'title',
            'slug',
            'uri',
            'has_fixed_uri',
            'parent_id',
            'template',
            'has_fixed_template',
            'contents' => [
                '*' => [
                    'key',
                    'value'
                ]
            ],
            'media' => [],
            'is_stand_alone',
            'is_published',
            'is_deletable',
            'created_at',
            'updated_at'
        ];
    }
}
