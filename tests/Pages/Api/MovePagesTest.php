<?php

namespace OptimusCMS\Tests\Pages\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Tests\Pages\TestCase;

class MovePagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_move_a_page_up_in_the_list()
    {
        factory(Page::class)->withoutEvents()->create(['order' => 1]);
        $secondPage = factory(Page::class)->withoutEvents()->create(['order' => 2]);
        $thirdPage = factory(Page::class)->withoutEvents()->create(['order' => 3]);

        $response = $this->postJson(
            route('admin.api.pages.move', $thirdPage->id),
            ['direction' => 'up']
        );

        $response->assertNoContent();

        $this->assertEquals(2, $thirdPage->fresh()->order);
        $this->assertEquals(3, $secondPage->fresh()->order);
    }

    /** @test */
    public function it_can_move_a_page_down_in_the_list()
    {
        factory(Page::class)->withoutEvents()->create(['order' => 1]);
        $secondPage = factory(Page::class)->withoutEvents()->create(['order' => 2]);
        $thirdPage = factory(Page::class)->withoutEvents()->create(['order' => 3]);

        $response = $this->postJson(
            route('admin.api.pages.move', $secondPage->id),
            ['direction' => 'down']
        );

        $response->assertNoContent();

        $this->assertEquals(3, $secondPage->fresh()->order);
        $this->assertEquals(2, $thirdPage->fresh()->order);
    }

    /**
     * @test
     * @param mixed $invalidDirection
     * @dataProvider invalidDirections
     */
    public function the_direction_field_must_be_either_up_or_down($invalidDirection)
    {
        $page = factory(Page::class)->create();

        $response = $this->postJson(
            route('admin.api.pages.move', $page->id),
            ['direction' => $invalidDirection]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'direction',
            ]);
    }

    public function invalidDirections()
    {
        return [
            [true],
            [false],
            [1],
            [-1],
            ['left'],
            ['right'],
        ];
    }
}
