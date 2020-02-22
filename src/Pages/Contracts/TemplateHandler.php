<?php

namespace OptimusCMS\Pages\Contracts;

use OptimusCMS\Pages\Models\Page;

interface TemplateHandler
{
    public static function id(): string;

    public static function name(): string;

    public function validate(array $data);

    public function save(Page $page, array $data);

    public function reset(Page $page);

    public function render(Page $page);

    public function toArray(Page $page): array; // Todo: Rename?
}