<?php

namespace Database\Seeders;


use App\Models\FeeType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = ['Specific','General','Open', 'System' ];


        foreach ($items as $v ) {

        $paymentItem = FeeType::updateOrCreate( ['name' => $v], [
            'name' => $v,
            'description' => Str::slug(strtolower($v))
        ]);

        }
    }






}
