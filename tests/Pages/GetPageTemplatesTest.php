<?php

namespace OptimusCMS\Tests\Pages;

use OptimusCMS\Pages\PageTemplates;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateOne;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateThree;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateTwo;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateWithoutMeta;
use stdClass;

class GetPageTemplatesTest extends TestCase
{
    /** @test */
    public function it_can_output_all_the_page_templates()
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
                        'meta' => $three::getMeta(),
                    ],
                    [
                        'id' => $one::getId(),
                        'name' => $one::getName(),
                        'meta' => $one::getMeta(),
                    ],
                    [
                        'id' => $two::getId(),
                        'name' => $two::getName(),
                        'meta' => $two::getMeta(),
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_output_a_specific_page_template()
    {
        PageTemplates::register([
            TestTemplateThree::class,
            $template = TestTemplateOne::class,
            TestTemplateTwo::class,
        ]);

        $response = $this->getJson(
            route('admin.api.page-templates.show', $template::getId())
        );

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $template::getId(),
                    'name' => $template::getName(),
                    'meta' => $template::getMeta(),
                ],
            ]);
    }

    /** @test */
    public function it_will_always_encode_meta_as_an_object_when_outputting_all_the_page_templates()
    {
        PageTemplates::register([
            $template = TestTemplateWithoutMeta::class,
        ]);

        $meta = $template::getMeta();

        $this->assertIsArray($meta);
        $this->assertEmpty($meta);

        $response = $this->getJson(
            route('admin.api.page-templates.index')
        );

        $response->assertOk();

        $decoded = json_decode($response->getContent());

        $this->assertTrue(isset($decoded->data[0]->meta));
        $this->assertInstanceOf(stdClass::class, $decoded->data[0]->meta);
    }

    /** @test */
    public function it_will_always_encode_meta_as_an_object_when_outputting_a_specific_page_template()
    {
        PageTemplates::register([
            $template = TestTemplateWithoutMeta::class,
        ]);

        $meta = $template::getMeta();

        $this->assertIsArray($meta);
        $this->assertEmpty($meta);

        $response = $this->getJson(
            route('admin.api.page-templates.show', $template::getId())
        );

        $response->assertOk();

        $decoded = json_decode($response->getContent());

        $this->assertTrue(isset($decoded->data->meta));
        $this->assertInstanceOf(stdClass::class, $decoded->data->meta);
    }
}
