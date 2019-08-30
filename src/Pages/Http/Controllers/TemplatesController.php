<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OptimusCMS\Pages\TemplateRegistry;

class TemplatesController extends Controller
{
    /**
     * Display a list of page templates.
     *
     * @param TemplateRegistry $templates
     * @return JsonResponse
     */
    public function index(TemplateRegistry $templates)
    {
        return response()->json([
            'data' => collect($templates->all())->map->toArray()
        ]);
    }
}
