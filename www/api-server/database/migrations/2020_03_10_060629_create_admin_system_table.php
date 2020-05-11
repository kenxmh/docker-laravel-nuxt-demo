<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('username', 32)->unique();
            $table->string('password', 128);
            $table->string('mobile', 11)->default('');
            $table->string('email', 32)->default('');
            $table->string('realname', 32)->default('');
            $table->tinyInteger('status')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('admin_role', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 32)->comment('角色名')->unique();
            $table->timestamps();
        });

        Schema::create('admin_access', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('pid')->default(0)->index();
            $table->string('name', 32);
            $table->string('action')->default('')->index();
            $table->timestamps();
        });

        Schema::create('admin_user_role', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('admin_id')->unsigned()->index();
            $table->integer('role_id')->unsigned()->index();
        });

        Schema::create('admin_role_access', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('role_id')->unsigned()->index();
            $table->integer('access_id')->unsigned()->index();
        });

        Schema::create('admin_operation_log', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('admin_id')->unsigned()->index();
            $table->string('method', 7);
            $table->string('description', 32);
            $table->string('path', 64);
            $table->text('raw_data');
            $table->string('ip', 16);
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
        Schema::dropIfExists('admin_user');
        Schema::dropIfExists('admin_role');
        Schema::dropIfExists('admin_access');
        Schema::dropIfExists('admin_user_role');
        Schema::dropIfExists('admin_role_access');
        Schema::dropIfExists('admin_operation_log');
    }
}
