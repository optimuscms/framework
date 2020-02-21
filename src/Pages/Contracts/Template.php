<?php

namespace OptimusCMS\Pages\Contracts;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use OptimusCMS\Pages\Models\Page;

interface Template
{
    public static function getId(): string;

    public static function getName(): string;

    public function validate(array $data);

    public function save(Page $page, array $data);

    public function reset(Page $page);

    public function render(Page $page);

    public function toArray(Page $page): array; // Todo: Rename?
}
