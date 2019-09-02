<?php

use Faker\Generator as Faker;
use OptimusCMS\Media\Models\Media;
use OptimusCMS\Media\Models\MediaFolder;

$factory->define(Media::class, function (Faker $faker) {
    return [
        'folder_id' => function () {
            return factory(MediaFolder::class)->create()->id;
        },
        'name' => $faker->word,
        'alt_text' => $faker->sentence,
        'caption' => $faker->sentence,
        'file_name' => "file.{$faker->fileExtension}",
        'disk' => config('media.disk'),
        'mime_type' => $faker->mimeType,
        'size' => $faker->randomNumber(4),
    ];
});
