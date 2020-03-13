<?php

namespace OptimusCMS\Pages;

use OptimusCMS\Pages\Contracts\PageTemplate;
use OptimusCMS\Pages\Exceptions\InvalidPageTemplateException;
use OptimusCMS\Pages\Exceptions\PageTemplateNotFoundException;

class PageTemplates
{
    /** @var PageTemplate|string[] */
    protected static $templates = [];

    /**
     * Get all the registered page templates.
     *
     * @return PageTemplate|string[]
     */
    public static function all()
    {
        return array_values(self::$templates);
    }

    /**
     * Set the array of page templates.
     *
     * @param array $templates
     * @return void
     */
    public static function register(array $templates)
    {
        $classes = [];

        foreach ($templates as $i => $template) {
            if ($template instanceof PageTemplate) {
                $classes[$template::getId()] = get_class($template);

                continue;
            }

            if (
                is_string($template)
                && is_subclass_of($template, PageTemplate::class, true)
            ) {
                $classes[$template::getId()] = $template;

                continue;
            }

            throw new InvalidPageTemplateException(
                "The page template given at index [{$i}] is invalid."
            );
        }

        self::$templates = $classes;
    }

    /**
     * Get the specified page template.
     *
     * @param string $id
     * @return string
     */
    public static function get(string $id)
    {
        if (! self::exists($id)) {
            throw new PageTemplateNotFoundException(
                "A page template with the id [{$id}] cannot be found."
            );
        }

        return self::$templates[$id];
    }

    /**
     * Determine if the specified page template is registered.
     *
     * @param string $id
     * @return bool
     */
    public static function exists(string $id)
    {
        return isset(self::$templates[$id]);
    }
}
