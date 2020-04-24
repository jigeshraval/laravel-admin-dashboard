<?php

use Illuminate\Database\Seeder;

use App\Objects\Post;
use App\Objects\PostCategory;
use App\Objects\PostCategoryAssigned;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::truncate();
        PostCategory::truncate();
        PostCategoryAssigned::truncate();

        factory(App\Objects\Post::class, 3)
        ->create()
        ->each(function ($post) {
            $post->post_category()->save(factory(App\Objects\PostCategory::class)->make());
        });
    }
}
