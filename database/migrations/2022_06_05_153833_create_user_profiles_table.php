<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('gender')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced'])->default('single');
            $table->date('dob')->nullable();
            $table->string('nationality')->default('Nigeria')->nullable();
            $table->string('state_id')->nullable();
            $table->string('local_government')->nullable();
            $table->string('town')->nullable();
            $table->string('extra_curricular')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('permanent_home_address')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreignId('department_id')->constrained('departments','id');
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
        Schema::dropIfExists('user_profiles');
    }
}
