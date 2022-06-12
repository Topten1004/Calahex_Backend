<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->dateTime('futures_activated_at')->nullable();
            $table->dateTime('margin_activated_at')->nullable();
            $table->dateTime('pool_activated_at')->nullable();
            $table->dateTime('savings_activated_at')->nullable();
            $table->dateTime('margin_paid_at')->nullable();
            $table->dateTime('pool_paid_at')->nullable();
            $table->dateTime('savings_paid_at')->nullable();
            $table->string('status')->default('good');
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
        Schema::dropIfExists('wallets');
    }
}
