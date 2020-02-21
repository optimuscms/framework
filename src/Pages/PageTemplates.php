<?php

namespace OptimusCMS\Pages;

use InvalidArgumentException;
use OptimusCMS\Pages\Contracts\Template;

class PageTemplates
{
    protected static $templates = [];

    public static function register($templates): void
    {
        if (is_array($templates)) {
            self::registerMany($templates);
            return;
        }

        if (
            is_string($templates)
            || $templates instanceof Template
        ) {
            self::registerOne($templates);
            return;
        }

        throw new InvalidArgumentException(
            // Todo: Message...
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
        if ($template instanceof Template) {
            $template = $template::class;
        } elseif (
            ! is_string($template)
            || ! is_subclass_of($template, Template::class, true)
        ) {
            throw new InvalidArgumentException(
                // Todo: Message...
            );
        }

        self::$templates[$template::identifier()] = $template;
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

    public static function get(string $identifier)
    {
        if (! self::exists($identifier)) {
            throw new InvalidArgumentException(
                "A template with the identifier [{$identifier}] does not exist."
            );
        }

        return self::$templates[$identifier];
    }

    public static function load($identifier)
    {
        return self::resolveFromContainer(
            self::get($identifier)
        );
    }

    protected static function resolveFromContainer(string $template)
    {
        return app()->get($template);
    }

    public static function exists(string $identifier)
    {
        return isset(self::$templates[$identifier]);
    }
}
