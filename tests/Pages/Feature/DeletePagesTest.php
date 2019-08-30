<?php

namespace OptimusCMS\Tests\Pages\Feature;

use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Tests\Pages\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeletePagesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function it_can_delete_a_page()
    {
        $page = factory(Page::class)->create([
            'is_deletable' => true,
        ]);

        $response = $this->deleteJson(
            route('admin.api.pages.destroy', [
                $page->id,
            ])
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing($page->getTable(), [
            'id' => $page->id,
        ]);
    }

    /** @test */
    public function it_can_only_delete_deletable_pages()
    {
        $page = factory(Page::class)->create([
            'is_deletable' => false,
        ]);

        $response = $this->deleteJson(
            route('admin.api.pages.destroy', [
                'id' => $page->id,
            ])
        );

        $response->assertStatus(403);

        $this->assertDatabaseHas($page->getTable(), [
            'id' => $page->id,
        ]);
    }
}
