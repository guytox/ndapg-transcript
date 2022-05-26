<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatricConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matric_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('application_number');
            $table->string('student_number');
            $table->integer('session_id');
            $table->integer('matric_count')->default(0);
            $table->integer('application_number_count')->default(0);
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
        Schema::dropIfExists('matric_configurations');
    }
}
