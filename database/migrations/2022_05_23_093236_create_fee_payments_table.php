<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('request_id')->nullable();
            $table->string('uid');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->bigInteger('payment_config_id');
            $table->integer('amount_billed');
            $table->string('txn_id')->nullable();
            $table->integer('amount_paid')->nullable();
            $table->integer('balance')->nullable();
            $table->string('checksum')->nullable();
            $table->string('billing_by')->nullable();
            $table->unique(array('user_id', 'payment_config_id','request_id'),'feepayment_dup_check');
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
        Schema::dropIfExists('fee_payments');
    }
}
