<?php

namespace OptimusCMS\Tests\Pages\Unit;

use InvalidArgumentException;
use OptimusCMS\Tests\Pages\TestCase;
use OptimusCMS\Pages\TemplateRegistry;

class TemplateRegistryTest extends TestCase
{
    protected $templates;

    public function setUp(): void
    {
        $this->templates = new TemplateRegistry();
    }

    /** @test */
    public function it_can_register_templates()
    {
        $templateOne = $this->mockTemplate('one');
        $templateTwo = $this->mockTemplate('two');

        $this->templates->register($templateOne);
        $this->assertCount(1, $this->templates->all());

        $this->templates->register($templateTwo);
        $this->assertCount(2, $templates = $this->templates->all());

        $this->assertSame($templateOne, array_shift($templates));
        $this->assertSame($templateTwo, array_shift($templates));
    }

    /** @test */
    public function it_can_register_multiple_templates_at_once()
    {
        $templateOne = $this->mockTemplate('one');
        $templateTwo = $this->mockTemplate('two');

        $this->templates->registerMany([
            $templateOne,
            $templateTwo,
        ]);

        $templates = $this->templates->all();

        $this->assertCount(2, $templates);

        $this->assertSame($templateOne, array_shift($templates));
        $this->assertSame($templateTwo, array_shift($templates));
    }

    /** @test */
    public function it_can_find_the_first_template_with_a_given_name()
    {
        $templateOne = $this->mockTemplate('one');
        $templateTwo = $this->mockTemplate('two');

        $this->templates->registerMany([
            $templateOne,
            $templateTwo,
        ]);

        $this->assertSame($templateTwo, $this->templates->find('two'));
        $this->assertSame($templateOne, $this->templates->find('one'));
    }

    /** @test */
    public function it_will_throw_an_exception_when_template_not_found()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->templates->find('unregistered');
    }
}
