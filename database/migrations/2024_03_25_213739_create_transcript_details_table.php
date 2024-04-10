<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranscriptDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcript_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('transcript_requests','id')->onDelete('cascade');
            $table->foreignId('t_type')->constrained('transcript_types','id');
            $table->integer('transcript_id')->nullable();
            $table->string('matric');
            $table->integer('express')->nullable()->default(0);
            $table->integer('admissionYear')->nullable();
            $table->integer('graduationYear')->nullable();
            $table->foreignId('d_option')->constrained('transcript_delivery_modes','id');
            $table->string('receiver_email')->nullable();
            $table->string('receiver')->nullable();
            $table->string('establishment')->nullable();
            $table->string('street')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->foreignId('country')->nullable()->constrained('countries','id');
            $table->integer('verifications')->default(0);
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
        Schema::dropIfExists('transcript_details');
    }
}
