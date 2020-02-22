<?php

namespace OptimusCMS\Pages\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use OptimusCMS\Meta\HasMeta;
use OptimusCMS\Pages\PageTemplates;
use Optix\Draftable\Draftable;
use Optix\Media\HasMedia;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model implements Sortable
{
    use Draftable,
        HasMedia,
        HasMeta,
        HasSlug,
        SortableTrait;

    protected $casts = [
        'has_fixed_template' => 'bool',
        'has_fixed_path' => 'bool',
        'is_standalone' => 'bool',
        'is_deletable' => 'bool',
    ];

    protected $dates = [
        'published_at',
    ];

    protected $fillable = [
        'title', 'slug', 'template_id', 'parent_id', 'is_standalone',
    ];

    protected $sortable = [
        'order_column_name' => 'order',
    ];

    public function scopeApplyFilters(Builder $query, array $filters)
    {
        // Parent
        if (! empty($filters['parent'])) {
            $query->whereChildOf($filters['parent']);
        }
    }

    public function scopeWhereChildOf(Builder $query, $parent)
    {
        if ($parent instanceof self) {
            $parent = $parent->id;
        }

        $query->where(
            'parent_id',
            $parent === 'root' ? null : $parent
        );
    }

    public function scopeDeletable(Builder $query)
    {
        $query->where('is_deletable', true);
    }

    public function buildSortQuery()
    {
        return $this->newQuery()->where(
            'parent_id', $this->parent_id
        );
    }

    public function generatePath()
    {
        $prefix = '';

        $parent = $this->parent;

        if ($parent && $prefix = $parent->path) {
            $prefix .= '/';
        }

        return $prefix.$this->slug;
    }

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

    protected function otherRecordExistsWithSlug(string $slug): bool
    {
        return $this->newQuery()->where($this->slugOptions->slugField, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?? '0')
            ->where('parent_id', $this->parent_id)
            ->withoutGlobalScopes()
            ->exists();
    }

    public function templateHandler()
    {
        return PageTemplates::load($this->template_id);
    }

    public function addContent($key, $value)
    {
        $content = new PageContent();

        $content->key = $key;
        $content->value = $value;

        return $this->contents()->save($content);
    }

    public function addContents(array $contents)
    {
        $models = $this->newCollection();

        foreach ($contents as $key => $value) {
            $models->push(
                $this->addContent($key, $value)
            );
        }

        return $models;
    }

    public function getContent($key, $default = null)
    {
        if (! $this->hasContent($key)) {
            return $default;
        }

        $content = $this->contents->firstWhere('key', $key);

        return $content->value;
    }

    public function hasContent($key)
    {
        return $this->contents->contains(
            function (PageContent $content) use ($key) {
                return $content->key === $key;
            }
        );
    }

    public function clearContents()
    {
        return $this->contents()->delete();
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function contents()
    {
        return $this->hasMany(PageContent::class, 'page_id');
    }
}
