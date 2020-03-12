<?php

namespace OptimusCMS\Tests\Pages;

use OptimusCMS\Pages\Models\Page;

class PageTest extends TestCase
{
    /** @test */
    public function it_can_build_a_path()
    {
        $rootPage = new Page();
        $rootPage->forceFill([
            'path' => 'root',
        ]);

        $parentPage = new Page();
        $parentPage->parent()->associate($rootPage);
        $parentPage->forceFill([
            'path' => 'root/parent',
        ]);

        $page = new Page();
        $page->parent()->associate($parentPage);
        $page->forceFill([
            'slug' => 'current',
            'path' => 'existing/path',
        ]);

        $this->assertEquals('root/parent/current', $page->buildPath());
    }

    /** @test */
    public function it_will_not_build_a_new_path_if_its_fixed()
    {
        $parentPage = new Page();
        $parentPage->forceFill([
            'path' => 'parent',
        ]);

        $page = new Page();
        $page->parent()->associate($parentPage);
        $page->forceFill([
            'slug' => 'current',
            'path' => 'existing/path',
            'has_fixed_path' => true,
        ]);

        $this->assertEquals('existing/path', $page->buildPath());
    }
}
