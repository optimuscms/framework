<?php

namespace OptimusCMS\Pages\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use OptimusCMS\Pages\Models\Page;

class UpdatePagePath
{
    use Dispatchable;

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
        $this->page->path = $this->page->generatePath();
        $this->page->save();

        if ($this->page->wasChanged('path')) {
            UpdateChildPagePaths::dispatch($this->page);
        }
    }
}
