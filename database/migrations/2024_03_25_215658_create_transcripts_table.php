<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->foreignId('user_id')->constrained('users','id')->onDelete('cascade');
            $table->string('matric')->unique();
            $table->integer('admissionYear')->nullable();
            $table->integer('graduationYear')->nullable();
            $table->string('grad_reason')->nullable();
            $table->integer('program_id')->nullable();
            $table->string('degree_title')->nullable();
            $table->string('degree_class')->nullable();
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
        Schema::dropIfExists('transcripts');
    }
}
