<?php

namespace OptimusCMS\Tests\Pages\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Pages\PageTemplates;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateOne;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateTwo;
use OptimusCMS\Tests\Pages\TestCase;

class UpdatePagesTest extends TestCase
{
    use RefreshDatabase;

    protected $parentPage;

    protected $page;

    protected function setUp(): void
    {
        parent::setUp();

        PageTemplates::register([
            $template = TestTemplateOne::class,
            TestTemplateTwo::class,
        ]);

        $this->parentPage = factory(Page::class)->create();

        $this->page = factory(Page::class)->create([
            'title' => 'Unedited title',
            'slug' => $slug = 'unedited-title',
            'path' => "{$this->parentPage->path}/{$slug}",
            'template_id' => $template::getId(),
            'parent_id' => $this->parentPage->id,
            'is_standalone' => true,
            'published_at' => now(),
        ]);
    }

    /** @test */
    public function it_can_be_update_a_specific_page()
    {
        $parentPage = factory(Page::class)->create();

        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
            $data = $this->validData([
                'parent_id' => $parentPage->id,
            ])
        );

        $template = TestTemplateTwo::class;

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $this->page->id,
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'path' => "{$parentPage->path}/{$data['slug']}",
                    'parent_id' => $parentPage->id,
                    'template_id' => $template::getId(),
                    'template_name' => $template::getName(),
                    'template_data' => $template::getData($this->page->fresh()),
                    'is_standalone' => $data['is_standalone'],
                    'is_published' => $data['is_published'],
                ],
            ]);
    }

    /** @test */
    public function it_update_a_specific_page_to_have_no_parent()
    {
        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
            $data = $this->validData([
                'parent_id' => null,
            ])
        );

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'slug' => $data['slug'],
                    'path' => $data['slug'],
                    'parent_id' => $data['parent_id'],
                ],
            ]);
    }

    /** @test */
    public function it_requires_specific_fields_to_be_present_and_not_empty()
    {
        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
            [] // Don't send any data...
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'slug',
                'template_id',
                'is_standalone',
                'is_published',
            ]);
    }

    /** @test */
    public function the_slug_field_must_be_unique()
    {
        $nonUniqueSlug = $this->parentPage->slug;

        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
            $this->validData([
                'slug' => $nonUniqueSlug,
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'slug',
            ]);
    }

    /** @test */
    public function the_slug_field_can_be_the_same_as_the_original()
    {
        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
            $data = $this->validData([
                'slug' => $this->page->slug,
            ])
        );

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'slug' => $data['slug'],
                ],
            ]);
    }

    /** @test */
    public function the_template_id_field_must_be_the_id_of_a_registered_template()
    {
        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
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
        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
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
    public function the_parent_id_field_must_not_be_the_page_being_updated()
    {
        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
            $this->validData([
                'parent_id' => $this->page->id,
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'parent_id',
            ]);
    }

    public function the_parent_id_field_must_not_be_an_ancestor_of_the_page_being_updated()
    {
        $childPage = factory(Page::class)->create([
            'parent_id' => $this->page->id,
        ]);

        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
            $this->validData([
                'parent_id' => $childPage->id,
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
        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
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
        $response = $this->patchJson(
            route('admin.api.pages.update', $this->page->id),
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

    protected function validData(array $overrides = [])
    {
        return array_merge([
            'title' => 'Updated title',
            'slug' => 'updated-title',
            'template_id' => TestTemplateTwo::getId(),
            'template_data' => [
                'two' => 'Two!',
            ],
            'parent_id' => null,
            'is_standalone' => false,
            'is_published' => true,
        ], $overrides);
    }
}
