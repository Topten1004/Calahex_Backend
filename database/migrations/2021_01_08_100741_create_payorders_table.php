<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payorders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('payment_id');
            $table->string('reference');
            $table->string('unit');
            $table->float('amount');
            $table->string('address')->nullable();
            $table->float('amount_left')->nullable();
            $table->string('payment_type');
            $table->dateTime('transaction_time');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('payorders');
    }
}
