<?php

namespace Database\Seeders;

use App\Models\ErrorCode;
use Illuminate\Database\Seeder;

class ErrorCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $errorCodes = [
            ['4001','User trying to Initiate a course allocation in a department not his'],
            ['40012','You Do not have Permission to view this resource']

        ];


        foreach ($errorCodes as $code) {
            ErrorCode::firstOrCreate(['code' => $code[0]], [
                'code' => $code[0],
                'description' => $code[1],

            ]);
        }
    }
}
