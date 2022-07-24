<?php

namespace Database\Seeders;

use App\Models\StudyLevel;
use Illuminate\Database\Seeder;

class StudyLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studyLevels = ['700', '800', '900'];

        foreach ($studyLevels as $studyLevel) {

            StudyLevel::firstOrCreate(['level' => $studyLevel], ['level' => $studyLevel, 'uid' => uniqid('sl_')]);
        }
    }

}
