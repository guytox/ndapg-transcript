<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurriculaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->integer('programs_id');
            $table->string('title');
            $table->string('semester');
            $table->string('uid');
            $table->integer('studyLevel');
            $table->integer('studyYear');
            $table->integer('minRegCredits');
            $table->integer('maxRegCredits');
            $table->integer('numOfCourses')->default(0);
            $table->boolean('active')->default(1);
            $table->unique(array('programs_id', 'semester','studyLevel','studyYear'),'curricula_dup_check');
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
        Schema::dropIfExists('curricula');
    }
}
