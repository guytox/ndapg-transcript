<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradingSystemItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grading_system_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_system_id')->constrained('grading_systems', 'id')->onDelete('cascade');
            $table->integer('lower_boundary');
            $table->integer('upper_boundary');
            $table->string('grade_letter');
            $table->integer('credit_earned')->default(0);
            $table->integer('weight_points');
            $table->unique(['grading_system_id','grade_letter'],'gradeitemDupCheck');
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
        Schema::dropIfExists('grading_system_items');
    }
}
