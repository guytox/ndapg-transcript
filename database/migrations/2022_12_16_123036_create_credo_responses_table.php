<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCredoResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credo_responses', function (Blueprint $table) {
            $table->id();
            $table->string('transRef')->nullable();
            $table->string('currency')->nullable();
            $table->string('status')->nullable();
            $table->string('transAmount')->nullable();
            $table->string('businessCode')->nullable();
            $table->string('businessRef')->nullable();
            $table->string('debitedAmount')->nullable();
            $table->string('verified_transAmount')->nullable();
            $table->string('transFeeAmount')->nullable();
            $table->string('settlementAmount')->nullable();
            $table->string('customerId')->nullable();
            $table->string('transactionDate')->nullable();
            $table->string('channelId')->nullable();
            $table->string('currencyCode')->nullable();
            $table->string('response_status')->nullable();
            $table->string('payee_name')->nullable();
            $table->string('payee_id')->nullable();
            $table->string('payee_code')->nullable();
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
        Schema::dropIfExists('credo_responses');
    }
}
