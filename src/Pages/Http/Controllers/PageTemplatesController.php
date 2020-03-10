<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Illuminate\Routing\Controller;
use OptimusCMS\Pages\Exceptions\PageTemplateNotFoundException;
use OptimusCMS\Pages\Http\Resources\PageTemplateResource;
use OptimusCMS\Pages\PageTemplates;

class PageTemplatesController extends Controller
{
    public function index()
    {
        $templates = collect(PageTemplates::all());

        return PageTemplateResource::collection($templates);
    }

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
