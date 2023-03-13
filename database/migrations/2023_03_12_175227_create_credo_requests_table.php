<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCredoRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credo_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payee_id');
            $table->foreignId('fee_payment_id')->nullable()->constrained('fee_payments','id');
            $table->integer('amount');
            $table->integer('session_id')->nullable();
            $table->string('uid');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->string('txn_id')->nullable();
            $table->string('checksum')->nullable();
            $table->string('channel')->nullable()->default('credo');
            $table->string('credo_ref')->nullable();
            $table->string('credo_url')->nullable();
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
        Schema::dropIfExists('credo_requests');
    }
}
