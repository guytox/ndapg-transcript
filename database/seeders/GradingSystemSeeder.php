<?php

namespace Database\Seeders;

use App\Models\GradingSystem;
use App\Models\GradingSystemItems;
use Illuminate\Database\Seeder;

class GradingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'id' => 1,
            'uid' => uniqid('gs_'),
            'name' => 'Four Point Grading System',
            'description' => 'Latest Approved NUC Grading System',
        ];

        $newGradingSystem = GradingSystem::upsert([$data], $uniqueBy='id', $update=[
            'description',

        ]);

        $items = [
            [7000,10000,'A',1,4],
            [6000,6999,'B',1,3],
            [5000,5999,'C',1,2],
            [4500,4999,'D',1,1],
            [0,4499,'F',0,0],
        ];

        foreach ($items as $item) {
            $gradData = [
                'grading_system_id' => 1,
                'lower_boundary' => $item[0],
                'upper_boundary' => $item[1],
                'grade_letter' => $item[2],
                'credit_earned' => $item[3],
                'weight_points' => $item[4],
            ];

            $gradItem = GradingSystemItems::upsert($gradData, $uniqueBy=['grade_letter'], $update =[
                'lower_boundary',
                'upper_boundary',
                'grade_letter',
                'credit_earned',
                'weight_points'
            ]);
        }



    }
}
