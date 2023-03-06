<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionalColumnsToRegMonitor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reg_monitors', function (Blueprint $table) {
            $table->enum('s_status', array(0,1))->nullable()->default(0);
            $table->integer('s_tce')->nullable()->default(0);
            $table->integer('s_twgp')->nullable()->default(0);
            $table->integer('s_cgpa')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reg_monitors', function (Blueprint $table) {
            $table->dropColumn('s_status');
            $table->dropColumn('s_tce');
            $table->dropColumn('s_twgp');
            $table->dropColumn('s_cgpa');
        });
    }
}
