<?php

namespace OptimusCMS\Tests\Media\Feature;

use OptimusCMS\Media\Models\Media;
use OptimusCMS\Tests\Media\TestCase;
use OptimusCMS\Media\Models\MediaFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function it_can_display_all_media()
    {
        $media = factory(Media::class, 3)->create();

        $response = $this->getJson(
            route('admin.api.media.index')
        );

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedMediaJsonStructure(),
                ],
            ]);

        $ids = $response->decodeResponseJson('data.*.id');

        $media->each(function (Media $media) use ($ids) {
            $this->assertContains($media->id, $ids);
        });
    }

    /** @test */
    public function it_can_get_all_the_media_in_a_specific_folder()
    {
        $folder = factory(MediaFolder::class)->create();

        $rootMedia = factory(Media::class)->create();

        $folderMedia = factory(Media::class, 2)->create([
            'folder_id' => $folder->id,
        ]);

        $response = $this->getJson(
            route('admin.api.media.index', [
                'folder' => $folder->id,
            ])
        );

        $response
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedMediaJsonStructure(),
                ],
            ]);

        $ids = $response->decodeResponseJson('data.*.id');

        $this->assertNotContains($rootMedia->id, $ids);

        $folderMedia->each(function (Media $media) use ($ids) {
            $this->assertContains($media->id, $ids);
        });
    }

    /** @test */
    public function it_can_display_a_specific_media_item()
    {
        $media = factory(Media::class)->create();

        $response = $this->getJson(
            route('admin.api.media.show', [
                'id' => $media->id,
            ])
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedMediaJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'id' => $media->id,
                    'folder_id' => $media->folder_id,
                    'name' => $media->name,
                    'alt_text' => $media->alt_text,
                    'caption' => $media->caption,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'created_at' => (string) $media->created_at,
                    'updated_at' => (string) $media->updated_at,
                ],
            ]);
    }
}
