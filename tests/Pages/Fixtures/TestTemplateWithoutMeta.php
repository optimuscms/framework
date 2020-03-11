<?php

namespace OptimusCMS\Tests\Pages\Fixtures;

use OptimusCMS\Pages\Contracts\PageTemplate;
use OptimusCMS\Pages\Models\Page;

class TestTemplateWithoutMeta implements PageTemplate
{
    public static function getId(): string
    {
        return 'no-meta';
    }

    public static function getName(): string
    {
        return 'No meta';
    }

    public static function getMeta(): array
    {
        return [];
    }

    public static function validateData(array $data)
    {
        //
    }

    public static function saveData(Page $page, array $data)
    {
        //
    }

    public static function resetData(Page $page)
    {
        //
    }

    public static function getData(Page $page): array
    {
        //
    }

    public static function render(Page $page)
    {
        //
    }
}
