<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateComputedResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('computed_results', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->string('cr_status')->default('pending');
            $table->foreignId('program_id')->constrained('programs','id');
            $table->foreignId('schoolsession_id')->constrained('academic_sessions','id');
            $table->foreignId('semester_id')->constrained('semesters','id');
            $table->foreignId('study_level')->constrained('study_levels','id');
            $table->foreignId('computed_by')->constrained('users','id');
            $table->foreignId('eo_approver')->nullable()->constrained('users','id');
            $table->foreignId('hod_approver')->nullable()->constrained('users','id');
            $table->foreignId('dean_approver')->nullable()->constrained('users','id');
            $table->foreignId('commitee_approver')->nullable()->constrained('users','id');
            $table->foreignId('senate_approver')->nullable()->constrained('users','id');
            $table->integer('eo_approval')->nullable()->default(0);
            $table->integer('hod_approval')->nullable()->default(0);
            $table->integer('dean_approval')->nullable()->default(0);
            $table->integer('committee_approval')->nullable()->default(0);
            $table->integer('senate_approval')->nullable()->default(0);
            $table->string('computed_at');
            $table->string('eo_approved_at');
            $table->string('hod_approved_at');
            $table->string('dean_approved_at');
            $table->string('committee_approved_at');
            $table->string('senate_approved_at');
            $table->string('last_updated_at');
            $table->timestamps();
            $table->unique(array('program_id','schoolsession_id','semester_id','study_level'),'duplicate_check');
        });

        DB::unprepared('CREATE TRIGGER update_reg_monitor AFTER UPDATE ON `computed_results`

            FOR EACH ROW
                BEGIN

                IF(OLD.`cr_status` != NEW.`cr_status`)

                THEN

                UPDATE `reg_monitors`  SET `reg_monitors`.`r_status` = NEW.cr_status
                WHERE

                `reg_monitors`.`r_computed_result_id` = OLD.id ;

                END IF;

                END');




    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('computed_results');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
