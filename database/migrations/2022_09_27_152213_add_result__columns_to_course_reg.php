<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddResultColumnsToCourseReg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reg_monitors', function (Blueprint $table) {
            $table->string('r_status')->default('pending');
            $table->integer('r_probation_count')->default(0);
            $table->integer('r_suspended')->default(0);
            $table->foreignId('r_computed_result_id')->nullable()->constrained('computed_results','id');
            $table->integer('cur')->default(0);
            $table->integer('cue')->default(0);
            $table->integer('wgp')->default(0);
            $table->integer('gpa')->default(0);
            $table->integer('ltcr')->default(0);
            $table->integer('ltce')->default(0);
            $table->integer('ltwgp')->default(0);
            $table->integer('lcgpa')->default(0);
            $table->integer('tcr')->default(0);
            $table->integer('tce')->default(0);
            $table->integer('twgp')->default(0);
            $table->integer('cgpa')->default(0);
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
            $table->dropConstrainedForeignId('r_computed_result_id');
            $table->dropColumn('r_status');
            $table->dropColumn('r_probation_count');
            $table->dropColumn('r_suspended');
            $table->dropColumn('cur');
            $table->dropColumn('cue');
            $table->dropColumn('wgp');
            $table->dropColumn('gpa');
            $table->dropColumn('ltcr');
            $table->dropColumn('ltce');
            $table->dropColumn('ltwgp');
            $table->dropColumn('lcgpa');
            $table->dropColumn('tcr');
            $table->dropColumn('tce');
            $table->dropColumn('twgp');
            $table->dropColumn('cgpa');
        });
    }
}
