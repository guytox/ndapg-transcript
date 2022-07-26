<?php

namespace Database\Seeders;

use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator;
use Illuminate\Support\Facades\Hash;

class StudentMassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $a=1;
        do {
            $faker = app(Generator::class);
            $email = $faker->email();
            $user = User::updateOrCreate(['email' => $email], [
                'name' => $faker->firstName() . ' ' . $faker->lastName(),
                'email' => $email,
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'username' => generateMatriculationNumber(),
                'phone_number'=> $faker->phoneNumber(),


            ]);
            updateMatriculationNumber($user->username);
            $user->assignRole('student');

            // insert student record

            $student = StudentRecord::updateOrCreate(['user_id'=>$user->id, 'matric'=>$user->username],[

                'user_id'=>$user->id,
                'matric'=>$user->username,
                'program_id'=> random_int(1,25),
                'state_origin' => random_int(1,38),
                'admission_session'=> activeSession()->id, 

            ]);
            //update user with current level

            $user->current_level = getProgrammeDetailById($student->program_id, 'level');
            $user->save();


            $a++;
        } while ($a <= 1000);
    }
}
