<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegClearancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reg_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('student_records','id');
            $table->foreignId('school_session_id')->constrained('academic_sessions','id');
            $table->integer('first_semester')->nullable()->default(0);
            $table->integer('second_semester')->nullable()->default(0);
            $table->integer('status')->default(1);
            $table->unique(array('student_id','school_session_id'),'clearance_dup_check');
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
        Schema::dropIfExists('reg_clearances');
    }
}
