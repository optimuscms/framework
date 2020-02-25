<?php

namespace OptimusCMS\Pages;

use InvalidArgumentException;
use OptimusCMS\Pages\Contracts\PageTemplate;

class PageTemplates
{
    /** @var array */
    protected static $templates = [];

    /**
     * @param mixed $templates
     * @return void
     */
    public static function register($templates)
    {
        if (is_array($templates)) {
            return self::registerMany($templates);
        }

        self::registerOne($templates);
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        return array_values(self::$templates);
    }

    /**
     * @return array
     */
    public static function loadAll()
    {
        return array_map(
            function ($template) {
                return self::resolveFromContainer($template);
            },
            self::getAll()
        );
    }

    /**
     * @param string $id
     * @return string
     */
    public static function get(string $id)
    {
        if (! self::exists($id)) {
            throw new InvalidArgumentException();
        }

        return self::$templates[$id];
    }

    /**
     * @param string $id
     * @return PageTemplate
     */
    public static function load(string $id)
    {
        return self::resolveFromContainer(self::get($id));
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function exists(string $id)
    {
        return isset(self::$templates[$id]);
    }

    /**
     * @param array $templates
     * @return void
     */
    protected static function registerMany(array $templates)
    {
        foreach ($templates as $template) {
            self::registerOne($template);
        }
    }

    /**
     * @param mixed $template
     * @return void
     */
    protected static function registerOne($template)
    {
        if (! self::verify($template)) {
            throw new InvalidArgumentException();
        }

        if ($template instanceof PageTemplate) {
            $template = get_class($template);
        }

        self::$templates[$template::getId()] = $template;
    }

    /**
     * @param string $template
     * @return bool
     */
    protected static function verify($template)
    {
        return $template instanceof PageTemplate
            || (
                is_string($template)
                && is_subclass_of($template, PageTemplate::class, true)
            );
    }

    /**
     * @param string $template
     * @return PageTemplate
     */
    protected static function resolveFromContainer(string $template)
    {
        return app()->get($template);
    }
}
