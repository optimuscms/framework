<?php

namespace OptimusCMS\Pages\Rules;

use Illuminate\Contracts\Validation\Rule;
use OptimusCMS\Pages\PageTemplates;

class ValidPageTemplate implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return PageTemplates::exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.exists');
    }
}
