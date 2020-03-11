<?php

namespace OptimusCMS\Tests\Pages;

use OptimusCMS\Pages\PageTemplates;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateOne;
use OptimusCMS\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    const DEFAULT_TEMPLATE = TestTemplateOne::class;

    protected $defaultTemplate = self::DEFAULT_TEMPLATE;

    protected function setUp(): void
    {
        parent::setUp();

        PageTemplates::register([
            $this->defaultTemplate,
        ]);
    }
}
