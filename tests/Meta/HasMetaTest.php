<?php

namespace OptimusCMS\Tests\Meta;

use Mockery;
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
    public function it_can_add_meta_data_to_a_model()
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
    public function it_can_update_the_existing_meta_data_on_a_model()
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

    /** @test */
    public function it_can_retrieve_meta_data_from_the_model()
    {
        $meta = Mockery::mock(Meta::class)->makePartial();

        $meta->setAttribute('title', $title = 'Meta title');
        $meta->setAttribute('description', $description = 'Meta description');
        $meta->setAttribute('og_title', $ogTitle = 'OG title');
        $meta->setAttribute('og_description', $ogDescription = 'OG description');

        $ogImage = 'https://www.optixsolutions.co.uk/og-image.png';

        $meta->shouldReceive('getFirstMediaUrl')
             ->with(
                 Meta::OG_IMAGE_MEDIA_GROUP,
                 Meta::OG_IMAGE_MEDIA_CONVERSION
             )
             ->once()
             ->andReturn($ogImage);

        $this->testSubject->setRelation('meta', $meta);

        // It can retrieve defined meta data...
        $this->assertEquals($title, $this->testSubject->getMeta('title'));
        $this->assertEquals($description, $this->testSubject->getMeta('description'));
        $this->assertEquals($ogTitle, $this->testSubject->getMeta('og_title'));
        $this->assertEquals($ogDescription, $this->testSubject->getMeta('og_description'));
        $this->assertEquals($ogImage, $this->testSubject->getMeta('og_image'));

        // It will return the default value for undefined meta...
        $this->assertEquals(null, $this->testSubject->getMeta('undefined'));
        $this->assertEquals('default', $this->testSubject->getMeta('undefined', 'default'));

        // It will return the default value when meta is defined but falsy...
        $this->testSubject->meta->setAttribute('title', null);
        $this->assertEquals('default', $this->testSubject->getMeta('title', 'default'));
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
