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

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->doNotGenerateSlugsOnUpdate()
            ->saveSlugsTo('slug');
    }

    protected function otherRecordExistsWithSlug(string $slug): bool
    {
        return $this->newQueryWithoutScopes()
            ->where($this->slugOptions->slugField, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?? '0')
            ->where('parent_id', $this->parent_id)
            ->exists();
    }

    public function buildSortQuery()
    {
        return $this->newQuery()->where(
            'parent_id', $this->parent_id
        );
    }

    public function scopeApplyFilters(Builder $query, array $filters)
    {
        // Parent
        if (isset($filters['parent'])) {
            $parentId = $filters['parent'];

            if ($parentId === 'root') {
                $parentId = null;
            }

            $query->where('parent_id', $parentId);
        }
    }

    public function buildPath()
    {
        if ($this->has_fixed_path) {
            return $this->path;
        }

        $prefix = '';

        $parent = $this->parent;

        if ($parent && $prefix = $parent->path) {
            $prefix .= '/';
        }

        return $prefix.$this->slug;
    }

    public function templateHandler()
    {
        return PageTemplates::load($this->template_id);
    }

    public function addContent($key, $value)
    {
        $content = new PageContent([
            'key' => $key,
            'value' => $value,
        ]);

        return $this->contents()->save($content);
    }

    public function addContents(array $contents)
    {
        $models = $this->newCollection();

        foreach ($contents as $key => $value) {
            $models->add($this->addContent($key, $value));
        }

        return $models;
    }

    public function getContent($key, $default = null)
    {
        foreach ($this->contents as $content) {
            if ($content->key === $key) {
                return $content->value;
            }
        }

        return $default;
    }

    public function hasContent($key)
    {
        foreach ($this->contents as $content) {
            if ($content->key === $key) {
                return true;
            }
        }

        return false;
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
