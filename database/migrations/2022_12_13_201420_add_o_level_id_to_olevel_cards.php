<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOLevelIdToOlevelCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('olevel_cards', function (Blueprint $table) {
            $table->foreignId('result_id')->nullable()->constrained('olevel_results','id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('olevel_cards', function (Blueprint $table) {
            $table->dropConstrainedForeignId('result_id');
        });
    }
}
