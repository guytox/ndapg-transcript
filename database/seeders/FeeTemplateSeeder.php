<?php

namespace Database\Seeders;

use App\Models\FeeTemplate;
use Illuminate\Database\Seeder;

class FeeTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purposes  = ['Initial System Billing', 'System Late Registration Billing'];
        foreach ($purposes as $purpose ) {
            $record = FeeTemplate::firstOrCreate(['narration' => $purpose], [
                'narration' => $purpose,
                'fee_type_id' => getFeeTypeIdByTypeName('System'),
                'total_amount' => 0,

            ]);


        }
    }
}
