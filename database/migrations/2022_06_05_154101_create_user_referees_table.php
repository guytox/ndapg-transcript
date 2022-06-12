<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRefereesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_referees', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->boolean('is_filled')->default(false);
            $table->string('email');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->date('expiry_date');

            $table->integer('candidate_referee_relationship_years')->nullable();
            $table->string('candidate_relationship')->nullable();

            // academics
            $table->string('intellectual_ability')->nullable();
            $table->string('capacity_for_persistent_academic_study')->nullable();
            $table->string('capacity_for_independent_academic_study')->nullable();
            $table->string('ability_for_imaginative_thought')->nullable();
            $table->string('ability_for_oral_expression_in_english')->nullable();
            $table->string('ability_for_written_expression_in_english')->nullable();
            $table->string('candidate_rank_academically_among_students_in_last_five_years')->nullable();

            //personality
            $table->enum('candidate_morally_upright', ['yes', 'no'])->nullable();
            $table->enum('candidate_emotionally_stable', ['yes', 'no'])->nullable();
            $table->enum('candidate_physically_fit', ['yes', 'no'])->nullable();
            $table->enum('accept_candidate_for_research', ['yes', 'no'])->nullable();
            $table->string('reason_for_rejecting_candidate_for_research')->nullable();
            $table->string('general_comment')->nullable();
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
        Schema::dropIfExists('user_referees');
    }



}
