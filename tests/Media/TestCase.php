<?php

namespace OptimusCMS\Tests\Media;

use OptimusCMS\Media\Models\Media;
use OptimusCMS\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('media.disk', 'public');
        $app['config']->set('media.model', Media::class);
    }

    protected function expectedMediaJsonStructure()
    {
        return [
            'id',
            'folder_id',
            'name',
            'alt_text',
            'caption',
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
