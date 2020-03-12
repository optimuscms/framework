<?php

namespace OptimusCMS\Tests\Pages\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Tests\Pages\TestCase;

class DeletePagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_specific_page()
    {
        $page = factory(Page::class)->create();

        $response = $this->deleteJson(
            route('admin.api.pages.destroy', $page->id)
        );

        $response->assertNoContent();

        $this->assertFalse(
            Page::whereKey($page->id)->exists()
        );
    }
}
