<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelToFeePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->string('channel')->nullable()->default('credo');
            $table->foreignId('fee_config_id')->nullable()->constrained('fee_configs','id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropColumn('channel');
            $table->dropConstrainedForeignId('fee_config_id');
        });
    }
}
