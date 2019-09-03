<?php

namespace OptimusCMS\Pages;

use InvalidArgumentException;
use Illuminate\Container\Container;
use OptimusCMS\Pages\Contracts\Template as TemplateContract;
use Illuminate\Contracts\Container\BindingResolutionException;

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
     */
    public function register(string $templateClass)
    {
        // Throw an error if the given string is not a class, or if it does not
        // implement the template interface...
        if (! is_a($templateClass, $templateContract = TemplateContract::class)) {
            throw new InvalidArgumentException(
                "The [{$templateClass}] class must exist and implement the [{$templateContract}] interface."
            );
        }

        // Throw an error if the required properties are
        // not set on the template class...
        foreach (['name', 'label'] as $requiredProperty) {
            if (! (
                property_exists($templateClass, $requiredProperty)
                && is_string($templateClass::$$requiredProperty)
            )) {
                throw new InvalidArgumentException(
                    "The [{$templateClass}] must have a valid {$requiredProperty} property."
                );
            }
        }

        $this->templateClasses[$templateClass::$name] = $templateClass;
    }

    /**
     * Register multiple template classes.
     *
     * @param array $templateClasses
     * @return void
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
                return $this->app->make($templateClass);
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
     * @throws InvalidArgumentException
     */
    public function get(string $name)
    {
        if (! $this->exists($name)) {
            throw new InvalidArgumentException(
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
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    public function load(string $name)
    {
        $templateClass = $this->get($name);

        return $this->app->make($templateClass);
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
