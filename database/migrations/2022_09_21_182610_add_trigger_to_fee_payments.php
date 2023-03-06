<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTriggerToFeePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_payments', function (Blueprint $table) {

            DB::unprepared('CREATE TRIGGER update_status AFTER UPDATE ON `fee_payments`

            FOR EACH ROW
                BEGIN

                IF(OLD.`payment_status` != NEW.`payment_status`)

                THEN

                UPDATE `fee_payment_items`  SET `fee_payment_items`.`status` = NEW.payment_status, `fee_payment_items`.`fee_payment_id` = NEW.id
                WHERE

                `fee_payment_items`.`fee_payment_id` = OLD.id ;

                END IF;

                END');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fee_payments', function (Blueprint $table) {

            DB::unprepared('DROP TRIGGER `update_status`');
        });
    }
}
