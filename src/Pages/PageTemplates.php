<?php

namespace OptimusCMS\Pages;

use Illuminate\Contracts\Support\Arrayable;
use OptimusCMS\Pages\Contracts\PageTemplate;
use OptimusCMS\Pages\Exceptions\InvalidPageTemplateException;
use OptimusCMS\Pages\Exceptions\PageTemplateNotFoundException;

class PageTemplates
{
    protected static $templates = [];

    public static function register($templates)
    {
        if ($templates instanceof Arrayable) {
            $templates = $templates->toArray();
        }

        if (is_array($templates)) {
            self::registerMany($templates);

            return;
        }

        self::registerOne($templates);
    }

    protected static function registerMany(array $templates)
    {
        foreach ($templates as $i => $template) {
            try {
                self::registerOne($template);
            } catch (InvalidPageTemplateException $exception) {
                // Override the exception message...
                throw new InvalidPageTemplateException(
                    "The page template given at index [{$i}] is invalid."
                );
            }
        }
    }

    protected static function registerOne($template)
    {
        if ($template instanceof PageTemplate) {
            $template = get_class($template);
        } elseif (! (
            // Determine if the given template is valid...
            is_string($template)
            && is_subclass_of($template, PageTemplate::class, true)
        )) {
            throw new InvalidPageTemplateException(
                'The given page template is invalid.'
            );
        }

        self::$templates[$template::getId()] = $template;
    }

    public static function all()
    {
        return array_values(self::$templates);
    }

    public static function get(string $id)
    {
        if (! self::exists($id)) {
            throw new PageTemplateNotFoundException(
                "A page template with the id [{$id}] does not exist."
            );
        }

        return self::$templates[$id];
    }

    public static function exists(string $id)
    {
        return isset(self::$templates[$id]);
    }
}
