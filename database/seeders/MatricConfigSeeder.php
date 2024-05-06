<?php

namespace Database\Seeders;

use App\Models\AdmissionCount;
use App\Models\MatricConfiguration;
use App\Models\SystemVariable;
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
            'session_id' => activeSession()->id,
            'application_number' => 'APPG20220000',
            'student_number' => 'PG20220000',
            'session_id' => '1',
        ]);

        AdmissionCount::updateOrCreate([
            'category' => 'transcript',
        ],[
            'category' => 'transcript',
            'prefix' => 'NDA/TR/'
        ]);

        SystemVariable::updateOrCreate([
            'name' => 'applications',
        ],[
            'name' => 'applications',
            'value' => 'On'
        ]);
    }
}
