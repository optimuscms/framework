<?php

namespace OptimusCMS\Meta;

use Intervention\Image\Image;
use OptimusCMS\Meta\Models\Meta;
use Optix\Media\Facades\Conversion;
use Illuminate\Support\ServiceProvider;

class MetaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(
            __DIR__.'/database/migrations'
        );

        // Conversions
        Conversion::register(
            Meta::OG_IMAGE_MEDIA_CONVERSION,
            function (Image $image) {
                return $image->fit(1200, 630);
            }
        );
    }
}
