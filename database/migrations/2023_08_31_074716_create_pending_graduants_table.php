<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingGraduantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_graduants', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->foreignId('student_id')->constrained('student_records','id');
            $table->foreignId('user_id')->constrained('users','id');
            $table->foreignId('program_id')->constrained('programs','id');
            $table->foreignId('result_id')->constrained('reg_monitors','id');
            $table->foreignId('grad_session_id')->nullable()->constrained('academic_sessions','id');
            $table->foreignId('grad_semester_id')->nullable()->constrained('semesters','id');
            $table->integer('grad_cgpa')->nullable();
            $table->string('degree_class')->nullable();
            $table->string('approval_status')->nullable()->default('pending');
            $table->foreignId('pg_coord_by')->constrained('users','id');
            $table->integer('pg_coord')->default(0);
            $table->timestamp('pg_coord_at')->nullable();
            $table->foreignId('hod_by')->nullable()->constrained('users','id');
            $table->integer('hod')->default(0);
            $table->timestamp('hod_at')->nullable();
            $table->foreignId('dean_by')->nullable()->constrained('users','id');
            $table->integer('dean')->default(0);
            $table->timestamp('dean_at')->nullable();
            $table->foreignId('dean_spgs_by')->nullable()->constrained('users','id');
            $table->integer('dean_spgs')->default(0);
            $table->timestamp('dean_spgs_at')->nullable();
            $table->foreignId('senate_by')->nullable()->constrained('users','id');
            $table->integer('senate')->default(0);
            $table->timestamp('senate_at')->nullable();
            $table->unique(['student_id','user_id','program_id'], 'grad_dup_check');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_graduants');
    }
}
