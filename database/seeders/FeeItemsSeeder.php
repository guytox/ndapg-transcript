<?php

namespace Database\Seeders;

use App\Models\FeeItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeeItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [ 'Transcript Application Fees', 'Transcript Express Fees', 'Statement Of Result Fee' ];


        foreach ($items as $v ) {

        $paymentItem = FeeItem::updateOrCreate( ['name' => $v], [
            'name' => $v,
            'description' => Str::slug(strtolower($v))
        ]);

        }
    }






}
