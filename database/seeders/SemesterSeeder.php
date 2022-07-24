<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $semesters = ['first', 'second'];

        foreach ($semesters as $semester ) {
            Semester::firstOrCreate(['name'=>$semester],['name'=>$semester]);
        }
    }
}
