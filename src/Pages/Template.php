<?php

namespace OptimusCMS\Pages;

use Illuminate\Http\Request;
use OptimusCMS\Pages\Models\Page;

abstract class Template
{
    /**
     * Get the template's name.
     *
     * @return string
     */
    abstract public function name(): string;

    /**
     * Get the template's label.
     *
     * @return string
     */
    public function label(): string
    {
        return ucfirst(str_replace('-', ' ', $this->name()));
    }

    /**
     * Validate the request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    abstract public function validate(Request $request);

    /**
     * Save the request data to the page.
     *
     * @param  \OptimusCMS\Pages\Models\Page  $page
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    abstract public function save(Page $page, Request $request);

    /**
     * Cast the template to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name(),
            'label' => $this->label()
        ];
    }
}
