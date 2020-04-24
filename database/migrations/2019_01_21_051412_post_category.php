<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PostCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default()->nullable();
            $table->string('url')->default()->nullable();
            $table->integer('id_media')->default()->nullable();
            $table->integer('id_lang')->default()->nullable();
            $table->integer('status')->default()->nullable();
            $table->string('meta_title')->default()->nullable();
            $table->string('meta_description')->default()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_category');
    }
}
