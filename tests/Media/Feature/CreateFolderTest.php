<?php

namespace OptimusCMS\Tests\Media\Feature;

use OptimusCMS\Tests\Media\TestCase;
use OptimusCMS\Media\Models\MediaFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateFolderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function it_can_add_a_folder_to_the_root()
    {
        $response = $this->postJson(route('admin.api.media-folders.store'), $data = [
            'name' => 'Name',
            'parent_id' => null
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->expectedFolderJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'name' => $data['name'],
                    'parent_id' => $data['parent_id']
                ]
            ]);

        $this->assertFolderExists(
            $response->decodeResponseJson('data.id')
        );
    }

    /** @test */
    public function it_will_add_folders_to_the_root_if_a_parent_id_is_not_present()
    {
        $response = $this->postJson(route('admin.api.media-folders.store'), $data = [
            'name' => 'Name'
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->expectedFolderJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'name' => $data['name'],
                    'parent_id' => null
                ]
            ]);

        $this->assertFolderExists(
            $response->decodeResponseJson('data.id')
        );
    }

    /** @test */
    public function it_can_add_a_folder_into_another_folder()
    {
        $parent = factory(MediaFolder::class)->create([
            'name' => 'Parent name'
        ]);

        $response = $this->postJson(route('admin.api.media-folders.store'), $data = [
            'name' => 'Name',
            'parent_id' => $parent->id
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->expectedFolderJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'name' => $data['name'],
                    'parent_id' => $data['parent_id']
                ]
            ]);

        $this->assertFolderExists(
            $response->decodeResponseJson('data.id')
        );
    }

    /** @test */
    public function the_name_field_must_be_present()
    {
        $response = $this->postJson(route('admin.api.media-folders.store'));

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name'
            ]);
    }

    /** @test */
    public function the_name_field_must_not_be_empty_when_present()
    {
        $response = $this->postJson(route('admin.api.media-folders.store'), [
            'name' => ''
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name'
            ]);
    }

    /** @test */
    public function the_parent_id_field_must_be_an_existing_folder_id_if_not_null()
    {
        $response = $this->postJson(route('admin.api.media-folders.store'), [
            'name' => 'New name',
            'parent_id' => 9999
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'parent_id'
            ]);
    }

    protected function assertFolderExists($id)
    {
        $this->assertTrue(
            MediaFolder::where('id', $id)->exists()
        );
    }
}
