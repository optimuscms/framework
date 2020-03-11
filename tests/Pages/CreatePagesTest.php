<?php

namespace OptimusCMS\Tests\Pages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Pages\PageTemplates;
use OptimusCMS\Tests\TestCase;
use stdClass;

class CreatePagesTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultTemplate = TestCase::DEFAULT_TEMPLATE;

    protected function setUp(): void
    {
        parent::setUp();

        PageTemplates::register([
            $this->defaultTemplate,
        ]);
    }

    /** @test */
    public function it_can_create_a_page()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $data = $this->validData()
        );

        $response->assertCreated();

        $page = Page::find(
            $response->decodeResponseJson('data.id')
        );

        $this->assertNotNull($page);

        $response->assertJson([
            'data' => [
                'title' => $data['title'],
                'slug' => $data['slug'],
                'template_id' => $this->defaultTemplate::getId(),
                'template_name' => $this->defaultTemplate::getName(),
                'template_data' => $this->defaultTemplate::getData($page),
                'parent_id' => $data['parent_id'],
                'is_standalone' => $data['is_standalone'],
                'is_published' => $data['is_published'],
            ],
        ]);
    }

    /** @test */
    public function it_can_create_a_page_with_a_parent()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $data = $this->validData([
                'parent_id' => factory(Page::class)->create()->id,
            ])
        );

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'parent_id' => $data['parent_id'],
                ],
            ]);
    }

    /** @test */
    public function it_will_auto_generate_the_slug_if_its_not_provided()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $data = $this->validData([
                'title' => 'Example title',
                'slug' => null,
            ])
        );

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'title' => 'Example title',
                    'slug' => 'example-title',
                ],
            ]);
    }

    /** @test */
    public function it_requires_specific_fields_to_be_present_and_not_empty()
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            [] // Don't send any data...
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'template_id',
                'is_standalone',
                'is_published',
            ]);
    }

    /** @test */
    public function the_template_id_field_must_be_the_id_of_a_registered_template()
    {
        // Empty the registered templates...
        PageTemplates::register([]);

        $response = $this->postJson(
            route('admin.api.pages.store'),
            $this->validData([
                'template_id' => 'unknown',
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'template_id',
            ]);
    }

    /** @test */
    public function the_parent_id_field_must_be_an_existing_page_id_if_not_null()
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

    /**
     * @test
     * @param mixed $nonBooleanValue
     * @dataProvider nonBooleanValues
     */
    public function the_is_standalone_field_must_be_a_boolean($nonBooleanValue)
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $this->validData([
                'is_standalone' => $nonBooleanValue,
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'is_standalone',
            ]);
    }

    /**
     * @test
     * @param mixed $nonBooleanValue
     * @dataProvider nonBooleanValues
     */
    public function the_is_published_field_must_be_a_boolean($nonBooleanValue)
    {
        $response = $this->postJson(
            route('admin.api.pages.store'),
            $this->validData([
                'is_published' => $nonBooleanValue,
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'is_published',
            ]);
    }

    public function nonBooleanValues()
    {
        return [
            [99],
            [3.14],
            ['jabroni'],
            [[]],
            [new stdClass()],
        ];
    }

    protected function validData(array $overrides = [])
    {
        return array_merge([
            'title' => "Here's your title, jabroni.",
            'slug' => 'jabroni',
            'template_id' => $this->defaultTemplate::getId(),
            'template_data' => [
                'one' => 'One!',
            ],
            'parent_id' => null,
            'is_standalone' => false,
            'is_published' => true,
        ], $overrides);
    }
}
