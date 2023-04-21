<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeffermentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defferments', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('student_records','id');
            $table->foreignId('d_session')->nullable()->constrained('academic_sessions','id');
            $table->integer('amount_payable')->nullable()->default(0);
            $table->string('r_session')->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('defferments');
    }
}
