<?php

namespace OptimusCMS\Media\Tests\Unit;

use Mockery;
use OptimusCMS\Tests\Media\TestCase;
use OptimusCMS\Media\Models\MediaFolder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaFolderTest extends TestCase
{
    protected $folder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->folder = new MediaFolder();
    }

    /** @test */
    public function it_registers_the_media_relationship()
    {
        $this->assertInstanceOf(
            HasMany::class,
            $this->folder->media()
        );
    }
}
