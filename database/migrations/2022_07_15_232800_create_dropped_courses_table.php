<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDroppedCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dropped_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('student_records','id');
            $table->foreignId('course_id')->constrained('semester_courses','id');
            $table->foreignId('sesstion_id')->constrained('academic_sessions','id');
            $table->foreignId('semester_id')->constrained('semesters','id');
            $table->enum('category', array('core','elective'));
            $table->integer('status')->default(0);
            $table->unique(array('student_id','course_id'));
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
        Schema::dropIfExists('dropped_courses');
    }
}
