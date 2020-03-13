<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;
use OptimusCMS\Pages\Exceptions\PageTemplateNotFoundException;
use OptimusCMS\Pages\Http\Resources\PageTemplateResource;
use OptimusCMS\Pages\PageTemplates;

class PageTemplatesController extends Controller
{
    /**
     * Display all the registered page templates.
     *
     * @return ResourceCollection
     */
    public function index()
    {
        $templates = collect(PageTemplates::all());

        return PageTemplateResource::collection($templates);
    }

    /**
     * Display the specified page template.
     *
     * @param $templateId
     * @return PageTemplateResource
     */
    public function show($templateId)
    {
        try {
            $template = PageTemplates::get($templateId);
        } catch (PageTemplateNotFoundException $exception) {
            abort(404, $exception->getMessage());
        }

        return new PageTemplateResource($template);
    }
}
