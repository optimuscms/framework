<?php

namespace OptimusCMS\Tests\Pages\Feature;

use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Tests\Pages\TestCase;
use OptimusCMS\Tests\Pages\DummyTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetPagesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();

        $this->registerTemplate(DummyTemplate::class);
    }

    /** @test */
    public function it_can_display_all_pages_in_the_correct_order()
    {
        $secondPage = factory(Page::class)->withoutEvents()->create(['order' => 2]);
        $firstPage = factory(Page::class)->withoutEvents()->create(['order' => 1]);
        $thirdPage = factory(Page::class)->withoutEvents()->create(['order' => 3]);

        $response = $this->getJson(
            route('admin.api.pages.index')
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedPageJsonStructure(),
                ],
            ])
            ->assertJson([
                'data' => [
                    ['id' => $firstPage->id],
                    ['id' => $secondPage->id],
                    ['id' => $thirdPage->id],
                ],
            ]);
    }

    /** @test */
    public function it_can_filter_pages_by_their_parent()
    {
        $parentPage = factory(Page::class)->create();
        $childPages = factory(Page::class, 2)->create([
            'parent_id' => $parentPage->id,
        ]);

        $response = $this->getJson(
            route('admin.api.pages.index')."?parent={$parentPage->id}"
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedPageJsonStructure(),
                ],
            ]);

        $ids = $response->decodeResponseJson('data.*.id');

        $childPages->each(function (Page $page) use ($ids) {
            $this->assertContains($page->id, $ids);
        });
    }

    /** @test */
    public function it_can_display_a_specific_page()
    {
        $page = factory(Page::class)->create()->fresh();

        $page->addContent('heading', $heading = 'Heading!');

        $response = $this->getJson(
            route('admin.api.pages.show', $page->id)
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedPageJsonStructure(),
            ])
            ->assertJson([
                'data' =>[
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'path' => $page->path,
                    'has_fixed_path' => $page->has_fixed_path,
                    'parent_id' => $page->parent_id,
                    'template' => [
                        'name' => $page->template_name,
                        'data' => [
                            'heading' => $heading,
                        ],
                        'is_fixed' => $page->has_fixed_template,
                    ],
                    'is_standalone' => $page->is_standalone,
                    'is_published' => $page->isPublished(),
                    'is_deletable' => $page->is_deletable,
                    'created_at' => (string) $page->created_at,
                    'updated_at' => (string) $page->updated_at,
                ],
            ]);
    }
}
