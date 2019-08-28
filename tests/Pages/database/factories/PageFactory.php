<?php

use Faker\Generator as Faker;
use OptimusCMS\Pages\Models\Page;

$factory->define(Page::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'parent_id' => null,
        'template' => 'default',
        'is_stand_alone' => false,
        'published_at' => now(),
        'order' => Page::max('order') + 1
    ];
});

$factory->state(Page::class, 'draft', function () {
    return [
        'published_at' => null
    ];
});
