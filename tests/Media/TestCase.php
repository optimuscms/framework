<?php

namespace OptimusCMS\Tests\Media;

use OptimusCMS\Media\Models\Media;
use OptimusCMS\Users\Models\AdminUser;
use OptimusCMS\Users\UserServiceProvider;
use OptimusCMS\Media\MediaServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use OptimusCMS\Media\MediaServiceProvider as OptimusCMSMediaServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            UserServiceProvider::class,
            MediaServiceProvider::class,
            OptimusCMSMediaServiceProvider::class
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ]);

        $app['config']->set('media.disk', 'public');
        $app['config']->set('media.model', Media::class);
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

    protected function expectedMediaJsonStructure()
    {
        return [
            'id',
            'folder_id',
            'name',
            'file_name',
            'mime_type',
            'size',
            'created_at',
            'updated_at'
        ];
    }

    protected function expectedFolderJsonStructure()
    {
        return [
            'id',
            'name',
            'parent_id',
            'created_at',
            'updated_at'
        ];
    }
}
