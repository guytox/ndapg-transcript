<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseAllocationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('course_allocation_items', function (Blueprint $table) {

            $table->id();
            $table->foreignId('allocation_id')->constrained('course_allocation_monitors', 'id')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('semester_courses', 'id');
            $table->foreignId('staff_id')->constrained('users', 'id');
            $table->enum('can_grade', array(1,2))->default(2);
            $table->enum('grading_completed', array(1,2))->default(2);
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
        Schema::dropIfExists('course_allocation_items');
    }
}
