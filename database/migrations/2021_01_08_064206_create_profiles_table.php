<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('referral_to')->nullable();
            $table->string('country')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('language')->default(0);
            $table->string('auth_code')->nullable();
            $table->boolean('api_enabled')->default(false);
            $table->dateTime('email_verified_at')->nullable();
            $table->dateTime('phone_verified_at')->nullable();
            $table->dateTime('auth_enabled_at')->nullable();
            $table->dateTime('video_verified_at')->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
