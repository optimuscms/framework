<?php

namespace OptimusCMS\Tests\Pages;

use Illuminate\Http\Request;
use OptimusCMS\Pages\Template;
use OptimusCMS\Pages\Models\Page;

class DummyTemplate extends Template
{
    public function name(): string
    {
        return 'dummy';
    }

    public function validate(Request $request)
    {
        $request->validate([
            'content' => 'required'
        ]);
    }

    public function save(Page $page, Request $request)
    {
        $page->addContents([
            'content' => $request->input('content')
        ]);
    }
}
