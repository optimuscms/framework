<?php

namespace OptimusCMS\Tests\Meta;

use OptimusCMS\Meta\Models\Meta;
use OptimusCMS\Media\Models\Media;
use Illuminate\Support\Facades\Queue;
use Optix\Media\Jobs\PerformConversions;
use OptimusCMS\Tests\Meta\Models\TestSubject;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasMetaTest extends TestCase
{
    use RefreshDatabase;

    /** @var TestSubject */
    protected $testSubject;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->testSubject = TestSubject::create();
    }

    /** @test */
    public function it_can_add_meta_to_a_model()
    {
        $meta = $this->testSubject->saveMeta(
            $data = $this->validData()
        );

        // Assert meta record was created...
        $this->assertEquals($data['title'], $meta->title);
        $this->assertEquals($data['description'], $meta->description);
        $this->assertEquals($data['og_title'], $meta->og_title);
        $this->assertEquals($data['og_description'], $meta->og_description);

        // Assert media item was attached...
        $this->assertInstanceOf(
            Media::class,
            $media = $meta->getFirstMedia(Meta::OG_IMAGE_MEDIA_GROUP)
        );

        $this->assertEquals($data['og_image_id'], $media->id);

        $this->assertOgImageWasManipulated($media);
    }

    /** @test */
    public function it_can_update_existing_meta()
    {
        $originalMeta = $this->testSubject->saveMeta($this->validData());

        $updatedMeta = $this->testSubject->saveMeta($data = [
            'title' => 'Updated meta title',
            'description' => 'Updated meta description',
            'og_title' => 'Updated OG title',
            'og_description' => 'Updated OG description',
            'og_image_id' => factory(Media::class)->create()->id,
        ]);

        // Assert that the existing meta record was updated...
        $this->assertTrue($originalMeta->is($updatedMeta));

        $this->assertEquals($data['title'], $updatedMeta->title);
        $this->assertEquals($data['description'], $updatedMeta->description);
        $this->assertEquals($data['og_title'], $updatedMeta->og_title);
        $this->assertEquals($data['og_description'], $updatedMeta->og_description);

        // Assert media item was updated...
        $this->assertInstanceOf(
            Media::class,
            $media = $updatedMeta->getFirstMedia(Meta::OG_IMAGE_MEDIA_GROUP)
        );

        $this->assertEquals($data['og_image_id'], $media->id);

        $this->assertOgImageWasManipulated($media);
    }

    protected function assertOgImageWasManipulated(Media $media)
    {
        Queue::assertPushed(
            PerformConversions::class,
            function (PerformConversions $job) use ($media) {
                return $media->is($job->getMedia()) && in_array(
                    Meta::OG_IMAGE_MEDIA_CONVERSION,
                    $job->getConversions()
                );
            }
        );
    }

    protected function validData(array $overrides = [])
    {
       return array_merge([
            'title' => 'Meta title',
            'description' => 'Meta description',
            'og_title' => 'OG title',
            'og_description' => 'OG description',
            'og_image_id' =>  factory(Media::class)->create()->id,
        ], $overrides);
    }
}
