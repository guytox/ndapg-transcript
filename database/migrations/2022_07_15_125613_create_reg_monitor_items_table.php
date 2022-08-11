<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRegMonitorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reg_monitor_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_id')->constrained('reg_monitors','id')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('student_records','id');
            $table->foreignId('course_id')->constrained('semester_courses','id');
            $table->foreignId('session_id')->constrained('academic_sessions','id');
            $table->foreignId('semester_id')->constrained('semesters','id');
            $table->enum('category',array('core', 'elective'));
            $table->enum('status',array('pending', 'approved'))->default('pending');
            $table->integer('is_carryOver')->default(0);
            $table->enum('is_passed',array(0, 1))->default(0);
            $table->enum('is_co_passed',array(0, 1))->default(0);
            $table->enum('is_suspended',array(0, 1))->default(0);
            $table->foreignId('suspended_by')->nullable()->constrained('users','id');
            $table->string('suspended_at')->nullable();
            $table->enum('cfm_ca1',array(0, 1))->default(0);
            $table->enum('cfm_ca2',array(0, 1))->default(0);
            $table->enum('cfm_ca3',array(0, 1))->default(0);
            $table->enum('cfm_ca4',array(0, 1))->default(0);
            $table->enum('cfm_exam',array(0, 1))->default(0);
            $table->integer('ca1')->default(0);
            $table->integer('ca2')->default(0);
            $table->integer('ca3')->default(0);
            $table->integer('ca4')->default(0);
            $table->integer('exam')->default(0);
            $table->integer('ltotal')->default(0);
            $table->string('lgrade')->default(0);
            $table->integer('gtotal')->default(0);
            $table->string('ggrade')->default(0);
            $table->timestamps();
            $table->unique(array('semester_id','student_id','course_id','session_id'),'dupicate_course');
        });


        DB::unprepared('CREATE TRIGGER insert_Course AFTER INSERT ON `reg_monitor_items`
            FOR EACH ROW
                BEGIN
                    UPDATE `reg_monitors`  SET `num_of_courses` = (select count(`course_id`) from reg_monitor_items f where f.  monitor_id= NEW.monitor_id) WHERE `reg_monitors`.`id` = NEW.monitor_id ;

                    UPDATE `reg_monitors`  SET `total_credits` = (select sum(`creditUnits`) from semester_courses s inner join `reg_monitor_items` f on f.course_id=s.id where f.monitor_id= NEW.monitor_id) WHERE `reg_monitors`.`id` = NEW.monitor_id ;

                END');


        




        DB::unprepared('CREATE TRIGGER delete_Course AFTER DELETE ON `reg_monitor_items`
            FOR EACH ROW
                BEGIN
                    UPDATE `reg_monitors`  SET `num_of_courses` = (select count(`course_id`) from reg_monitor_items f where f.  monitor_id= OLD.monitor_id) WHERE `reg_monitors`.`id` = OLD.monitor_id ;

                   UPDATE `reg_monitors`  SET `total_credits` = (select sum(`creditUnits`) from  semester_courses s inner join `reg_monitor_items`  f on f.course_id=s.id where f.monitor_id= OLD.monitor_id) WHERE `reg_monitors`.`id` = OLD.monitor_id ;
                END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reg_monitor_items');
    }
}
