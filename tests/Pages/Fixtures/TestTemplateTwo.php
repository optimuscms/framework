<?php

namespace OptimusCMS\Tests\Pages\Fixtures;

use OptimusCMS\Pages\Contracts\PageTemplate;
use OptimusCMS\Pages\Models\Page;

class TestTemplateTwo implements PageTemplate
{
    public static function getId(): string
    {
        return 'two';
    }

    public static function getName(): string
    {
        return 'Two';
    }

    public static function getMeta(): array
    {
        return [
            'two' => true,
        ];
    }

    public static function validateData(array $data)
    {
        validator($data, [
            'two' => 'required|string',
        ])->validate();
    }

    public static function saveData(Page $page, array $data)
    {
        $page->addContents([
            'two' => $data['two'],
        ]);
    }

    public static function resetData(Page $page)
    {
        $page->clearContents();
    }

    public static function getData(Page $page): array
    {
        return [
            'two' => $page->getContent('two'),
        ];
    }

    public static function render(Page $page)
    {
        //
    }
}
