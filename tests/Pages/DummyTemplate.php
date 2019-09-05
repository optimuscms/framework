<?php

namespace OptimusCMS\Tests\Pages;

use Illuminate\Http\Response;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Pages\Contracts\Template;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DummyTemplate implements Template
{
    /**
     * Get the template's name.
     *
     * @return string
     */
    public static function name(): string
    {
        return 'dummy';
    }

    /**
     * Get the template's label.
     *
     * @return string
     */
    public static function label(): string
    {
        return 'Dummy';
    }

    /**
     * Validate the template data.
     *
     * @param array $data
     * @return void
     *
     * @throws ValidationException
     */
    public function validate(array $data)
    {
        Validator::make($data, [
            'heading' => 'required|string|max:255',
        ])->validate();
    }

    /**
     * Save the template data to the page.
     *
     * @param Page $page
     * @param array $data
     * @return void
     */
    public function save(Page $page, array $data)
    {
        $page->addContent('heading', $data['heading']);
    }

    /**
     * Reset the template to its original state.
     *
     * @param Page $page
     * @return void
     */
    public function reset(Page $page)
    {
        $page->clearContents();
    }

    /**
     * Render the template.
     *
     * @param Page $page
     * @return Response
     */
    public function render(Page $page)
    {
        return response('Hello world!');
    }

    /**
     * Transform the template data into an array.
     *
     * @param Page $page
     * @return array
     */
    public function toArray(Page $page): array
    {
        return [
            'heading' => $page->getContent('heading')
        ];
    }
}
