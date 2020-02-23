<?php

namespace OptimusCMS\Pages\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OptimusCMS\Pages\Models\Page;

class UpdateChildPagePaths
{
    use Dispatchable, SerializesModels;

    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function handle()
    {
        $this->updateChildPagePaths($this->page);
    }

    protected function updateChildPagePaths(Page $parent)
    {
        $children = $parent->children()
            ->where('has_fixed_path', false)
            ->get();

        $children->each(function (Page $page) use ($parent) {
            $page->setRelation('parent', $parent);

            $page->path = $page->generatePath();
            $page->save();

            $this->updateChildPagePaths($page);
        });
    }
}
