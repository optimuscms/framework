<?php

namespace OptimusCMS\Pages\Facades;

use OptimusCMS\Pages\TemplateRegistry;

class Template
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public function getFacadeAccessor()
    {
        return TemplateRegistry::class;
    }
}
