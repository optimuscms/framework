<?php

namespace OptimusCMS\Pages\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OptimusCMS\Pages\Models\Page;

class UpdateChildPagePaths
{
    use Dispatchable, SerializesModels;

    /** @var Page */
    protected $page;

    /**
     * Create a new job instance.
     *
     * @param Page $page
     * @return void
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->updateChildPagePaths($this->page);
    }

    /**
     * Update the child page paths.
     *
     * @param Page $parentPage
     * @return void
     */
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
