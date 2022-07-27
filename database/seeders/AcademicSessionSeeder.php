<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use Illuminate\Database\Seeder;

class AcademicSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AcademicSession::firstOrCreate(['name' => '2022/2023'], [
            'name' => '2021/2022',
            'uid' => uniqid('as_'),
            'currentSemester' =>  config('app.semesters.2'),
            'description' => 'Academic session for 2022/2023',
            'status' => true
        ]);
    }
}
