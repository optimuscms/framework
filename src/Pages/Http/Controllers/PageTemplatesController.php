<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use OptimusCMS\Pages\TemplateRegistry;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OptimusCMS\Pages\Http\Resources\PageTemplateResource;

class PageTemplatesController extends Controller
{
    /** @var TemplateRegistry */
    protected $templateRegistry;

    public function __construct(TemplateRegistry $templateRegistry)
    {
        $this->templateRegistry = $templateRegistry;
    }

    /**
     * Display a list of page templates.
     *
     * @return ResourceCollection
     */
    public function index()
    {
        $templates = new Collection($this->templateRegistry->all());

        return PageTemplateResource::collection($templates);
    }

    /**
     * Display the specified page template.
     *
     * @param string $name
     * @return PageTemplateResource
     */
    public function show($name)
    {
        try {
            $template = $this->templateRegistry->get($name);
        } catch (Exception $exception) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return new PageTemplateResource($template);
    }
}
