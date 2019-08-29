<?php

namespace OptimusCMS\Tests\Media\Feature;

use OptimusCMS\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use OptimusCMS\Tests\Media\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use OptimusCMS\Media\Models\MediaFolder;
use Optix\Media\Jobs\PerformConversions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();

        Storage::fake('public');
    }

    /** @test */
    public function it_can_upload_media_to_the_root()
    {
        $image = UploadedFile::fake()->create('image.png')->size(64);

        Queue::fake();

        $response = $this->postJson(
            route('admin.api.media.store'), $data = [
                'folder_id' => null,
                'file' => $image
            ]
        );

        // Assert media thumbnail conversion ran...
        Queue::assertPushed(PerformConversions::class);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->expectedMediaJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'folder_id' => $data['folder_id'],
                    'name' => 'image',
                    'file_name' => 'image.png',
                    'extension' => 'png',
                    'mime_type' => 'image/png',
                    'size' => $image->getSize()
                ]
            ]);

        $this->assertMediaExists(
            $response->decodeResponseJson('data.id')
        );
    }

    /** @test */
    public function it_can_upload_media_into_a_folder()
    {
        $folder = factory(MediaFolder::class)->create();

        $document = UploadedFile::fake()->create('document.doc')->size(128);

        $response = $this->postJson(
            route('admin.api.media.store'), $data = [
                'folder_id' => $folder->id,
                'file' => $document
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->expectedMediaJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'folder_id' => $data['folder_id'],
                    'name' => 'document',
                    'file_name' => 'document.doc',
                    'extension' => 'doc',
                    'mime_type' => 'application/msword',
                    'size' => $document->getSize()
                ]
            ]);

        $this->assertMediaExists(
            $response->decodeResponseJson('data.id')
        );
    }

    /** @test */
    public function it_will_upload_media_to_the_root_if_a_folder_id_is_not_present()
    {
        $audio = UploadedFile::fake()->create('audio.mp3')->size(32);

        $response = $this->postJson(
            route('admin.api.media.store'), [
                'file' => $audio
            ]
        );

        $response
            ->assertJsonStructure([
                'data' => $this->expectedMediaJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'folder_id' => null,
                    'name' => 'audio',
                    'file_name' => 'audio.mp3',
                    'extension' => 'mp3',
                    'mime_type' => 'audio/mpeg',
                    'size' => $audio->getSize()
                ]
            ]);

        $this->assertMediaExists(
            $response->decodeResponseJson('data.id')
        );
    }

    /** @test */
    public function it_will_only_perform_the_media_thumbnail_conversion_on_images()
    {
        $document = UploadedFile::fake()->create('document.doc');
        $image = UploadedFile::fake()->image('image.png');

        Queue::fake();

        $this->postJson(
            route('admin.api.media.store'), [
                'file' => $document
            ]
        );

        // Assert conversion did not run...
        Queue::assertNotPushed(PerformConversions::class);

        $this->postJson(
            route('admin.api.media.store'), [
                'file' => $image
            ]
        );

        // Assert conversion ran...
        Queue::assertPushed(PerformConversions::class);
    }

    /** @test */
    public function the_file_field_must_be_present()
    {
        $response = $this->postJson(route('admin.api.media.store'));

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'file'
            ]);
    }

    /** @test */
    public function the_file_field_must_be_a_file_when_present()
    {
        $response = $this->postJson(
            route('admin.api.media.store'), [
                'file' => 'not-a-file'
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'file'
            ]);
    }

    /** @test */
    public function the_folder_id_field_must_be_an_existing_folder_id_if_not_null()
    {
        $document = UploadedFile::fake()->create('document.doc');

        $response = $this->postJson(
            route('admin.api.media.store'), [
                'file' => $document,
                'folder_id' => 9999
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'folder_id'
            ]);
    }

    protected function assertMediaExists($id)
    {
        $this->assertNotNull(Media::find($id));
    }
}
