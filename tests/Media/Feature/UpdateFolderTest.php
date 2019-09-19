<?php

namespace OptimusCMS\Tests\Media\Feature;

use OptimusCMS\Tests\Media\TestCase;
use OptimusCMS\Media\Models\MediaFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateFolderTest extends TestCase
{
    use RefreshDatabase;

    protected $folder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();

        $parent = factory(MediaFolder::class)->create([
            'name' => 'Old parent name',
        ]);

        $this->folder = factory(MediaFolder::class)->create([
            'name' => 'Old name',
            'parent_id' => $parent->id,
        ]);
    }

    /** @test */
    public function it_can_change_the_name_of_a_folder()
    {
        $response = $this->patchJson(
            route('admin.api.media-folders.update', $this->folder->id),
            $newData = [
                'name' => 'New name',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedFolderJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'name' => $newData['name'],
                    'parent_id' => $this->folder->parent_id,
                ],
            ]);
    }

    /** @test */
    public function it_can_move_a_folder_into_another_folder()
    {
        $newParent = factory(MediaFolder::class)->create([
            'name' => 'New parent name',
        ]);

        $response = $this->patchJson(
            route('admin.api.media-folders.update', $this->folder->id),
            $newData = [
                'parent_id' => $newParent->id,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedFolderJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'name' => $this->folder->name,
                    'parent_id' => $newData['parent_id'],
                ],
            ]);
    }

    /** @test */
    public function it_can_move_a_folder_into_the_root()
    {
        $response = $this->patchJson(
            route('admin.api.media-folders.update', $this->folder->id),
            $newData = [
                'parent_id' => null,
            ]
        );

        $response
            ->assertJsonStructure([
                'data' => $this->expectedFolderJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'name' => $this->folder->name,
                    'parent_id' => $newData['parent_id'],
                ],
            ]);
    }

    /** @test */
    public function the_name_field_must_not_be_empty_when_present()
    {
        $response = $this->patchJson(
            route('admin.api.media-folders.update', $this->folder->id), [
                'name' => '',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /** @test */
    public function the_parent_id_field_must_be_an_existing_folder_id_if_not_null()
    {
        $response = $this->patchJson(
            route('admin.api.media-folders.update', $this->folder->id), [
                'parent_id' => 9999,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'parent_id',
            ]);
    }
}
