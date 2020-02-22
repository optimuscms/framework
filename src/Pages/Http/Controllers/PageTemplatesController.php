<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use OptimusCMS\Pages\Http\Resources\PageTemplateResource;
use OptimusCMS\Pages\PageTemplates;

class PageTemplatesController extends Controller
{
    public function index()
    {
        $templates = new Collection(PageTemplates::all());

        return PageTemplateResource::collection($templates);
    }

    public function show($templateId)
    {
        try {
            $template = PageTemplates::get($templateId);
        } catch (Exception $exception) {
            abort(Response::HTTP_NOT_FOUND);

            // Todo: Abort with error message.
        }

        return new PageTemplateResource($template);
    }
}
