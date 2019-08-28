<?php

namespace OptimusCMS\Pages\Jobs;

use OptimusCMS\Pages\Models\Page;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePageUri
{
    use Dispatchable;

    protected $page;

    /**
     * Create a new job instance.
     *
     * @param  Page  $page
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
        $this->page->uri = $this->page->generateUri();
        $this->page->save();

        if ($this->page->wasChanged('uri')) {
            UpdateChildPageUris::dispatch($this->page);
        }
    }
}
