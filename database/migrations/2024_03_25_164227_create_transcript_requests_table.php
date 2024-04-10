<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranscriptRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcript_requests', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->foreignId('user_id')->constrained('users','id')->onDelete('cascade');
            $table->string('matric');
            $table->foreignId('t_type')->constrained('transcript_types','id');
            $table->integer('p')->default(0);
            $table->integer('ts')->default(0);
            $table->timestamp('ts_at')->nullable();
            $table->integer('pu')->default(0);
            $table->integer('pu_by')->nullable();
            $table->timestamp('pu_at')->nullable();
            $table->integer('tp')->default(0);
            $table->integer('tp_by')->nullable();
            $table->timestamp('tp_at')->nullable();
            $table->integer('tv')->default(0);
            $table->integer('tv_by')->nullable();
            $table->timestamp('tv_at')->nullable();
            $table->integer('td')->default(0);
            $table->integer('td_by')->nullable();
            $table->timestamp('td_at')->nullable();
            $table->integer('tr')->default(0);
            $table->string('tr_by')->nullable();
            $table->timestamp('tr_at')->nullable();
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
        Schema::dropIfExists('transcript_requests');
    }
}
