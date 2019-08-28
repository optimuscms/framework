<?php

namespace OptimusCMS\Users\Tests;

use OptimusCMS\Users\Models\AdminUser;
use OptimusCMS\Users\UserServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(
            __DIR__ . '/database/factories'
        );
    }
    protected function getPackageProviders($app)
    {
        return [
            UserServiceProvider::class,
        ];
    }
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ]);
    }
    protected function signIn(Authenticatable $user = null)
    {
        $user = $user ?: factory(AdminUser::class)->create();
        $this->actingAs($user, 'admin');
        return $user;
    }
    protected function expectedJsonStructure()
    {
        return [
            'id',
            'name',
            'email',
            'username',
            'created_at',
            'updated_at'
        ];
    }
}
