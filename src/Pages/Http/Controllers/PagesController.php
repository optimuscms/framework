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

// page
//   title
//   slug
//   parent_id
//   template_id
//   template_data
//   is_standalone
//   is_published

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

        $templateHandler = PageTemplates::load(
            $templateId = $request->input('template_id')
        );

        $templateHandler->validate(
            $templateData = $request->input('template_data', [])
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

        $templateHandler->save($page, $templateData);

        $page->saveMeta(
            $request->input('meta', [])
        );

        UpdatePagePath::dispatch($page)->onQueue('sync');

        if ($request->input('is_published')) {
            $page->publish();
        }

        return new PageResource($page);
    }

    public function show($pageId)
    {
        $page = Page::withDrafts()->findOrFail($pageId);

        return new PageResource($page);
    }

    public function update(Request $request, $pageId)
    {
        $page = Page::withDrafts()->findOrFail($pageId);

        $this->validatePage($request);

        $templateId = ! $page->has_fixed_template
            ? $request->input('template_id')
            : $page->template_id;

        $templateHandler = PageTemplates::load($templateId);

        $templateHandler->validate(
            $templateData = $request->input('template_data', [])
        );

        $slug = ! $page->has_fixed_path
            ? $request->input('slug')
            : $page->slug;

        $page->update([
            'title' => $request->input('title'),
            'slug' => $slug,
            'template_id' => $templateId,
            'parent_id' => $request->input('parent_id'),
            'is_standalone' => $request->input('is_standalone'),
        ]);

        $templateHandler->reset($page);
        $templateHandler->save($page, $templateData);

        $page->saveMeta(
            $request->input('meta', [])
        );

        if (! $page->has_fixed_path) {
            UpdatePagePath::dispatch($page)->onQueue('sync');
        }

        if ($page->isDraft() && $request->input('is_published')) {
            $page->publish();
        } elseif ($page->isPublished() && ! $request->input('is_published')) {
            $page->draft();
        }

        return new PageResource($page);
    }

    public function move(Request $request, $pageId)
    {
        $page = Page::withDrafts()->findOrFail($pageId);

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
            'template_id' => [
                'required', function ($attribute, $value, $fail) {
                    if (! PageTemplates::exists($value)) {
                        $fail(__('validation.exists', [
                            'attribute' => 'template id',
                        ]));
                    }
                },
            ],
            'template_data' => 'array',
            'parent_id' => [
                'required', 'exists:pages,id',
                // Todo: Not ancestor of self
            ],
            'is_standalone' => 'present|boolean',
            'is_published' => 'present|boolean',
        ], Meta::rules()));
    }
}
