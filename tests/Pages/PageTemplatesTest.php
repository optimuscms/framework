<?php

namespace OptimusCMS\Tests\Pages;

use OptimusCMS\Pages\Exceptions\InvalidPageTemplateException;
use OptimusCMS\Pages\Exceptions\PageTemplateNotFoundException;
use OptimusCMS\Pages\PageTemplates;
use OptimusCMS\Tests\Pages\Fixtures\TestInvalidTemplate;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateOne;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateThree;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateTwo;

class PageTemplatesTest extends TestCase
{
    /** @test */
    public function it_can_register_and_retrieve_page_templates()
    {
        PageTemplates::register($expectedTemplates = [
            TestTemplateOne::class,
            TestTemplateTwo::class,
            TestTemplateThree::class,
        ]);

        $actualTemplates = PageTemplates::all();

        $this->assertIsArray($actualTemplates);
        $this->assertCount(3, $actualTemplates);
        $this->assertSame($expectedTemplates, $actualTemplates);
    }

    /**
     * @test
     * @param mixed $invalidTemplate
     * @dataProvider invalidPageTemplates
     */
    public function it_will_throw_an_exception_on_attempts_to_register_an_invalid_page_template($invalidTemplate)
    {
        $this->expectException(
            InvalidPageTemplateException::class
        );

        PageTemplates::register([
            $invalidTemplate,
        ]);
    }

    /** @test */
    public function it_can_retrieve_a_specific_page_template_from_the_registry()
    {
        PageTemplates::register([
            TestTemplateOne::class,
            $expectedTemplate = TestTemplateTwo::class,
            TestTemplateThree::class,
        ]);

        $actualTemplate = PageTemplates::get($expectedTemplate::getId());

        $this->assertSame($expectedTemplate, $actualTemplate);
    }

    /** @test */
    public function it_will_throw_an_exception_on_attempts_to_retrieve_an_unregistered_page_template()
    {
        $this->expectException(
            PageTemplateNotFoundException::class
        );

        PageTemplates::get('unknown');
    }

    /** @test */
    public function it_can_determine_if_a_page_template_has_been_registered()
    {
        PageTemplates::register([
            $one = TestTemplateOne::class,
            $two = TestTemplateTwo::class,
            $three = TestTemplateThree::class,
        ]);

        // Should be registered...
        $this->assertTrue(PageTemplates::exists($one::getId()));
        $this->assertTrue(PageTemplates::exists($two::getId()));
        $this->assertTrue(PageTemplates::exists($three::getId()));

        // Should be unregistered...
        $this->assertFalse(PageTemplates::exists('unknown'));
    }

    public function invalidPageTemplates()
    {
        return [
            [true],
            [false],
            [123],
            [3.14],
            ['not a template'],
            [TestInvalidTemplate::class],
        ];
    }
}
