<?php

use Faker\Generator as Faker;
use OptimusCMS\Media\Models\MediaFolder;

$factory->define(MediaFolder::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'parent_id' => null,
    ];
});
