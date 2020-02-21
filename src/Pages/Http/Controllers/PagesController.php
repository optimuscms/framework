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
use OptimusCMS\Pages\TemplateRegistry;

class PagesController extends Controller
{
    /** @var TemplateRegistry */
    protected $templateRegistry;

    /**
     * Create a new controller instance.
     *
     * @param TemplateRegistry $templateRegistry
     * @return void
     */
    public function __construct(TemplateRegistry $templateRegistry)
    {
        $this->templateRegistry = $templateRegistry;
    }

    /**
     * Display a list of pages.
     *
     * @param Request $request
     * @return ResourceCollection
     */
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

    /**
     * Create a new page.
     *
     * @param Request $request
     * @return PageResource
     *
     * @throws ValidationException
     * @throws TemplateNotFoundException
     */
    public function store(Request $request)
    {
        $this->validatePage($request);

        $template = $this->templateRegistry->load(
            $templateName = $request->input('template.name')
        );

        // Validate the template data...
        $template->validate(
            $templateData = $request->input('template.data', [])
        );

        $page = new Page();

        $page->title = $request->input('title');
        $page->slug = $request->input('slug');
        $page->has_fixed_path = false;
        $page->template_name = $templateName;
        $page->has_fixed_template = false;
        $page->parent_id = $request->input('parent_id');
        $page->is_standalone = $request->input('is_standalone');
        $page->is_deletable = true;

        $page->save();

        $page->saveMeta($request->input('meta', []));

        UpdatePagePath::dispatch($page);

        // Save the template data to the page...
        $template->save($page, $templateData);

        if ($request->input('is_published')) {
            $page->publish();
        }

        return new PageResource($page);
    }

    /**
     * Display the specified page.
     *
     * @param int $id
     * @return PageResource
     */
    public function show($id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        return new PageResource($page);
    }

    /**
     * Update the specified page.
     *
     * @param Request $request
     * @param int $id
     * @return PageResource
     *
     * @throws ValidationException
     * @throws TemplateNotFoundException
     */
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
                : $page->template_name
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

    /**
     * Reorder the specified page.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
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

    /**
     * Delete the specified page.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        if (! $page->is_deletable) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $page->delete();

        return response()->noContent();
    }

    /**
     * Validate the request.
     *
     * @param Request $request
     * @return void
     *
     * @throws ValidationException
     */
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
