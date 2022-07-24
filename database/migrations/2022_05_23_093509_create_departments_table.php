<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->foreignId('hod_id')->nullable()->constrained('users','id');
            $table->foreignId('exam_officer_id')->nullable()->constrained('users','id');
            $table->foreignId('registration_officer_id')->nullable()->constrained('users','id');
            $table->foreignId('faculty_id')->constrained('faculties');
            $table->enum('academic',[1,2])->default(2);
            $table->string('uid');
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
        Schema::dropIfExists('departments');
    }
}
