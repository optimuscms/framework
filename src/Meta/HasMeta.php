<?php

namespace OptimusCMS\Meta;

use Illuminate\Support\Arr;
use OptimusCMS\Meta\Models\Meta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMeta
{
    /**
     * Get the meta relationship.
     *
     * @return MorphOne
     */
    public function meta()
    {
        return $this->morphOne(Meta::class, 'metable');
    }

    /**
     * Save meta to the model.
     *
     * @param array $data
     * @return Meta|false
     */
    public function saveMeta(array $data = [])
    {
        $meta = $this->meta ?: new Meta();

        $ogImageId = Arr::pull($data, 'og_image_id');
        $meta->fill($data);

        $meta = $this->meta()->save($meta);

        if ($meta && $ogImageId) {
            $meta->attachMedia(
                $ogImageId, Meta::OG_IMAGE_MEDIA_GROUP
            );
        }

        return $meta;
    }
}
