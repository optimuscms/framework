<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use OptimusCMS\Pages\Http\Resources\PageTemplateResource;
use OptimusCMS\Pages\PageTemplates;

class PageTemplatesController extends Controller
{
    public function index()
    {
        $templates = collect(PageTemplates::getAll());

        return PageTemplateResource::collection($templates);
    }

    public function show($templateId)
    {
        if (! PageTemplates::exists($templateId)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $template = PageTemplates::get($templateId);

        return new PageTemplateResource($template);
    }
}
