<?php

namespace OptimusCMS\Pages;

use Illuminate\Container\Container;
use OptimusCMS\Pages\Exceptions\InvalidTemplateException;
use OptimusCMS\Pages\Exceptions\TemplateNotFoundException;
use OptimusCMS\Pages\Contracts\Template as TemplateContract;

class TemplateRegistry
{
    /** @var Container */
    protected $app;

    /** @var array */
    protected $templateClasses = [];

    /**
     * Create a new registry instance.
     *
     * @param Container $app
     * @param array $templateClasses
     * @return void
     *
     * @throws InvalidTemplateException
     */
    public function __construct(Container $app, array $templateClasses = [])
    {
        $this->app = $app;

        $this->registerMany($templateClasses);
    }

    /**
     * Register the given template class.
     *
     * @param string $templateClass
     * @return void
     *
     * @throws InvalidTemplateException
     */
    public function register(string $templateClass)
    {
        // Throw an error if the $templateClass argument is not a defined class...
        if (! class_exists($templateClass)) {
            throw new InvalidTemplateException(
                "The \$templateClass argument must be a defined class, you gave [{$templateClass}]."
            );
        }

        // Throw an error if the given class does not implement the template interface...
        if (! is_subclass_of(
            $templateClass, $templateContract = TemplateContract::class
        )) {
            throw new InvalidTemplateException(
                "The given [{$templateClass}] class does not implement the [{$templateContract}] interface."
            );
        }

        // Throw an error if the template's name is not defined...
        if (! $templateName = $templateClass::name()) {
            throw new InvalidTemplateException(
                "The name of the given [{$templateClass}] class is not defined."
            );
        }

        $this->templateClasses[$templateName] = $templateClass;
    }

    /**
     * Register multiple template classes.
     *
     * @param array $templateClasses
     * @return void
     *
     * @throws InvalidTemplateException
     */
    public function registerMany(array $templateClasses)
    {
        foreach ($templateClasses as $templateClass) {
            $this->register($templateClass);
        }
    }

    /**
     * Retrieve all of the registered template class names.
     *
     * @return array
     */
    public function all()
    {
        return array_values($this->templateClasses);
    }

    /**
     * Retrieve all of the registered template classes.
     *
     * @return array
     */
    public function loadAll()
    {
        return array_map(
            function ($templateClass) {
                return $this->app->get($templateClass);
            },
            $this->all()
        );
    }

    /**
     * Retrieve the specified template class name.
     *
     * @param string $name
     * @return string
     *
     * @throws TemplateNotFoundException
     */
    public function get(string $name)
    {
        if (! $this->exists($name)) {
            throw new TemplateNotFoundException(
                "A template with the name [{$name}] has not been registered."
            );
        }

        return $this->templateClasses[$name];
    }

    /**
     * Retrieve the specified template class.
     *
     * @param string $name
     * @return TemplateContract
     *
     * @throws TemplateNotFoundException
     */
    public function load(string $name)
    {
        $templateClass = $this->get($name);

        return $this->app->get($templateClass);
    }

    /**
     * Determine if a template class with the given name exists.
     *
     * @param string $name
     * @return bool
     */
    public function exists(string $name)
    {
        return isset($this->templateClasses[$name]);
    }
}
