<?php

namespace OptimusCMS\Pages\Contracts;

use Illuminate\Http\Response;
use OptimusCMS\Pages\Models\Page;
use Illuminate\Validation\ValidationException;

interface Template
{
    /**
     * Validate the template data.
     *
     * @param array $data
     * @return void
     *
     * @throws ValidationException
     */
    public function validate(array $data);

    /**
     * Save the template data to the page.
     *
     * @param Page $page
     * @param array $data
     * @return void
     */
    public function save(Page $page, array $data);

    /**
     * Render the page template.
     *
     * @param Page $page
     * @return Response
     */
    public function render(Page $page);

    /**
     * Cast the template data to an array.
     *
     * @param Page $page
     * @return array
     */
    public function toArray(Page $page);
}
