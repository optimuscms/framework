<?php

namespace OptimusCMS\Pages\Rules;

use Illuminate\Contracts\Validation\Rule;
use OptimusCMS\Pages\Models\Page;

class NotSameAsOrAncestorOfPage implements Rule
{
    protected $page;

    public function __construct(?Page $page = null)
    {
        $this->page = $page;
    }

    public function passes($attribute, $value)
    {
        if (is_null($this->page)) {
            return true;
        }

        $parentPage = Page::withDrafts()->find($value);

        if (is_null($parentPage)) {
            return true;
        }

        return ! $this->isSameOrAncestor($parentPage);
    }

    protected function isSameOrAncestor(Page $parentPage)
    {
        if ($parentPage->is($this->page)) {
            return true;
        }

        if ($parentPage->children->isEmpty()) {
            return false;
        }

        foreach ($parentPage->children as $childPage) {
            if ($this->isSameOrAncestor($childPage)) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return "The selected :attribute must not be the same as, or an ancestor of the page you're editing.";
    }
}
