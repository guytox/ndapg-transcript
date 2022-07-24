<?php

use App\Models\Department;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemesterCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semester_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments','id');
            $table->string('courseCode');
            $table->string('courseTitle');
            $table->integer('creditUnits');
            $table->string('courseDescription')->nullable();
            $table->integer('max_ca')->nullable()->default(30);
            $table->integer('max_exam')->nullable()->default(70);
            $table->boolean('activeStatus')->default(1);
            //$table->foreign('department_id')
            //        ->references('id')->on('departments')->onDelete('cascade');
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
        Schema::dropIfExists('semester_courses');
    }
}
