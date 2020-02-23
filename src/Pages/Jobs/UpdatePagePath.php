<?php

namespace OptimusCMS\Pages\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OptimusCMS\Pages\Models\Page;

class UpdatePagePath
{
    use Dispatchable, SerializesModels;

    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function handle()
    {
        $this->page->path = $this->page->generatePath();
        $this->page->save();

        if ($this->page->wasChanged('path')) {
            UpdateChildPagePaths::dispatch($this->page);
        }
    }
}
