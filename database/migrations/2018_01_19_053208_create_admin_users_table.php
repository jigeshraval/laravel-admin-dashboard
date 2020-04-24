<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('mobile')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });

        $data = [
          'name' => 'Jigesh Raval',
          'email' => 'jigesh@jigeshraval.com',
          'password' => '$2y$10$JkBE4qanKSUjC.M.OdF7GuqawAkFzkOjho1VFOj88c89Yeup9YQPS'
        ];
        \Illuminate\Support\Facades\DB::table('admin_users')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
}
