<?php

namespace OptimusCMS\Pages\Contracts;

use OptimusCMS\Pages\Models\Page;

interface PageTemplate
{
    public static function getId(): string;

    public static function getName(): string;

    public static function getMeta(): array;

    public function validateData(array $data);

    public function saveData(Page $page, array $data);

    public function deleteData(Page $page);

    public function getData(Page $page): array;

    public function render(Page $page);
}
