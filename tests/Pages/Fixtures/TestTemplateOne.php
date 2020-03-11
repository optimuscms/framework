<?php

namespace OptimusCMS\Tests\Pages\Fixtures;

use OptimusCMS\Pages\Contracts\PageTemplate;
use OptimusCMS\Pages\Models\Page;

class TestTemplateOne implements PageTemplate
{
    public static function getId(): string
    {
        return 'one';
    }

    public static function getName(): string
    {
        return 'One';
    }

    public static function getMeta(): array
    {
        return [
            'one' => true,
        ];
    }

    public static function validateData(array $data)
    {
        validator($data, [
            'one' => 'required|string',
        ])->validate();
    }

    public static function saveData(Page $page, array $data)
    {
        $page->addContents([
            'one' => $data['one'],
        ]);
    }

    public static function resetData(Page $page)
    {
        $page->clearContents();
    }

    public static function getData(Page $page): array
    {
        return [
            'one' => $page->getContent('one'),
        ];
    }

    public static function render(Page $page)
    {
        //
    }
}
