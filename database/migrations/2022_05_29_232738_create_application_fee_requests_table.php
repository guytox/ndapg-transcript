<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationFeeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_fee_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payee_id');
            $table->integer('amount');
            $table->string('uid');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->string('txn_id')->nullable();
            $table->string('checksum')->nullable();
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
        Schema::dropIfExists('application_fee_requests');
    }
}
