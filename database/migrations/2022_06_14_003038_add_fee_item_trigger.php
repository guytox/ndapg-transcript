<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Node\Inline\DelimitedInterface;

class AddFeeItemTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::unprepared('CREATE TRIGGER insert_Total_Amount AFTER INSERT ON `fee_template_items`
            FOR EACH ROW
                BEGIN
                   UPDATE `fee_templates`  SET `total_amount` = (select SUM(`item_amount`) from fee_template_items f where f.fee_template_id= NEW.fee_template_id)
                   WHERE
                   `fee_templates`.`id` = NEW.fee_template_id ;
                END');


        DB::unprepared('CREATE TRIGGER update_Total_Amount AFTER UPDATE ON `fee_template_items`
            FOR EACH ROW
                BEGIN
                UPDATE `fee_templates`  SET `total_amount` = (select SUM(`item_amount`) from fee_template_items f where f.fee_template_id= NEW.fee_template_id)
                WHERE
                `fee_templates`.`id` = NEW.fee_template_id ;
                END');

        DB::unprepared('CREATE TRIGGER delete_Total_Amount AFTER DELETE ON `fee_template_items`
            FOR EACH ROW
                BEGIN
                UPDATE `fee_templates`  SET `total_amount` = (select SUM(`item_amount`) from fee_template_items f where f.fee_template_id= OLD.fee_template_id)
                WHERE
                `fee_templates`.`id` = OLD.fee_template_id ;
                END');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `insert_Total_Amount`');
        DB::unprepared('DROP TRIGGER `update_Total_Amount`');
        DB::unprepared('DROP TRIGGER `delete_Total_Amount`');

    }
}
