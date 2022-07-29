<?php

use App\Models\AcademicSession;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadedPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('student_records','id');
            $table->foreignId('session_id')->constrained('academic_sessions','id');
            $table->foreignId('uploaded_by')->constrained('users','id');
            $table->integer('amount_paid')->nullable();
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
        Schema::dropIfExists('uploaded_payments');
    }
}
