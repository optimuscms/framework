<?php

namespace OptimusCMS\Tests\Pages\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Tests\Pages\TestCase;

class GetPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_output_all_the_pages_in_the_correct_order()
    {
        $secondPage = factory(Page::class)->withoutEvents()->create([
            'order' => 2,
            'published_at' => now(),
        ]);

        $firstPage = factory(Page::class)->withoutEvents()->create([
            'order' => 1,
            'published_at' => null,
        ]);

        $thirdPage = factory(Page::class)->withoutEvents()->create([
            'order' => 3,
            'published_at' => now(),
        ]);

        $response = $this->getJson(
            route('admin.api.pages.index')
        );

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedJsonStructure([
                        'children_count',
                    ]),
                ],
            ]);

        $response->assertJson([
            'data' => [
                ['id' => $firstPage->id],
                ['id' => $secondPage->id],
                ['id' => $thirdPage->id],
            ],
        ]);
    }

    /** @test */
    public function it_can_output_all_the_root_pages()
    {
        $rootPages = factory(Page::class, 2)->create();

        // Create a child page...
        factory(Page::class)->create([
            'parent_id' => $rootPages->first()->id,
        ]);

        $response = $this->getJson(
            route('admin.api.pages.index', [
                'parent' => 'root',
            ])
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedJsonStructure([
                        'children_count',
                    ]),
                ],
            ]);

        $ids = $response->decodeResponseJson('data.*.id');

        // Only root pages should be included in the response...
        $rootPages->each(function (Page $rootPage) use ($ids) {
            $this->assertTrue(
                in_array($rootPage->id, $ids)
            );
        });
    }

    /** @test */
    public function it_can_output_all_the_pages_with_a_given_parent()
    {
        $rootPage = factory(Page::class)->create();

        $childPages = factory(Page::class, 2)->create([
            'parent_id' => $rootPage->first()->id,
        ]);

        // Create other child pages...
        factory(Page::class)->create([
            'parent_id' => factory(Page::class)->create()->id,
        ]);

        $response = $this->getJson(
            route('admin.api.pages.index', [
                'parent' => $rootPage->id,
            ])
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedJsonStructure(),
                ],
            ]);

        $ids = $response->decodeResponseJson('data.*.id');

        // Only child pages should be included in the response...
        $childPages->each(function (Page $childPage) use ($ids) {
            $this->assertTrue(
                in_array($childPage->id, $ids)
            );
        });
    }

    /** @test */
    public function it_can_output_a_specific_page()
    {
        $page = factory(Page::class)->create();

        $response = $this->getJson(
            route('admin.api.pages.show', $page->id)
        );

        $template = $this->defaultTemplate;

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedJsonStructure([
                    'template_data' => [
                        'one',
                    ],
                ]),
            ])
            ->assertJson([
                'data' => [
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'path' => $page->path,
                    'has_fixed_path' => $page->has_fixed_path,
                    'parent_id' => $page->parent_id,
                    'template_id' => $template::getId(),
                    'template_name' => $template::getName(),
                    'template_data' => $template::getData($page),
                    'has_fixed_template' => $page->has_fixed_template,
                    'is_standalone' => $page->is_standalone,
                    'is_deletable' => $page->is_deletable,
                    'is_published' => $page->isPublished(),
                    'created_at' => (string) $page->created_at,
                    'updated_at' => (string) $page->updated_at,
                ],
            ]);
    }
}
