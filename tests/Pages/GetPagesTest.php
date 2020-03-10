<?php

namespace OptimusCMS\Pages\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OptimusCMS\Pages\PageTemplates;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateOne;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateThree;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateTwo;
use OptimusCMS\Tests\TestCase;

class GetPageTemplatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_output_a_list_of_page_templates()
    {
        PageTemplates::register([
            $three = TestTemplateThree::class,
            $one = TestTemplateOne::class,
            $two = TestTemplateTwo::class,
        ]);

        $response = $this->getJson(
            route('admin.api.page-templates.index')
        );

        $response
            ->assertOk()
            ->assertJson([
                // The templates should be returned in
                // the order that they were defined...
                'data' => [
                    [
                        'id' => $three::getId(),
                        'name' => $three::getName(),
                    ],
                    [
                        'id' => $one::getId(),
                        'name' => $one::getName(),
                    ],
                    [
                        'id' => $two::getId(),
                        'name' => $two::getName(),
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_output_a_specific_page_template()
    {
        //
    }
}
