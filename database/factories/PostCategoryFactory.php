<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Objects\PostCategory;
use Faker\Generator as Faker;

$factory->define(PostCategory::class, function (Faker $faker) {
    $title = $faker->sentence;
    return [
        'title' => $title,
        'url' => \Str::slug($title),
        'meta_title' => $title,
        'meta_description' => $title
    ];
});