<?php

namespace OptimusCMS\Media\Tests\Unit;

use Mockery;
use Illuminate\Http\Request;
use OptimusCMS\Media\Tests\TestCase;
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
            HasMany::class, $this->folder->media()
        );
    }

    /** @test */
    public function it_registers_the_filter_scope()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('filled')->with('parent')->once()->andReturn(true);
        $request->shouldReceive('input')->with('parent')->andReturn($parentId = 1);

        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')->with('parent_id', $parentId)->once()->andReturnSelf();

        $this->folder->scopeFilter($query, $request);
    }
}
