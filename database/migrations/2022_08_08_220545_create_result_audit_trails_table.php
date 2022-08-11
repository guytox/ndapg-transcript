<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultAuditTrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users','id');
            $table->string('changes');
            $table->string('old_values');
            $table->string('new_values');
            $table->foreignId('course_id')->constrained('semester_courses','id');
            $table->foreignId('session_id')->constrained('academic_sessions','id');
            $table->foreignId('semester_id')->constrained('semesters','id');
            $table->foreignId('student_id')->constrained('student_records','id');
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
        Schema::dropIfExists('result_audit_trails');
    }
}
