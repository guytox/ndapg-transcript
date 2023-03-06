<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeeConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->foreignId('fee_template_id')->constrained();
            $table->foreignId('fee_category_id')->constrained();
            $table->string('narration');
            $table->foreignId('study_level_id')->nullable()->constrained();
            $table->foreignId('program_id')->nullable()->constrained();
            $table->foreignId('semester_id')->nullable()->constrained();
            $table->integer('in_state')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users','id');
            $table->unique(array('fee_template_id', 'fee_category_id','narration','program_id','semester_id', 'in_state','study_level_id'), 'fee_config_duplicate_check');
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
        Schema::dropIfExists('fee_configs');
    }
}
