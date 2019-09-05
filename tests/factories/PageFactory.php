<?php

use Carbon\Carbon;
use Faker\Generator as Faker;
use OptimusCMS\Pages\Models\Page;

$factory->define(Page::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'parent_id' => null,
        'template_name' => 'dummy',
        'is_standalone' => false,
        'published_at' => Carbon::now(),
        'order' => Page::max('order') + 1,
    ];
});

$factory->state(Page::class, 'draft', function () {
    return [
        'published_at' => null,
    ];
});
