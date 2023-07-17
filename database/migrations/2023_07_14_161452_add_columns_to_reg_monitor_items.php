<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRegMonitorItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reg_monitor_items', function (Blueprint $table) {
            $table->integer('twgp')->nullable()->default(0);
            $table->integer('co_sem_spent')->nullable()->default(0);
            $table->integer('co_passed_sem_spent')->nullable()->default(0);
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
            $table->dropColumn('twgp');
            $table->dropColumn('co_sem_spent');
            $table->dropColumn('co_passed_sem_spent');
        });
    }
}
