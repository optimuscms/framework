<?php

namespace OptimusCMS\Pages\Facades;

use OptimusCMS\Pages\TemplateRegistry;
use Illuminate\Support\Facades\Facade;

class Template extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TemplateRegistry::class;
    }
}
