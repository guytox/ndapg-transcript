<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['applicant', 'admin', 'student', 'dean', 'lecturer', 'hod', 'exam_officer','reg_officer','bursar','vc','audit'];

        foreach ($roles as $role) {
            // create role when the seeder is called with 3 basic roles.
            Role::findOrCreate($role);
        }
    }
}
