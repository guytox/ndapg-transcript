<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('awarding_institution')->nullable();
            $table->string('uid');
            $table->string('certificate_type')->nullable();
            $table->string('qualification_obtained')->nullable();
            $table->string('year_obtained')->nullable();
            $table->string('class')->nullable();
            $table->enum('type', ['school', 'professional'])->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('certificate_no')->nullable();
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
        Schema::dropIfExists('user_qualifications');
    }
}
