<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrograAndServiceRecordsToUserProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->foreignId('applicant_program')->nullable()->constrained('programs','id');
            $table->enum('is_serving_officer', array('0','1'))->nullable()->default('0');
            $table->string('service_number')->nullable();
            $table->string('service_rank')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('applicant_program');
            $table->dropColumn('is_serving_officer');
            $table->dropColumn('service_number');
            $table->dropColumn('service_rank');
        });
    }
}
