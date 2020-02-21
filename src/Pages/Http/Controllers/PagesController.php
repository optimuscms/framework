<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use OptimusCMS\Meta\Models\Meta;
use OptimusCMS\Pages\Exceptions\TemplateNotFoundException;
use OptimusCMS\Pages\Http\Resources\PageResource;
use OptimusCMS\Pages\Jobs\UpdatePagePath;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Pages\PageTemplates;
use OptimusCMS\Pages\TemplateRegistry;

class PagesController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::withDrafts()
            ->applyFilters($request->all())
            ->withCount('children')
            ->with('contents')
            ->ordered()
            ->get();

        return PageResource::collection($pages);
    }

    public function store(Request $request)
    {
        $this->validatePage($request);

        // page
        //   title
        //   slug
        //   parent_id
        //   template_id
        //   template_data
        //   is_standalone
        //   is_published

        $template = PageTemplates::load(
            $templateId = $request->input('template_id')
        );

        $template->validate(
            $templateData = $request->input('template_data')
        );

        $page = new Page([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'template_id' => $templateId,
            'parent_id' => $request->input('parent_id'),
            'is_standalone' => $request->input('is_standalone'),
        ]);

        $page->has_fixed_path = false;
        $page->has_fixed_template = false;
        $page->is_deletable = true;

        $page->save();

        $page->saveMeta(
            $request->input('meta', [])
        );

        UpdatePagePath::dispatch($page);

        $template->save($page, $templateData);

        if ($request->input('is_published')) {
            $page->publish();
        }

        return new PageResource($page);
    }

    public function show($id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        return new PageResource($page);
    }

    public function update(Request $request, $id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        $this->validatePage($request);

        // If the page template is fixed, load the page's
        // current template - otherwise load the template
        // specified in the request data...
        $template = $this->templateRegistry->load(
            $templateName = ! $page->has_fixed_template
                ? $request->input('template.name')
                : $page->template_identifier
        );

        // Validate the template data...
        $template->validate(
            $templateData = $request->input('template.data', [])
        );

        $page->title = $request->input('title');

        // Only change the slug if the page's
        // path is not fixed...
        $page->slug = ! $page->has_fixed_path
            ? $request->input('slug')
            : $page->slug;

        $page->template_name = $templateName;
        $page->parent_id = $request->input('parent_id');
        $page->is_standalone = $request->input('is_standalone');

        $page->save();

        $page->saveMeta($request->input('meta', []));

        if (! $page->has_fixed_path) {
            UpdatePagePath::dispatch($page);
        }

        // Update the page's template data...
        $template->reset($page);
        $template->save($page, $templateData);

        if ($page->isDraft() && $request->input('is_published')) {
            $page->publish();
        } elseif ($page->isPublished() && ! $request->input('is_published')) {
            $page->draft();
        }

        return new PageResource($page);
    }

    public function move(Request $request, $id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        $request->input('direction') === 'down'
            ? $page->moveOrderDown()
            : $page->moveOrderUp();

        return response()->noContent();
    }

    public function destroy($id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        if (! $page->is_deletable) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $page->delete();

        return response()->noContent();
    }

    protected function validatePage(Request $request)
    {
        $request->validate(array_merge([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'template.name' => [
                'required', function ($attribute, $value, $fail) {
                    // Verify that the template has been registered...
                    if (! $this->templateRegistry->exists($value)) {
                        $fail(__('validation.exists', [
                            'attribute' => 'template',
                        ]));
                    }
                },
            ],
            'template.data' => 'array',
            'parent_id' => 'nullable|exists:pages,id',
            'is_standalone' => 'present|boolean',
            'is_published' => 'present|boolean',
        ], Meta::rules()));
    }
}
