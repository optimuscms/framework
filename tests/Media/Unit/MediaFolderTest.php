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

    /** @test */
    public function it_registers_the_apply_filters_scope()
    {
        $filters = ['parent' => 1];

        $query = Mockery::mock(Builder::class);

        $query->shouldReceive('where')
            ->with('parent_id', $filters['parent'])
            ->once()
            ->andReturnSelf();

        $this->folder->scopeApplyFilters($query, $filters);
    }
}
