<?php

namespace OptimusCMS\Pages\Rules;

use Illuminate\Contracts\Validation\Rule;
use OptimusCMS\Pages\PageTemplates;

class ValidPageTemplate implements Rule
{
    public function passes($attribute, $value)
    {
        return PageTemplates::exists($value);
    }

    public function message()
    {
        return __('validation.exists');
    }
}
