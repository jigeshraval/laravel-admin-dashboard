<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetsTable extends Migration
{
  public function boot()
  {
    Schema::defaultStringLength(191);
  }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
			      $table->increments('id');
            $table->string('email', 250)->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
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
		Schema::hasTable('password_resets');
        Schema::dropIfExists('password_resets');
    }
}
