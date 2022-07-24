<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRegMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reg_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('student_records','id');
            $table->string('uid')->nullable()->unique();
            $table->foreignId('semester_id')->constrained('semesters','id');
            $table->foreignId('curricula_id')->constrained('curricula','id');
            $table->foreignId('session_id')->constrained('academic_sessions','id');
            $table->foreignId('level_id')->constrained('study_levels','id');
            $table->foreignId('program_id')->constrained('programs','id');
            $table->integer('semesters_spent')->default(0);
            $table->integer('total_credits')->default(0);
            $table->integer('num_of_courses')->default(0);
            $table->enum('std_confirm',array(0,1))->default(0);
            $table->enum('status', array('pending','approved'))->default('pending');
            $table->enum('ro_approval',array(0,1))->default(0);
            $table->foreignId('ro_approver')->nullable()->constrained('users','id');
            $table->string('ro_approvalDate')->nullable();
            $table->enum('hod_approval',array(0,1))->default(0);
            $table->foreignId('hod_approver')->nullable()->constrained('users','id');
            $table->string('hod_approvalDate')->nullable();
            $table->enum('dean_approval',array(0,1))->default(0);
            $table->foreignId('dean_approver')->nullable()->constrained('users','id');
            $table->string('dean_approvalDate')->nullable();
            $table->string('message')->nullable();
            $table->timestamps();
            $table->unique(array('student_id','curricula_id'));
        });




        DB::unprepared('CREATE TRIGGER insert_reg_Monitor AFTER INSERT ON `reg_monitors`
            FOR EACH ROW
                BEGIN
                UPDATE `student_records`  SET `semesters_spent` = NEW.semesters_spent
                WHERE
                `student_records`.`id` = NEW.student_id ;
                END');

        DB::unprepared('CREATE TRIGGER update_Reg_Approval_Status AFTER UPDATE ON `reg_monitors`
            FOR EACH ROW
                BEGIN
                IF(OLD.`status` != NEW.`status`)
                THEN
                UPDATE `reg_monitor_items`  SET `status` = NEW.status, `monitor_id` = NEW.id
                WHERE
                `reg_monitor_items`.`monitor_id` = OLD.id ;
                END IF;
                END');

        DB::unprepared('CREATE TRIGGER delete_reg_Monitor AFTER DELETE ON `reg_monitors`
            FOR EACH ROW
                BEGIN
                UPDATE `student_records`  SET `semesters_spent` = semesters_spent-1
                WHERE
                `student_records`.`id` = OLD.student_id ;
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
        Schema::dropIfExists('reg_monitors');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
