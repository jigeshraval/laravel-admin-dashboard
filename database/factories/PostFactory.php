<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Objects\Post;
use App\Objects\PostCategory;
use App\Objects\PostCategoryAssigned;
use Faker\Generator;

$factory->define(Post::class, function (Generator $faker) {

    $title = $faker->sentence;

    $paragraph = '';

    for ($x = 1; $x <= 5; $x++) {
        $paragraph .= '<p>' . $faker->paragraph(10) . '</p>';
    }

    return [
        'title' => $title,
        'url' => \Str::slug($title),
        'content' => $paragraph,
        'meta_title' => $title,
        'meta_description' => $title,
        'post_date' => date('Y-m-d h:i:s'),
        'published' => 0
    ];
});
