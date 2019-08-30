<?php

namespace OptimusCMS\Tests\Media;

use OptimusCMS\Users\Models\AdminUser;
use OptimusCMS\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function signIn()
    {
        $user = factory(AdminUser::class)->create();

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
            'updated_at',
        ];
    }

    protected function expectedFolderJsonStructure()
    {
        return [
            'id',
            'name',
            'parent_id',
            'created_at',
            'updated_at',
        ];
    }
}
