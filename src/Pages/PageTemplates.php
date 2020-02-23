<?php

namespace OptimusCMS\Pages;

use InvalidArgumentException;
use OptimusCMS\Pages\Contracts\PageTemplate;

class PageTemplates
{
    protected static $templates = [];

    public static function register($templates)
    {
        if (is_array($templates)) {
            return self::registerMany($templates);
        }

        if (
            is_string($templates)
            || $templates instanceof PageTemplate
        ) {
            return self::registerOne($templates);
        }

        throw new InvalidArgumentException(
            'The given page template type is invalid.'
        );
    }

    public static function registerMany(array $templates)
    {
        foreach ($templates as $template) {
            self::registerOne($template);
        }
    }

    public static function registerOne($template)
    {
        if ($template instanceof PageTemplate) {
            $template = get_class($template);
        } elseif (
            ! is_string($template)
            || ! is_subclass_of($template, PageTemplate::class, true)
        ) {
            throw new InvalidArgumentException(
                'The given page template type is invalid.'
            );
        }

        self::$templates[$template::id()] = $template;
    }

    public static function all()
    {
        return array_values(self::$templates);
    }

    public static function loadAll()
    {
        $templates = [];

        foreach (self::all() as $template) {
            $templates[] = self::resolveFromContainer($template);
        }

        return $templates;
    }

    public static function get(string $id)
    {
        if (! self::exists($id)) {
            throw new InvalidArgumentException(
                "A page template with the id [{$id}] has not been registered."
            );
        }

        return self::$templates[$id];
    }

    public static function load($id)
    {
        return self::resolveFromContainer(self::get($id));
    }

    public static function exists(string $id)
    {
        return isset(self::$templates[$id]);
    }

    protected static function resolveFromContainer(string $template)
    {
        return app()->get($template);
    }
}
