<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password', 200);
            $table->string('refToken', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('address', 100)->nullable();
            $table->integer('parent_id')->default(0);
            $table->integer('user_type')->default(0)->comment('0-Users, 1-Admin');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
