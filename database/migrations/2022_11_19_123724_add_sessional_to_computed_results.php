<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionalToComputedResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('computed_results', function (Blueprint $table) {
            $table->enum('is_s_computed',array(0,1))->nullable()->default(0);
            $table->string('s_computed_status')->nullable()->default('Not Computed');
            $table->string('s_computed_at')->nullable();
            $table->foreignId('s_computed_by')->nullable()->constrained('users','id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('computed_results', function (Blueprint $table) {
            $table->dropColumn('is_s_computed');
            $table->dropColumn('s_computed_status');
            $table->dropColumn('s_computed_at');
            $table->dropConstrainedForeignId('s_computed_by');
        });
    }
}
