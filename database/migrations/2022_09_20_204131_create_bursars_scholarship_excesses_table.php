<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBursarsScholarshipExcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bursars_scholarship_excesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('student_records','id');
            $table->integer('scholarship_category');
            $table->integer('school_session');
            $table->integer('scholarship_amount');
            $table->foreignId('uploaded_by')->constrained('users','id');
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
        Schema::dropIfExists('bursars_scholarship_excesses');
    }
}
