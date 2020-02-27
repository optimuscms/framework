<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
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
        } catch (InvalidArgumentException $exception) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return new PageTemplateResource($template);
    }
}
