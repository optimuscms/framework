<?php

namespace OptimusCMS\Tests\Users;

use OptimusCMS\Users\Models\AdminUser;
use OptimusCMS\Users\UserServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Setup the tests by including the database factories.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(
            __DIR__.'/database/factories'
        );
    }

    /**
     * Get the Users Package providers.
     *
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            UserServiceProvider::class,
        ];
    }

    /**
     * Tell the Tests to use the sqllite database and not the provided in .env.
     *
     * @param $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Sign in the user, if the user is NULL one is created via the user Factory.
     *
     * @param Authenticatable|null $user
     * @return Authenticatable|mixed
     */
    protected function signIn(Authenticatable $user = null)
    {
        $user = $user ?: factory(AdminUser::class)->create();
        $this->actingAs($user, 'admin');

        return $user;
    }

    /**
     * The expected json structure that matches the User Resource.
     *
     * @return array
     */
    protected function expectedJsonStructure()
    {
        return [
            'id',
            'name',
            'email',
            'username',
            'created_at',
            'updated_at',
        ];
    }
}
