<?php

namespace OptimusCMS\Tests\Pages;

use Illuminate\Database\Eloquent\FactoryBuilder;
use OptimusCMS\Pages\PageTemplates;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateOne;
use OptimusCMS\Tests\TestCase as BaseTestCase;
use stdClass;

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

        FactoryBuilder::macro('withoutEvents', function () {
            $this->class::flushEventListeners();

            return $this;
        });
    }

    protected function expectedJsonStructure(array $overrides = [])
    {
        return array_merge([
            'id',
            'title',
            'slug',
            'path',
            'has_fixed_path',
            'parent_id',
            'template_id',
            'template_name',
            'has_fixed_template',
            'is_standalone',
            'is_deletable',
            'is_published',
            'created_at',
            'updated_at',
        ], $overrides);
    }

    public function nonBooleanValues()
    {
        return [
            [99],
            [3.14],
            ['jabroni'],
            [[]],
            [new stdClass()],
        ];
    }
}
