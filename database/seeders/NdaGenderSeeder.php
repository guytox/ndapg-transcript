<?php

namespace Database\Seeders;

use App\Models\Gender;
use Illuminate\Database\Seeder;

class NdaGenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Gender::updateOrCreate([
            'gender_name' => 'Female',
        ],[
            'gender_name' => 'Female',
        ]);

        Gender::updateOrCreate([
            'gender_name' => 'Male',
        ],[
            'gender_name' => 'Male',
        ]);
    }
}
