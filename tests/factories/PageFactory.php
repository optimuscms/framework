<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Tests\Pages\Fixtures\TestTemplateOne;

/** @var Factory $factory */
$factory->define(Page::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'slug' => $slug = $faker->unique()->slug,
        'path' => $slug,
        'has_fixed_path' => false,
        'parent_id' => null,
        'template_id' => TestTemplateOne::getId(),
        'has_fixed_template' => false,
        'is_standalone' => false,
        'is_deletable' => true,
        'published_at' => now(),
    ];
});
