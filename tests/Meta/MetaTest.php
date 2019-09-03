<?php

namespace OptimusCMS\Tests\Meta;

use Mockery;
use OptimusCMS\Meta\Models\Meta;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MetaTest extends TestCase
{
    /** @test */
    public function it_registers_the_metable_relationship()
    {
        $meta = new Meta();

        $this->assertInstanceOf(
            MorphTo::class, $meta->metable()
        );
    }

    /** @test */
    public function it_registers_an_og_image_attribute()
    {
        $meta = Mockery::mock(Meta::class)->makePartial();

        $ogImage = 'https://www.optixsolutions.co.uk/og-image.png';

        $meta->shouldReceive('getFirstMediaUrl')
             ->with(
                 Meta::OG_IMAGE_MEDIA_GROUP,
                 Meta::OG_IMAGE_MEDIA_CONVERSION
             )
             ->once()
             ->andReturn($ogImage);

        $this->assertEquals($ogImage, $meta->og_image);
    }
}
