<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('token_decimal');
            $table->string('token_name');
            $table->string('token_symbol');
            $table->string('token_logo');
            $table->string('token_pair_type');
            $table->string('token_whitepaper');
            $table->boolean('for_cefi')->default(true);
            $table->string('wallet_address')->nullable();
            $table->string('deposit_fee')->default(0);
            $table->string('transfer_fee')->default(0);
            $table->boolean('is_deleted')->default(false);
            $table->string('status');
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
        Schema::dropIfExists('tokens');
    }
}
