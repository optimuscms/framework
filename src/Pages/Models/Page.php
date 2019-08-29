<?php

namespace OptimusCMS\Pages\Models;

use OptimusCMS\Meta\HasMeta;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use OptimusCMS\Pages\Facades\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasMeta,
        HasSlug;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_fixed_template' => 'bool',
        'has_fixed_uri' => 'bool',
        'is_deletable' => 'bool',
        'is_stand_alone' => 'bool',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'template_name', 'parent_id', 'is_stand_alone', 'is_deletable', 'order',
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
            $parent = $filters['parent'] === 'root'
                ? $filters['parent']
                : null;

            $query->where('parent_id', $parent);
        }
    }

    /**
     * Get the page's template handler.
     *
     * @return
     */
    public function getTemplateAttribute()
    {
        return Template::load($this->template_name);
    }

    /**
     * Only retrieve deletable pages.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeDeletable(Builder $query)
    {
        $query->where('is_deletable', true);
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

        if ($this->has_fixed_uri) {
            $options->doNotGenerateSlugsOnUpdate();
        }

        return $options;
    }

    /**
     * Determine if another model exists with the given slug.
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
     * Generate the page's path.
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
     * Add contents to the page.
     *
     * @param array $contents
     * @return Collection
     */
    public function addContents(array $contents)
    {
        $records = [];

        foreach ($contents as $key => $value) {
            $records[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        return $this->contents()->createMany($records);
    }

    /**
     * Determine if the page has content with the given key.
     *
     * @param string $key
     * @return bool
     */
    public function hasContent($key)
    {
        return $this->contents
            ->contains(function ($content) use ($key) {
                return $content->key === $key;
            });
    }

    /**
     * Get the content value with the given key.
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

        $content = $this->contents->firstWhere('key', $key);

        return $content->value;
    }

    /**
     * Clear the page's contents.
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
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the children relationship.
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
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
