<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicSession;

class SchoolSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AcademicSession::firstOrCreate(['name' => '2021/2022'], [
            'name' => '2021/2022',
            'uid' => uniqid('as_'),
            'currentSemester' =>  config('app.semesters.2'),
            'description' => 'Academic session for 2021/2022',
            'status' => true
        ]);
    }
}
