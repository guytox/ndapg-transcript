<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefToTranscriptRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transcript_requests', function (Blueprint $table) {
            $table->string('ug_ref')->nullable()->after('uid');
            $table->string('ug_mssg')->nullable()->after('ug_ref');
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
            $table->dropColumn('ug_ref');
            $table->dropColumn('ug_mssg');
        });
    }
}
