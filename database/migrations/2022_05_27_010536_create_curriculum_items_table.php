<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCurriculumItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curricula_id')->constrained('curricula','id')->onDelete('cascade');
            $table->foreignId('semester_courses_id')->constrained('semester_courses','id');
            $table->string('category')->default('core');
            $table->string('alternative')->nullable();
            $table->unique(array('semester_courses_id','curricula_id'));
            $table->unique(array('semester_courses_id','alternative','curricula_id'),'dup_check');
            $table->timestamps();
        });

        DB::unprepared('CREATE TRIGGER insert_Total_Courses AFTER INSERT ON `curriculum_items`
            FOR EACH ROW
                BEGIN
                   UPDATE `curricula`  SET `numOfCourses` = (select count(`semester_courses_id`) from curriculum_items f where f.curricula_id= NEW.curricula_id)
                   WHERE
                   `curricula`.`id` = NEW.curricula_id ;
                END');


        DB::unprepared('CREATE TRIGGER update_Total_Courses AFTER UPDATE ON `curriculum_items`
            FOR EACH ROW
                BEGIN
                UPDATE `curricula`  SET `numOfCourses` = (select count(`semester_courses_id`) from curriculum_items f where f.curricula_id= NEW.curricula_id)
                WHERE
                `curricula`.`id` = NEW.curricula_id ;
                END');

        DB::unprepared('CREATE TRIGGER delete_Total_Courses AFTER DELETE ON `curriculum_items`
            FOR EACH ROW
                BEGIN
                UPDATE `curricula`  SET `numOfCourses` = (select count(`semester_courses_id`) from curriculum_items f where f.curricula_id= OLD.curricula_id)
                WHERE
                `curricula`.`id` = OLD.curricula_id ;
                END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curriculum_items');
    }
}
