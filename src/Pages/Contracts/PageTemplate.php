<?php

namespace OptimusCMS\Pages\Contracts;

use OptimusCMS\Pages\Models\Page;

interface PageTemplate
{
    public static function getId(): string;

    public static function getName(): string;

    public static function getMeta(): array;

    public static function validateData(array $data);

    public static function saveData(Page $page, array $data);

    public static function resetData(Page $page);

    public static function getData(Page $page): array;

    public static function render(Page $page);
}
