<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('auth_verified_at')->nullable();
            $table->timestamp('video_verified_at')->nullable();
            $table->string('password');
            $table->string('password_recovery')->nullable();
            $table->string('referral_from')->nullable();
            $table->integer('verify_level')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->string('menuroles');
            $table->integer('status')->unsigned()->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
