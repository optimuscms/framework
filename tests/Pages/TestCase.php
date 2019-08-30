<?php

namespace OptimusCMS\Tests\Pages;

use Mockery;
use OptimusCMS\Pages\Template;
use OptimusCMS\Pages\TemplateRegistry;
use OptimusCMS\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function registerTemplate(Template $template)
    {
        $this->app[TemplateRegistry::class]->register($template);
    }

    protected function registerTemplates(array $templates)
    {
        $this->app[TemplateRegistry::class]->registerMany($templates);
    }

    protected function mockTemplate(string $name)
    {
        $template = Mockery::mock(Template::class);

        $template->shouldReceive('name')->andReturn($name);

        return $template;
    }

    public function expectedPageJsonStructure()
    {
        return [
            'id',
            'title',
            'slug',
            'uri',
            'has_fixed_uri',
            'parent_id',
            'template',
            'has_fixed_template',
            'contents' => [
                '*' => [
                    'key',
                    'value',
                ],
            ],
            'media' => [],
            'is_stand_alone',
            'is_published',
            'is_deletable',
            'created_at',
            'updated_at',
        ];
    }
}
