<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToTranscriptRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transcript_requests', function (Blueprint $table) {
            $table->integer('fconfig')->nullable()->after('matric');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transcript_requests', function (Blueprint $table) {
            $table->dropColumn('fconfig');
        });
    }
}
