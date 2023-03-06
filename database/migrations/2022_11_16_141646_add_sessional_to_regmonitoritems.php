<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionalToRegmonitoritems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reg_monitor_items', function (Blueprint $table) {

            $table->enum('is_reg_sess', array('0','1'))->nullable()->default('0');
            $table->enum('sess_is_passed', array('0','1'))->nullable()->default('0');
            $table->integer('sess_total')->nullable()->default(0);
            $table->string('sess_grade')->nullable();
            $table->integer('sess_twgp')->nullable()->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reg_monitor_items', function (Blueprint $table) {
            $table->dropColumn('is_reg_sess');
            $table->dropColumn('sess_is_passed');
            $table->dropColumn('sess_total');
            $table->dropColumn('sess_grade');
            $table->dropColumn('sess_twgp');
        });
    }
}
