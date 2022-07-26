<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOlevelCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('olevel_cards', function (Blueprint $table) {
            $table->id();
            $table->string('exam_type');
            $table->year('exam_year');
            $table->string('card_pin');
            $table->enum('sitting', ['first', 'second'])->default('first');
            $table->string('card_serial_no');
            $table->string('exam_body');
            $table->enum('verification_status', ['approved', 'rejected', 'pending'])->default('pending');
            $table->bigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('olevel_cards');
    }
}
