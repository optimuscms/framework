<?php

namespace OptimusCMS\Media\Tests\Unit;

use Mockery;
use OptimusCMS\Media\Models\Media;
use OptimusCMS\Tests\Media\TestCase;
use Illuminate\Database\Eloquent\Builder;
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

    /** @test */
    public function it_registers_the_apply_filters_scope()
    {
        $filters = ['folder' => 1];

        $query = Mockery::mock(Builder::class);

        $query->shouldReceive('where')
            ->with('folder_id', $filters['folder'])
            ->once()
            ->andReturnSelf();

        $this->media->scopeApplyFilters($query, $filters);
    }
}
