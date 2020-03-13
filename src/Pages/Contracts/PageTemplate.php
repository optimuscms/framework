<?php

namespace OptimusCMS\Pages\Contracts;

use OptimusCMS\Pages\Models\Page;

interface PageTemplate
{
    /**
     * Get the template's id.
     *
     * @return string
     */
    public static function getId(): string;

    /**
     * Get the template's name.
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * Get the template's meta data.
     *
     * @return array
     */
    public static function getMeta(): array;

    /**
     * Validate the template data.
     *
     * @param array $data
     * @return mixed
     */
    public static function validateData(array $data);

    /**
     * Save the template data to the page.
     *
     * @param Page $page
     * @param array $data
     * @return void
     */
    public static function saveData(Page $page, array $data);

    /**
     * Delete the template data from the page.
     *
     * @param Page $page
     * @return mixed
     */
    public static function resetData(Page $page);

    /**
     * Get the template data.
     *
     * @param Page $page
     * @return array
     */
    public static function getData(Page $page): array;

    /**
     * Render the template.
     *
     * @param Page $page
     * @return mixed
     */
    public static function render(Page $page);
}
