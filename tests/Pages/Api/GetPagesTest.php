<?php

namespace OptimusCMS\Tests\Pages\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Tests\Pages\TestCase;

class GetPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_output_all_the_pages()
    {
        // Create 2 published pages...
        $publishedPages = factory(Page::class, 2)->create([
            'published_at' => now(),
        ]);

        // Create 2 draft pages...
        $draftPages = factory(Page::class, 2)->create([
            'published_at' => null,
        ]);

        $response = $this->getJson(
            route('admin.api.pages.index')
        );

        $response
            ->assertOk()
            ->assertJsonCount(4, 'data');

        $allPages = $publishedPages->merge($draftPages);

        $ids = $response->decodeResponseJson('data.*.id');

        // Draft pages should be included in the response...
        $allPages->each(function (Page $page) use ($ids) {
            $this->assertTrue(
                in_array($page->id, $ids)
            );
        });
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
            ->assertJsonCount(2, 'data');

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
            ->assertJsonCount(2, 'data');

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

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'template_id' => $this->defaultTemplate::getId(),
                    'template_name' => $this->defaultTemplate::getName(),
                    'template_data' => $this->defaultTemplate::getData($page),
                    'parent_id' => $page->parent_id,
                    'is_standalone' => $page->is_standalone,
                    'is_published' => $page->isPublished(),
                ],
            ]);
    }
}
