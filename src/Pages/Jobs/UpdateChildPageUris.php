<?php

namespace Optimus\Pages\Jobs;

use Illuminate\Bus\Queueable;
use OptimusCMS\Pages\Models\Page;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateChildPageUris
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $this->updateChildPageUris($this->page);
    }

    protected function updateChildPageUris(Page $parent)
    {
        $children = $parent->children()
            ->where('has_fixed_uri', false)
            ->get();

        $children->each(function (Page $page) use ($parent) {
            $page->setRelation('parent', $parent);

            $page->uri = $page->generateUri();
            $page->save();

            $this->updateChildPages($page);
        });
    }
}
