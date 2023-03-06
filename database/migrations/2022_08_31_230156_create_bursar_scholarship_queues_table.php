<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBursarScholarshipQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bursar_scholarship_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->foreignId('fee_payment_id')->constrained('fee_payments','id');
            $table->foreignId('scholarship_category')->constrained('scholarships','id');
            $table->foreignId('academic_session_id')->constrained('academic_sessions','id');
            $table->integer('proposed_amount');
            $table->foreignId('billed_by')->constrained('users', 'id');
            $table->foreignId('checked_by')->nullable()->constrained('users', 'id');
            $table->enum('bill_confirmed',[0,1])->default(0);
            $table->enum('bill_checked',[0,1])->default(0);
            $table->enum('bill_approved',[0,1])->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users','id');
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
        Schema::dropIfExists('bursar_scholarship_queues');
    }
}
