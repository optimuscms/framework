<?php

namespace OptimusCMS\Tests\Users;

use OptimusCMS\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
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
