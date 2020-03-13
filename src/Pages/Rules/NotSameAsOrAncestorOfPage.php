<?php

namespace OptimusCMS\Pages\Rules;

use Illuminate\Contracts\Validation\Rule;
use OptimusCMS\Pages\Models\Page;

class NotSameAsOrAncestorOfPage implements Rule
{
    /** @var Page|null */
    protected $page;

    /**
     * Create a new rule instance.
     *
     * @param Page|null $page
     * @return void
     */
    public function __construct(?Page $page = null)
    {
        $this->page = $page;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_null($this->page)) {
            return true;
        }

        /** @var Page|null $parentPage */
        $parentPage = Page::withDrafts()->find($value);

        if (is_null($parentPage)) {
            return true;
        }

        return ! $this->isSameOrAncestor($parentPage);
    }

    /**
     * Determine if the given page is the same as,
     * or an ancestor of the page on the instance.
     *
     * @param Page $parentPage
     * @return bool
     */
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

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The selected :attribute must not be the same as, or an ancestor of the page you're editing.";
    }
}
