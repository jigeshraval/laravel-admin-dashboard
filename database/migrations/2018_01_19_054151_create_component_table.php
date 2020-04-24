<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('component', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('table');
            $table->string('variable');
            $table->string('slug');
            $table->string('controller');
            $table->integer('is_front_create')->unsigned();
            $table->integer('is_front_view')->unsigned();
            $table->integer('is_front_list')->unsigned();
            $table->integer('is_admin_create')->unsigned();
            $table->integer('is_admin_list')->unsigned();
            $table->integer('is_admin_delete')->unsigned();
            $table->integer('is_login_needed')->unsigned()->nullable();
            $table->integer('is_meta_needed')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });

        Schema::create('component_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_component')->unsigned();
            $table->string('field_name');
            $table->string('field_text');
            $table->string('column_type');
            $table->string('input_type');
            $table->integer('use_in_listing')->unsigned();
            $table->integer('is_fillable')->unsigned();
            $table->integer('required')->unsigned();
            $table->integer('default')->nullable();
            $table->string('class')->nullable();
            $table->string('relationship_type')->nullable();
            $table->string('reference_name')->nullable();
            $table->string('relational_component_name')->nullable();
            $table->string('foreign_key')->nullable();
            $table->string('local_key')->nullable();
            $table->string('mediator_table')->nullable();
            $table->string('mediator_table_key')->nullable();
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
        Schema::dropIfExists('component');
        Schema::dropIfExists('component_fields');
    }
}
