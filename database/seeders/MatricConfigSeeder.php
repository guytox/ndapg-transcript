<?php

namespace Database\Seeders;

use App\Models\MatricConfiguration;
use Illuminate\Database\Seeder;

class MatricConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MatricConfiguration::updateOrCreate([
            'session_id' => activeSession()->id
        ], [
            'application_number' => 'APPG20220000',
            'student_number' => 'PG20220000',
            'session_id' => '1',
            'created_at' => '2019-09-03 00:00:00',
            'updated_at' => '2019-09-03 00:00:00',
        ]);
    }
}
