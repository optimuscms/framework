<?php

namespace OptimusCMS\Media\Tests\Unit;

use OptimusCMS\Media\Models\Media;
use OptimusCMS\Tests\Media\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaTest extends TestCase
{
    protected $media;

    protected function setUp(): void
    {
        parent::setUp();

        $this->media = new Media();
    }

    /** @test */
    public function it_registers_the_folder_relationship()
    {
        $this->assertInstanceOf(
            BelongsTo::class, $this->media->folder()
        );
    }
}
