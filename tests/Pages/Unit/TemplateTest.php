<?php

namespace OptimusCMS\Pages\Tests;

class TemplateTest extends TestCase
{
    /** @test */
    public function it_generates_a_label_from_its_name()
    {
        $template = $this->mockTemplate('a-template-name')->makePartial();

        $this->assertEquals('A template name', $template->label());
    }

    /** @test */
    public function it_can_be_converted_to_an_array()
    {
        $template = $this->mockTemplate('template')->makePartial();

        $array = $template->toArray();

        $this->assertCount(2, $array);
        $this->assertEquals($template->name(), $array['name']);
        $this->assertEquals($template->label(), $array['label']);
    }
}
