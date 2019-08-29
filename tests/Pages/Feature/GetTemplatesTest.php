<?php

namespace OptimusCMS\Pages\Tests\Feature;

use OptimusCMS\Tests\Pages\TestCase;

class GetTemplatesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function it_can_display_all_templates_in_the_correct_order()
    {
        // Mock three selectable templates...
        $templateOne = $this->mockTemplate('one')->makePartial();
        $templateTwo = $this->mockTemplate('two')->makePartial();
        $templateThree = $this->mockTemplate('three')->makePartial();

        // Register the templates...
        $this->registerTemplates([
            $templateOne,
            $templateTwo,
            $templateThree
        ]);

        $response = $this->getJson(
            route('admin.api.page-templates.index')
        );

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'label'
                    ]
                ]
            ])
            ->assertJson([
                'data' => [
                    ['name' => $templateOne->name()],
                    ['name' => $templateTwo->name()],
                    ['name' => $templateThree->name()]
                ]
            ]);
    }
}
