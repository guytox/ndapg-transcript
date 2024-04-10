<?php

namespace Database\Seeders;

use App\Models\TranscriptDeliveryMode;
use Illuminate\Database\Seeder;

class DeliveryModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TranscriptDeliveryMode::updateOrCreate([
            'mode' => "Physical"
        ],[
            'mode' => 'Physical'
        ]);

        TranscriptDeliveryMode::updateOrCreate([
            'mode' => "E-mail"
        ],[
            'mode' => 'E-mail'
        ]);
    }
}
