<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

class CreateStudentRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->unique();
            $table->foreignId('program_id')->constrained('programs','id');
            $table->string('matric')->unique();
            $table->integer('study_year')->default(1);
            $table->boolean('in_defferment')->default(false);
            $table->boolean('is_suspended')->default(false);
            $table->boolean('has_graduated')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->boolean('is_on_siwes')->default(false);
            $table->string('disability')->nullable();
            $table->integer('state_origin')->nullable();
            $table->integer('semesters_spent')->default(0);
            $table->string('dob')->nullable();
            $table->string('jamb_no')->nullable();
            $table->string('admission_session')->nullable();
            $table->string('graduation_session')->nullable();
            $table->string('room_number')->nullable();
            $table->integer('grading_system')->default(1);
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
        Schema::dropIfExists('student_records');
    }
}
