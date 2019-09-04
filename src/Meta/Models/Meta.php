<?php

namespace OptimusCMS\Meta\Models;

use Optix\Media\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Meta extends Model
{
    use HasMedia;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'meta';

    /** @var string */
    const OG_IMAGE_MEDIA_GROUP = 'og_image';

    /** @var string */
    const OG_IMAGE_MEDIA_CONVERSION = '1200x630';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'og_title',
        'og_description',
        'additional_tags',
    ];

    /**
     * Get the og image url.
     *
     * @return string
     */
    public function getOgImageAttribute()
    {
        return $this->getFirstMediaUrl(
            self::OG_IMAGE_MEDIA_GROUP,
            self::OG_IMAGE_MEDIA_CONVERSION
        );
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'meta' => 'array',
            'meta.title' => 'nullable|string|max:255',
            'meta.description' => 'nullable|string|max:255',
            'meta.og_title' => 'nullable|string|max:255',
            'meta.og_description' => 'nullable|string|max:255',
            'meta.og_image_id' => 'nullable|exists:media,id',
            'meta.additional_tags' => 'nullable|string',
        ];
    }

    /**
     * Register the model's media groups.
     *
     * @return void
     */
    public function registerMediaGroups()
    {
        $this->addMediaGroup(self::OG_IMAGE_MEDIA_GROUP)
             ->performConversions(self::OG_IMAGE_MEDIA_CONVERSION);
    }

    /**
     * Get the metable relationship.
     *
     * @return MorphTo
     */
    public function metable()
    {
        return $this->morphTo();
    }
}
