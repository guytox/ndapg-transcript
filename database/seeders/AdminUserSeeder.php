<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(Generator::class);
        $emails = ['julius@gmail.com', 'guytox@gmail.com', 'iortimdan@umm.edu.ng', 'successfullprince@gmail.com', 'pioryina@umm.edu.ng', 'vicechancellor@umm.edu.ng'];

        foreach ($emails as $email)
        {
            $user = User::updateOrCreate(['email' => $email], [
                'name' => $faker->firstName() . ' ' . $faker->lastName(),
                'email' => $email,
                'password' => Hash::make('123456'),
                'email_verified_at' => now()
            ]);

            $user->assignRole('staff');

            if ($email == 'pioryina@umm.edu.ng') {

                $user = User::updateOrCreate(['email' => $email], [
                    'name' => 'Mr. Amo Philip Ioryina',
                    'email' => $email,
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now()
                ]);

                $user->assignRole('bursar');
                $user->assignRole('staff');

            }elseif ($email == 'vicechancellor@umm.edu.ng') {

                $user = User::updateOrCreate(['email' => $email], [
                    'name' => 'Vice Chancellor',
                    'email' => $email,
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now()
                ]);

                $user->assignRole('vc');
                $user->assignRole('staff');

            }else{

                $user = User::updateOrCreate(['email' => $email], [
                    'name' => $faker->firstName() . ' ' . $faker->lastName(),
                    'email' => $email,
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now()
                ]);

                $user->assignRole('admin');
                $user->assignRole('staff');

            }



        }
    }
}
