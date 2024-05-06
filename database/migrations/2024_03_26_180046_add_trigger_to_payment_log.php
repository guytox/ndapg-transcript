<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTriggerToPaymentLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_logs', function (Blueprint $table) {

            DB::unprepared('CREATE TRIGGER insert_log AFTER INSERT ON `payment_logs`
            FOR EACH ROW
                BEGIN

                    UPDATE `fee_payments`  SET `amount_paid` = (select sum(`amount_paid`) from payment_logs s  where s.fee_payment_id= NEW.fee_payment_id) WHERE `fee_payments`.`id` = NEW.fee_payment_id ;

                    UPDATE `fee_payments`  SET `balance` = (`amount_billed` - (select sum(`amount_paid`) from payment_logs s  where s.fee_payment_id= NEW.fee_payment_id)) WHERE `fee_payments`.`id` = NEW.fee_payment_id ;


                END');


        DB::unprepared('CREATE TRIGGER update_log AFTER UPDATE ON `payment_logs`
            FOR EACH ROW
                BEGIN

                UPDATE `fee_payments`  SET `amount_paid` = (select sum(`amount_paid`) from payment_logs s  where s.fee_payment_id= NEW.fee_payment_id) WHERE `fee_payments`.`id` = NEW.fee_payment_id ;

                UPDATE `fee_payments`  SET `balance` = (`amount_billed` - (select sum(`amount_paid`) from payment_logs s  where s.fee_payment_id= NEW.fee_payment_id)) WHERE `fee_payments`.`id` = NEW.fee_payment_id ;

                END');


        DB::unprepared('CREATE TRIGGER delete_log AFTER DELETE ON `payment_logs`
            FOR EACH ROW
                BEGIN
                    UPDATE `fee_payments`  SET `amount_paid` = (select sum(`amount_paid`) from payment_logs s  where s.fee_payment_id= OLD.fee_payment_id) WHERE `fee_payments`.`id` = OLD.fee_payment_id ;

                    UPDATE `fee_payments`  SET `balance` = (`amount_billed` - (select sum(`amount_paid`) from payment_logs s  where s.fee_payment_id= OLD.fee_payment_id)) WHERE `fee_payments`.`id` = OLD.fee_payment_id ;
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
        Schema::table('payment_logs', function (Blueprint $table) {
            //
        });
    }
}
