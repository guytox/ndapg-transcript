<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIssueFileToApplicantAdmissionRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applicant_admission_requests', function (Blueprint $table) {
            $table->integer('file_issued')->default(0);
            $table->integer('file_issued_by')->nullable();
            $table->timestamp('file_issued_at')->nullable();
            $table->integer('reg_courses')->default(0);
            $table->timestamp('reg_courses_at')->nullable();
            $table->integer('student_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applicant_admission_requests', function (Blueprint $table) {
            $table->dropColumn('file_issued');
            $table->dropColumn('file_issued_by');
            $table->dropColumn('file_issued_at');
            $table->dropColumn('reg_courses');
            $table->dropColumn('reg_courses_at');
            $table->dropColumn('student_id');

        });
    }
}
