<?php

namespace OptimusCMS\Tests;

use OptimusCMS\Users\Models\AdminUser;
use OptimusCMS\Meta\MetaServiceProvider;
use OptimusCMS\Pages\PageServiceProvider;
use OptimusCMS\Users\UserServiceProvider;
use OptimusCMS\Media\MediaServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            MediaServiceProvider::class,
            MetaServiceProvider::class,
            PageServiceProvider::class,
            UserServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function signIn(Authenticatable $user = null)
    {
        $user = $user ?: factory(AdminUser::class)->create();

        $this->actingAs($user, 'admin');

        return $user;
    }
}
