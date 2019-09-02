<?php

namespace OptimusCMS\Tests\Meta;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use OptimusCMS\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create a table for the test subject models...
        Schema::create('test_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
    }
}
