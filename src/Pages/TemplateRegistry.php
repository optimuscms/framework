<?php

namespace OptimusCMS\Pages;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class TemplateRegistry
{
    /**
     * @var Template[]
     */
    protected $templates = [];

    /**
     * Create a new TemplateRegistry instance.
     *
     * @param  array  $templates
     * @return void
     */
    public function __construct(array $templates = [])
    {
        $this->registerMany($templates);
    }

    /**
     * Get all the registered templates.
     *
     * @return Template[]
     */
    public function all()
    {
        return $this->templates;
    }

    /**
     * Get the template with the given name.
     *
     * @throws InvalidArgumentException
     *
     * @param string $name
     * @return Template
     */
    public function find(string $name)
    {
        $template = Arr::first(
            $this->all(), function (Template $template) use ($name) {
                return $name === $template->name();
            }
        );

        if (! $template) {
            throw new InvalidArgumentException(
                "A template with the name `{$name}` has not been registered."
            );
        }

        return $template;
    }

    /**
     * Register a template class.
     *
     * @param Template $template
     * @return void
     */
    public function register(Template $template)
    {
        $this->templates[] = $template;
    }

    /**
     * Register multiple template classes.
     *
     * @param array $templates
     * @return void
     */
    public function registerMany(array $templates)
    {
        foreach ($templates as $template) {
            $this->register($template);
        }
    }
}
