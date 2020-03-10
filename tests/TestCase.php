<?php

namespace OptimusCMS\Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use OptimusCMS\Pages\PageServiceProvider;
use OptimusCMS\Users\Models\AdminUser;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PageServiceProvider::class,
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
