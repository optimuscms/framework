<?php

namespace OptimusCMS\Pages\Http\Controllers;

use Illuminate\Http\Request;
use OptimusCMS\Pages\Template;
use OptimusCMS\Pages\Models\Page;
use Illuminate\Routing\Controller;
use OptimusCMS\Pages\TemplateRegistry;
use OptimusCMS\Pages\Jobs\UpdatePagePath;
use OptimusCMS\Pages\Http\Resources\PageResource;

class PagesController extends Controller
{
    /**
     * @var \OptimusCMS\Pages\TemplateRegistry
     */
    protected $templates;

    /**
     * PagesController constructor.
     *
     * @param  \OptimusCMS\Pages\TemplateRegistry  $templates
     * @return void
     */
    public function __construct(TemplateRegistry $templates)
    {
        $this->templates = $templates;
    }

    /**
     * Display a list of pages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     *
     * @throws \Illuminate\Validation\ValidationException
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

        UpdatePagePath::dispatch($page);

        $template->save($page, $request);

        if ($request->input('is_published')) {
            $page->publish();
        }

        return new PageResource($page);
    }

    /**
     * Display the specified page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($id)
    {
        $page = Page::withDrafts()->findOrFail($id);

        return new PageResource($page);
    }

    /**
     * Update the specified page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\JsonResource
     *
     * @throws \Illuminate\Validation\ValidationException
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
            UpdatePagePath::dispatch($page);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'pages' => 'required|array',
            'pages.*' => 'exists:pages,id',
        ]);

        $order = 1;

        foreach ($request->input('pages') as $id) {
            Page::where('id', $id)->update([
                'order' => $order,
            ]);

            $order++;
        }

        return response(null, 204);
    }

    /**
     * Delete the specified page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatePage(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'template' => 'required|in:'.collect($this->templates->all())
                    ->map(function (Template $template) {
                        return $template->name();
                    })
                    ->implode(','),
            'parent_id' => 'exists:pages,id|nullable',
            'is_stand_alone' => 'present|boolean',
            'is_published' => 'present|boolean',
        ]);
    }
}
