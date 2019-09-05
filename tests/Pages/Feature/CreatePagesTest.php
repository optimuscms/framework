<?php

namespace OptimusCMS\Tests\Pages\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Tests\Pages\TestCase;
use OptimusCMS\Tests\Pages\DummyTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePagesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function it_can_create_a_page()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $data = $this->validData()
        );

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->expectedPageJsonStructure(),
            ])
            ->assertJson([
                'data' => [
                    'title' => $data['title'],
                    'slug' => $slug = Str::slug($data['title']),
                    'path' => $slug,
                    'has_fixed_path' => false,
                    'template' => [
                        'name' => $data['template']['name'],
                        'data' => $data['template']['data'],
                        'is_fixed' => false,
                    ],
                    'parent_id' => $data['parent_id'],
                    'is_standalone' => $data['is_standalone'],
                    'is_deletable' => true,
                    'is_published' => $data['is_published'],
                ],
            ]);
    }

    /** @test */
    public function there_are_required_fields()
    {
        $response = $this->postJson(
            route('admin.api.pages.store')
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title', 'template.name', 'is_standalone', 'is_published',
            ]);
    }

    /** @test */
    public function the_template_field_must_be_the_name_of_a_registered_template()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $this->validData([
                'template' => [
                    'name' => 'unregistered',
                ],
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'template.name',
            ]);
    }

    /** @test */
    public function the_parent_id_field_must_be_a_valid_page_id_if_not_null()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $this->validData([
                'parent_id' => -1,
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'parent_id',
            ]);
    }

    /** @test */
    public function the_is_standalone_field_must_be_a_boolean()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $this->validData([
                'is_standalone' => 'string',
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'is_standalone',
            ]);
    }

    /** @test */
    public function the_is_published_field_must_be_a_boolean()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $this->validData([
                'is_published' => 'string',
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'is_published',
            ]);
    }

    protected function validData(array $overrides = [])
    {
        $this->registerTemplate(
            $templateClass = DummyTemplate::class
        );

        return array_merge([
            'title' => 'Title',
            'template' => [
                'name' => $templateClass::name(),
                'data' => [
                    // Required by the dummy template...
                    'heading' => 'Heading',
                ],
            ],
            'parent_id' => factory(Page::class)->create()->id,
            'is_standalone' => false,
            'is_published' => true,
        ], $overrides);
    }
}
