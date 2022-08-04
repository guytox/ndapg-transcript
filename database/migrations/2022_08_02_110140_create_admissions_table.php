<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('faculty')->nullable();
            $table->integer('session_id')->nullable();
            $table->string('category')->nullable();
            $table->string('form_number')->nullable();
            $table->string('surname')->nullable();
            $table->string('other_names')->nullable();
            $table->string('state')->nullable();
            $table->string('programme')->nullable();
            $table->integer('programme_id')->nullable();
            $table->string('department')->nullable();
            $table->string('country')->nullable();
            $table->string('gender')->nullable();
            $table->string('qualifications')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('admissions');
    }
}
