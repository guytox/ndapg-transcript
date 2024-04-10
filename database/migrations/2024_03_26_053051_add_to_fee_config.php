<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToFeeConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_configs', function (Blueprint $table) {
            $table->integer('in_country')->nullable()->default(1)->after('narration');
            $table->integer('physical')->nullable()->default(1)->after('in_country');
            $table->integer('express')->nullable()->default(1)->after('physical');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fee_configs', function (Blueprint $table) {
            $table->dropColumn('in_country');
            $table->dropColumn('physical');
            $table->dropColumn('express');
        });
    }
}
