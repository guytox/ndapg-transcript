<?php

namespace Database\Seeders;

use App\Models\NdaService;
use Illuminate\Database\Seeder;

class NdaServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NdaService::updateOrCreate([
            'service_name' => 'AIR FORCE',
        ],[
            'service_name' => 'AIR FORCE',
        ]);

        NdaService::updateOrCreate([
            'service_name' => 'ARMY',
        ],[
            'service_name' => 'ARMY',
        ]);

        NdaService::updateOrCreate([
            'service_name' => 'NAVY',
        ],[
            'service_name' => 'NAVY',
        ]);
    }
}
