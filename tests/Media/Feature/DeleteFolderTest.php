<?php

namespace OptimusCMS\Tests\Media\Feature;

use OptimusCMS\Tests\Media\TestCase;
use OptimusCMS\Media\Models\MediaFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteFolderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_folder()
    {
        $this->signIn();

        $folder = factory(MediaFolder::class)->create([
            'name' => 'Name',
            'parent_id' => null,
        ]);

        $response = $this->deleteJson(
            route('admin.api.media-folders.destroy', $folder->id)
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing($folder->getTable(), [
            'id' => $folder->id,
        ]);
    }
}
