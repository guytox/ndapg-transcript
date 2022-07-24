<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseAllocationMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_allocation_monitors', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->foreignId('department_id')->constrained('departments','id');
            $table->foreignId('session_id')->constrained('academic_sessions','id');
            $table->foreignId('semester_id')->constrained('semesters', 'id');
            $table->foreignId('created_by')->constrained('users','id');
            $table->unique(array('department_id','session_id','semester_id'),'double_dept_allocation');
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
        Schema::dropIfExists('course_allocation_monitors');
    }
}
