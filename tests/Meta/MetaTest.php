<?php

namespace OptimusCMS\Tests\Meta;

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
}
