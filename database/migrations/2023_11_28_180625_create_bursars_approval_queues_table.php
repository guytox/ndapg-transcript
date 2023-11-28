<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBursarsApprovalQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bursars_approval_queues', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('uid');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->bigInteger('payment_config_id');
            $table->bigInteger('academic_session_id');
            $table->integer('amount_billed');
            $table->string('txn_id')->nullable();
            $table->string('billing_by')->nullable();
            $table->integer('fee_payment_id')->nullable();
            $table->unique(array('user_id', 'payment_config_id','academic_session_id'),'feepayment_dup_check');
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
        Schema::dropIfExists('bursars_approval_queues');
    }
}
