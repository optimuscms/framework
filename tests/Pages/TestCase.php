<?php

namespace OptimusCMS\Tests\Pages;

use OptimusCMS\Tests\TestCase as BaseTestCase;
use OptimusCMS\Pages\Facades\Template as TemplateFacade;

class TestCase extends BaseTestCase
{
    protected function registerTemplate(string $templateClass)
    {
        return TemplateFacade::register($templateClass);
    }

    protected function registerTemplates(array $templateClasses)
    {
        return TemplateFacade::registerMany($templateClasses);
    }

    public function expectedPageJsonStructure()
    {
        return [
            'id',
            'title',
            'slug',
            'path',
            'has_fixed_path',
            'parent_id',
            'template' => [
                'name',
                'data' => [],
                'is_fixed',
            ],
            'is_standalone',
            'is_published',
            'is_deletable',
            'created_at',
            'updated_at',
        ];
    }
}
