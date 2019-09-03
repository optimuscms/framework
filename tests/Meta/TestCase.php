<?php

namespace OptimusCMS\Tests\Meta;

use OptimusCMS\Media\Models\Media;
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

    protected function makeOgImageMedia()
    {
        return factory(Media::class)->create([
            'file_name' => 'og-image.png',
        ]);
    }
}
