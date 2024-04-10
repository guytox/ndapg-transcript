<?php

namespace Database\Seeders;

use App\Models\TranscriptType;
use Illuminate\Database\Seeder;

class TranscriptTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TranscriptType::updateOrCreate([
            'type_name' => 'Regular Course',
        ],[
            'type_name' => 'Regular Course',
        ]);

        TranscriptType::updateOrCreate([
            'type_name' => 'Postgraduate',
        ],[
            'type_name' => 'Postgraduate',
        ]);
    }
}
