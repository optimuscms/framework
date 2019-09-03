<?php

namespace OptimusCMS\Pages\Models;

use Optix\Media\HasMedia;
use OptimusCMS\Meta\HasMeta;
use Spatie\Sluggable\HasSlug;
use Optix\Draftable\Draftable;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use OptimusCMS\Pages\Contracts\Template;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OptimusCMS\Pages\Facades\Template as TemplateFacade;

class Page extends Model
{
    use Draftable,
        HasMeta,
        HasMedia,
        HasSlug;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_fixed_template' => 'bool',
        'has_fixed_path' => 'bool',
        'is_standalone' => 'bool',
        'is_deletable' => 'bool',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'template_name',
        'parent_id',
        'is_stand_alone',
        'is_deletable',
        'order',
    ];

    /**
     * Apply filters to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeApplyFilters(Builder $query, array $filters)
    {
        // Parent
        if (! empty($filters['parent'])) {
            $query->whereChildOf($filters['parent']);
        }
    }

    /**
     * Only retrieve pages that are direct descendants of the given page.
     *
     * @param Builder $query
     * @param self|int $parent
     * @return void
     */
    public function scopeWhereChildOf(Builder $query, $parent)
    {
        if ($parent instanceof self) {
            $parent = $parent->id;
        }

        $query->where('parent_id', $parent);
    }

    /**
     * Only retrieve pages that are deletable.
     *
     * @param Builder $query
     * @param void
     */
    public function scopeDeletable(Builder $query)
    {
        $query->where('is_deletable', true);
    }

    /**
     * Generate a path for the page.
     *
     * @return string
     */
    public function generatePath()
    {
        $prefix = '';

        $parent = $this->parent;

        if ($parent && $prefix = $parent->uri) {
            $prefix .= '/';
        }

        return $prefix.$this->slug;
    }

    /**
     * Get the page's slug options.
     *
     * @return SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        $options = SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');

        // Prevent the slug from being updated if the
        // page has a fixed path...
        if ($this->has_fixed_path) {
            $options->doNotGenerateSlugsOnUpdate();
        }

        return $options;
    }

    /**
     * Determine if another record exists with the
     * given slug and the same parent.
     *
     * @param string $slug
     * @return bool
     */
    protected function otherRecordExistsWithSlug(string $slug): bool
    {
        return static::where($this->slugOptions->slugField, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?? '0')
            ->where('parent_id', $this->parent_id)
            ->withoutGlobalScopes()
            ->exists();
    }

    /**
     * Get the page's template class.
     *
     * @return Template
     */
    public function template()
    {
        return TemplateFacade::load($this->template_name);
    }

    /**
     * Create a new page content record.
     *
     * @param string $key
     * @param string $value
     * @return PageContent
     */
    public function addContent($key, $value)
    {
        $content = new PageContent();

        $content->key = $key;
        $content->value = $value;

        $this->contents()->save($content);
    }

    /**
     * Add contents to the page.
     *
     * @param array $contents
     * @return Collection
     */
    public function addContents(array $contents)
    {
        $contents = $this->newCollection();

        foreach ($contents as $key => $value) {
            $contents->push(
                $this->addContent($key, $value)
            );
        }

        return $contents;
    }

    /**
     * Get the page content value with the given key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getContent($key, $default = null)
    {
        if (! $this->hasContent($key)) {
            return $default;
        }

        $content = $this->contents->where('key', $key);

        return $content->value;
    }

    /**
     * Determine if the page has content with the given key.
     *
     * @param string $key
     * @return bool
     */
    public function hasContent($key)
    {
        return $this->contents->contains(
            function (PageContent $content) use ($key) {
                return $content->key === $key;
            }
        );
    }

    /**
     * Clear all contents from the page.
     *
     * @return mixed
     */
    public function clearContents()
    {
        return $this->contents()->delete();
    }

    /**
     * Get the parent relationship.
     *
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /**
     * Get the children relationship.
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    /**
     * Get the contents relationship.
     *
     * @return HasMany
     */
    public function contents()
    {
        return $this->hasMany(PageContent::class, 'page_id');
    }
}
