<?php

namespace OptimusCMS\Tests\Media\Feature;

use OptimusCMS\Tests\Media\TestCase;
use OptimusCMS\Media\Models\MediaFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetFolderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function it_can_display_all_folders()
    {
        $folders = factory(MediaFolder::class, 3)->create();

        $response = $this->getJson(
            route('admin.api.media-folders.index')
        );

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedFolderJsonStructure(),
                ],
            ]);

        $ids = $response->decodeResponseJson('data.*.id');

        $folders->each(function (MediaFolder $folder) use ($ids) {
            $this->assertContains($folder->id, $ids);
        });
    }

    /** @test */
    public function it_can_display_all_the_folders_in_a_specific_folder()
    {
        $parentFolder = factory(MediaFolder::class)->create();
        $childFolders = factory(MediaFolder::class, 2)->create([
            'parent_id' => $parentFolder->id,
        ]);

        $response = $this->getJson(
            route('admin.api.media-folders.index', [
                'parent' => $parentFolder->id,
            ])
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedFolderJsonStructure(),
                ],
            ]);

        $ids = $response->decodeResponseJson('data.*.id');

        $childFolders->each(function (MediaFolder $folder) use ($ids) {
            $this->assertContains($folder->id, $ids);
        });
    }

    /** @test */
    public function it_can_display_a_specific_folder()
    {
        $folder = factory(MediaFolder::class)->create();

        $response = $this->getJson(
            route('admin.api.media-folders.show', [
                'id' => $folder->id
            ])
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedFolderJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'parent_id' => $folder->parent_id,
                    'created_at' => (string) $folder->created_at,
                    'updated_at' => (string) $folder->updated_at,
                ],
            ]);
    }
}
