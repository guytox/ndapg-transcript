<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToApplicationFeeRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_fee_requests', function (Blueprint $table) {
            $table->string('channel')->nullable()->default('credo');
            $table->string('credo_ref')->nullable();
            $table->string('credo_url')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_fee_requests', function (Blueprint $table) {
            $table->dropColumn('channel');
            $table->dropColumn('credo_ref');
            $table->dropColumn('credo_url');
        });
    }
}
