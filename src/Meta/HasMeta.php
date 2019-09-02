<?php

namespace OptimusCMS\Meta;

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
        return tap(
            $this->meta()->updateOrCreate([], $data),
            function (Meta $meta) use ($data) {
                if (! array_key_exists('og_image_id', $data)) {
                    return;
                }

                if (! $meta->wasRecentlyCreated) {
                    $meta->clearMediaGroup(Meta::OG_IMAGE_MEDIA_GROUP);
                }

                // Attach an og image to the model if it's
                // present in the data...
                if ($ogImageId = $data['og_image_id']) {
                    $meta->attachMedia(
                        $ogImageId,
                        Meta::OG_IMAGE_MEDIA_GROUP
                    );
                }
            }
        );
    }
}
