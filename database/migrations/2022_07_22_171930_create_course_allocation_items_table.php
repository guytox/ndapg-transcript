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
            $table->string('uid');
            $table->foreignId('allocation_id')->constrained('course_allocation_monitors', 'id')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('semester_courses', 'id');
            $table->foreignId('staff_id')->constrained('users', 'id');
            $table->enum('can_grade', array(1,2))->default(2);
            $table->enum('graded', array(1,2))->default(2);
            $table->enum('grading_completed', array(1,2))->default(2);
            $table->enum('submitted', array(1,2))->default(2);
            $table->foreignId('submitted_by')->nullable()->constrained('users','id');
            $table->string('submitted_at')->nullable();
            $table->enum('accepted', array(1,2))->default(2);
            $table->foreignId('accepted_by')->nullable()->constrained('users','id');
            $table->string('accepted_at')->nullable();
            $table->string('cfm_ca1')->default(0);
            $table->string('cfm_ca2')->default(0);
            $table->string('cfm_ca3')->default(0);
            $table->string('cfm_ca4')->default(0);
            $table->string('cfm_exam')->default(0);
            $table->string('c1')->default(0);
            $table->string('c2')->default(0);
            $table->string('c3')->default(0);
            $table->string('c4')->default(0);
            $table->string('exam')->default(0);
            $table->string('changeLog')->nullable();
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
