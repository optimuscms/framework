<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OptimusCMS\Pages\Template;
use OptimusCMS\Pages\Models\Page;
use Illuminate\Routing\Controller;
use OptimusCMS\Pages\TemplateRegistry;
use OptimusCMS\Pages\Jobs\UpdatePageUri;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\JsonResource;
use OptimusCMS\Pages\Http\Resources\PageResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PagesController extends Controller
{
    /** @var TemplateRegistry */
    protected $templates;

    /**
     * Create a new controller instance.
     *
     * @param TemplateRegistry $templates
     * @return void
     */
    public function __construct(TemplateRegistry $templates)
    {
        $this->templates = $templates;
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
            ->withCount('children')
            ->filter($request)
            ->orderBy('order')
            ->get();

        return PageResource::collection($pages);
    }

    /**
     * Create a new page.
     *
     * @param Request $request
     * @return JsonResource
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validatePage($request);

        $template = $this->templates->find(
            $request->input('template')
        );

        $template->validate($request);

        $page = new Page();

        $page->title = $request->input('title');
        $page->slug = $request->input('slug');
        $page->template = $template->name();
        $page->parent_id = $request->input('parent_id');
        $page->is_stand_alone = $request->input('is_stand_alone');
        $page->is_deletable = true;
        $page->order = Page::max('order') + 1;

        $page->save();

        UpdatePageUri::dispatch($page);

        $template->save($page, $request);

        if ($request->input('is_published')) {
            $page->publish();
        }

        return new PageResource($page);
    }

    /**
     * Display the specified page.
     *
     * @param int $id
     * @return JsonResource
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
     * @return JsonResource
     *
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        $this->validatePage($request);

        $template = $this->templates->find(
            ! $page->has_fixed_template
                ? $request->input('template')
                : $page->template
        );

        $template->validate($request);

        $page->title = $request->input('title');
        $page->slug = ! $page->has_fixed_uri
            ? $request->input('slug')
            : $page->slug;
        $page->template = $template->name();
        $page->parent_id = $request->input('parent_id');
        $page->is_stand_alone = $request->input('is_stand_alone');

        $page->save();

        if (! $page->has_fixed_uri) {
            UpdatePageUri::dispatch($page);
        }

        $page->detachMedia();
        $page->deleteContents();

        $template->save($page, $request);

        if ($page->isDraft() && $request->input('is_published')) {
            $page->publish();
        } elseif ($page->isPublished() && ! $request->input('is_published')) {
            $page->draft();
        }

        return new PageResource($page);
    }

    /**
     * Reorder a list of pages.
     *
     * @param Request $request
     * @return Response
     *
     * @throws ValidationException
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'pages' => 'required|array',
            'pages.*' => 'exists:pages,id'
        ]);

        $order = 1;

        foreach ($request->input('pages') as $id) {
            Page::where('id', $id)->update([
                'order' => $order
            ]);

            $order++;
        }

        return response(null, 204);
    }

    /**
     * Delete the specified page.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        if (! $page->is_deletable) {
            abort(403);
        }

        $page->delete();

        return response(null, 204);
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
        $request->validate([
            'title' => 'required',
            'template' => 'required|in:' . collect($this->templates->all())
                ->map(function (Template $template) {
                    return $template->name();
                })
                ->implode(','),
            'parent_id' => 'exists:pages,id|nullable',
            'is_stand_alone' => 'present|boolean',
            'is_published' => 'present|boolean'
        ]);
    }
}
