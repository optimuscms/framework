<?php

namespace OptimusCMS\Tests\Media\Feature;

use OptimusCMS\Media\Models\Media;
use OptimusCMS\Tests\Media\TestCase;
use OptimusCMS\Media\Models\MediaFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateMediaTest extends TestCase
{
    use RefreshDatabase;

    protected $media;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();

        $folder = factory(MediaFolder::class)->create([
            'name' => 'New folder name',
        ]);

        $this->media = factory(Media::class)->create([
            'name' => 'Old name',
            'folder_id' => $folder->id,
        ]);
    }

    /** @test */
    public function it_can_change_the_name_of_a_media_item()
    {
        $response = $this->patchJson(
            route('admin.api.media.update', [
                'id' => $this->media->id,
            ]), $newData = [
                'name' => 'New name',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedMediaJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'name' => $newData['name'],
                    'folder_id' => $this->media->folder_id,
                ],
            ]);
    }

    /** @test */
    public function it_can_move_a_media_item_into_another_folder()
    {
        $newFolder = factory(MediaFolder::class)->create([
            'name' => 'New folder name',
        ]);

        $response = $this->patchJson(
            route('admin.api.media.update', [
                'id' => $this->media->id,
            ]), $newData = [
                'folder_id' => $newFolder->id,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedMediaJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'name' => $this->media->name,
                    'folder_id' => $newData['folder_id'],
                ],
            ]);
    }

    /** @test */
    public function it_can_move_a_media_item_into_the_root_folder()
    {
        $response = $this->patchJson(
            route('admin.api.media.update', [
                'id' => $this->media->id,
            ]), $newData = [
                'folder_id' => null,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedMediaJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'name' => $this->media->name,
                    'folder_id' => $newData['folder_id'],
                ],
            ]);
    }

    /** @test */
    public function the_folder_id_must_be_an_existing_folder_id_if_not_null()
    {
        $response = $this->patchJson(
            route('admin.api.media.update', [
                'id' => $this->media->id,
            ]), [
                'folder_id' => 9999,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'folder_id',
            ]);
    }

    /** @test */
    public function the_name_field_must_not_be_empty_when_present()
    {
        $response = $this->patchJson(
            route('admin.api.media.update', [
                'id' => $this->media->id,
            ]), [
                'name' => '',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }
}
