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
        $emails = ['julius@gmail.com', 'guytox@gmail.com', 'vincentachanya@gmail.com'];
        $phone_number = 1234567;



        foreach ($emails as $email)
        {
            $user = User::updateOrCreate(['email' => $email], [
                'name' => $faker->firstName() . ' ' . $faker->lastName(),
                'email' => $email,
                'password' => Hash::make('123456'),
                'phone_number' => $phone_number,
                'email_verified_at' => now()
            ]);

            $user->assignRole('admin');
            $user->assignRole('staff');
            $phone_number++;

        }
    }
}
