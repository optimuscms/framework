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

    protected function updateChildPagePaths(Page $parentPage)
    {
        $childPages = $parentPage->children()
            ->where('has_fixed_path', false)
            ->get();

        foreach ($childPages as $childPage) {
            $childPage->setRelation('parent', $parentPage);

            $childPage->path = $childPage->buildPath();
            $childPage->save();

            $this->updateChildPagePaths($childPage);
        }
    }
}
