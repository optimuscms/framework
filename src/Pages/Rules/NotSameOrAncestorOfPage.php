<?php

namespace OptimusCMS\Pages\Rules;

use Illuminate\Contracts\Validation\Rule;
use OptimusCMS\Pages\Models\Page;

// Todo: Rename
class NotSameOrAncestorOfPage implements Rule
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function passes($attribute, $value)
    {
        return ! $this->isAncestorOrSelf(Page::find($value));
    }

    protected function isAncestorOrSelf($page)
    {
        if (is_null($page)) {
            return false;
        }

        if ($page->is($this->page)) {
            return true;
        }

        if ($page->children->isEmpty()) {
            return false;
        }

        foreach ($page->children as $childPage) {
            if ($this->isAncestorOrSelf($childPage)) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return 'The :attribute must not be the same as, or an ancestor of the current page.';
    }
}
