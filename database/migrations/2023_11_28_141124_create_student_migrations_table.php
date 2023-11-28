<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentMigrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_migrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('student_records','id');
            $table->integer('session_id');
            $table->integer('old_year');
            $table->integer('new_year');
            $table->integer('migration_status')->default(0);
            $table->foreignId('recommended_by')->nullable()->constrained('users','id');
            $table->timestamp('recommended_at')->nullable();
            $table->foreignId('migrated_by')->nullable()->constrained('users','id');
            $table->timestamp('migrated_at')->nullable();
            $table->unique(['session_id','student_id'],'migration_dup_check');
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
        Schema::dropIfExists('student_migrations');
    }
}
