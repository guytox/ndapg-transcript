<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToApplicantAdmissionRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applicant_admission_requests', function (Blueprint $table) {
            $table->integer('adm_notification')->default(0);
            $table->integer('acc_verified')->default(0);
            $table->integer('acc_verified_by')->nullable();
            $table->timestamp('acc_verified_at')->nullable();
            $table->integer('schfee_verified')->default(0);
            $table->integer('schfee_verified_by')->nullable();
            $table->timestamp('schfee_verified_at')->nullable();
            $table->integer('reg_clearance')->default(0);
            $table->integer('reg_clearance_by')->nullable();
            $table->timestamp('reg_clearance_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applicant_admission_requests', function (Blueprint $table) {
            $table->dropColumn('adm_notification');
            $table->dropColumn('acc_verified');
            $table->dropColumn('acc_verified_by');
            $table->dropColumn('acc_verified_at');
            $table->dropColumn('schfee_verified');
            $table->dropColumn('schfee_verified_by');
            $table->dropColumn('schfee_verified_at');
            $table->dropColumn('reg_clearance');
            $table->dropColumn('reg_clearance_by');
            $table->dropColumn('reg_clearance_at');

        });
    }
}
