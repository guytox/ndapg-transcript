<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantAdmissionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicant_admission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users','id');
            $table->foreignId('session_id')->constrained('academic_sessions','id');
            $table->foreignId('program_id')->nullable()->constrained('programs','id');
            $table->string('form_number')->nullable();
            $table->string('uid')->nullable();
            $table->boolean('is_submitted')->nullable()->default(false);
            $table->integer('downloaded')->nullable()->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('is_admitted')->nullable()->default(false);
            $table->timestamp('admitted_at')->nullable();
            $table->foreignId('admitted_by')->nullable()->constrained('users','id');
            $table->boolean('acceptance_paid')->nullable()->default(false);
            $table->timestamp('acceptance_paid_at')->nullable();
            $table->boolean('is_screened')->nullable()->default(false);
            $table->timestamp('is_screened_at')->nullable();
            $table->foreignId('is_screened_by')->nullable()->constrained('users','id');
            $table->boolean('is_paid_tuition')->nullable()->default(false);
            $table->timestamp('paid_tuition_at')->nullable();
            $table->boolean('is_olevel_verified')->nullable()->default(false);
            $table->timestamp('olevel_verified_at')->nullable();
            $table->foreignId('olevel_verified_by')->nullable()->constrained('users','id');
            $table->boolean('is_sent_dept')->nullable()->default(false);
            $table->timestamp('sent_dept_at')->nullable();
            $table->foreignId('sent_dept_by')->nullable()->constrained('users','id');
            $table->boolean('pg_coord')->nullable()->default(false);
            $table->timestamp('pg_coord_at')->nullable();
            $table->foreignId('pg_coord_by')->nullable()->constrained('users','id');
            $table->boolean('hod')->nullable()->default(false);
            $table->timestamp('hod_at')->nullable();
            $table->foreignId('hod_by')->nullable()->constrained('users','id');
            $table->boolean('dean')->nullable()->default(false);
            $table->timestamp('dean_at')->nullable();
            $table->foreignId('dean_by')->nullable()->constrained('users','id');
            $table->boolean('dean_spgs')->nullable()->default(false);
            $table->timestamp('dean_spgs_at')->nullable();
            $table->foreignId('dean_spgs_by')->nullable()->constrained('users','id');
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
        Schema::dropIfExists('applicant_admission_requests');
    }
}
