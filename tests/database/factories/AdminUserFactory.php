<?php

use Faker\Generator as Faker;
use OptimusCMS\Users\Models\AdminUser;

$factory->define(AdminUser::class, function (Faker $faker) {
    static $password;
    return [
        'name' => $faker->name,
        'username' => $faker->unique()->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
