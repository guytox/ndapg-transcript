<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToResearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_research', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users','id')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('academic_sessions','id');
            $table->string('summary')->nullable();
            $table->string('path')->nullable();
            $table->unique(array('user_id','session_id'),'research_dup_check');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_research', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('summary');
            $table->dropColumn('session_id');
            $table->dropColumn('path');
        });
    }
}
