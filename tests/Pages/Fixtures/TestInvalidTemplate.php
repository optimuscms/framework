<?php

namespace OptimusCMS\Tests\Pages\Fixtures;

use OptimusCMS\Pages\Models\Page;

class TestInvalidTemplate // Does not implement interface...
{
    public static function getId(): string
    {
        return 'invalid';
    }

    public static function getName(): string
    {
        return 'Invalid';
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
        return [];
    }

    public static function render(Page $page)
    {
        //
    }
}
