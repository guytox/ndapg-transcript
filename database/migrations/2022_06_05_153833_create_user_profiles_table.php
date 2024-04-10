<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users','id')->onDelete('cascade');
            $table->integer('ndanumber')->nullable()->unique();
            $table->integer('regularcourse')->nullable();
            $table->integer('batallion')->nullable();
            $table->foreignId('ndaservice')->nullable()->constrained('nda_services','id');
            $table->integer('ugadmissionyear')->nullable();
            $table->integer('uggraduationyear')->nullable();
            $table->integer('commissiondate')->nullable();
            $table->string('pgnumber')->nullable()->unique();
            $table->integer('pgadmissionyear')->nullable();
            $table->integer('pggraduationyear')->nullable();
            $table->foreignId('gender')->nullable()->constrained('genders','id');
            $table->enum('marital_status', ['single', 'married', 'divorced'])->default('single');
            $table->date('dob')->nullable();
            $table->string('surname');
            $table->string('othernames');
            $table->integer('nationality')->default(160)->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('local_government')->nullable();
            $table->string('town')->nullable();
            $table->string('extra_curricular')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('permanent_home_address')->nullable();
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
        Schema::dropIfExists('user_profiles');
    }
}
