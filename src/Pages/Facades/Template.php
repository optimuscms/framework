<?php

namespace OptimusCMS\Pages\Facades;

use Illuminate\Support\Facades\Facade;
use OptimusCMS\Pages\TemplateRegistry;

class Template extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return TemplateRegistry::class;
    }
}
