<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCourseAllocationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('course_allocation_items', function (Blueprint $table) {

            $table->id();
            $table->string('uid');
            $table->foreignId('allocation_id')->constrained('course_allocation_monitors', 'id')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('semester_courses', 'id');
            $table->foreignId('staff_id')->constrained('users', 'id');
            $table->enum('can_grade', array(1,2))->default(2);
            $table->enum('graded', array(1,2))->default(2);
            $table->enum('grading_completed', array(1,2))->default(2);
            $table->enum('submitted', array(1,2))->default(2);
            $table->foreignId('submitted_by')->nullable()->constrained('users','id');
            $table->string('submitted_at')->nullable();
            $table->enum('accepted', array(1,2))->default(2);
            $table->foreignId('accepted_by')->nullable()->constrained('users','id');
            $table->string('accepted_at')->nullable();
            $table->string('cfm_ca1')->default(0);
            $table->string('cfm_ca2')->default(0);
            $table->string('cfm_ca3')->default(0);
            $table->string('cfm_ca4')->default(0);
            $table->string('cfm_exam')->default(0);
            $table->string('c1')->default(0);
            $table->string('c2')->default(0);
            $table->string('c3')->default(0);
            $table->string('c4')->default(0);
            $table->string('exam')->default(0);
            $table->string('changeLog')->nullable();
            $table->timestamps();

        });

        DB::unprepared('CREATE TRIGGER update_alloc_items AFTER UPDATE ON `course_allocation_items`
            FOR EACH ROW
                BEGIN
                IF (NEW.cfm_ca1!= OLD.cfm_ca1 OR NEW.cfm_ca2!= OLD.cfm_ca2 OR NEW.cfm_ca3!= OLD.cfm_ca3 OR NEW.cfm_ca4!= OLD.cfm_ca4 OR NEW.cfm_exam!= OLD.cfm_exam)
                THEN
                    UPDATE `reg_monitor_items`
                    SET `reg_monitor_items`.`cfm_ca1` = NEW.cfm_ca1, `reg_monitor_items`.`cfm_ca2` = NEW.cfm_ca2, `reg_monitor_items`.`cfm_ca3` = NEW.cfm_ca3, `reg_monitor_items`.`cfm_ca4` = NEW.cfm_ca4, `reg_monitor_items`.`cfm_exam` = NEW.cfm_exam, `reg_monitor_items`.`ltotal` = (`reg_monitor_items`.`ca1`+ `reg_monitor_items`.`ca2`+ `reg_monitor_items`.`ca3`+ `reg_monitor_items`.`ca4`+ `reg_monitor_items`.`exam`)
                    WHERE `reg_monitor_items`.`course_id` = OLD.course_id
                    AND `reg_monitor_items`.`session_id` = (SELECT a.session_id from course_allocation_items i inner join course_allocation_monitors a on a.id=i.allocation_id where i.course_id = OLD.course_id)
                    AND `reg_monitor_items`.`semester_id` = (SELECT a.semester_id from course_allocation_items i inner join course_allocation_monitors a on a.id=i.allocation_id where i.course_id = OLD.course_id);
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
        Schema::dropIfExists('course_allocation_items');
    }
}
