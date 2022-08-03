<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StaffImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = [
            'name'=> $row['name'],
            'username'=> $row['staffid'],
            'password' => Hash::make($row['staffid']),
        ];

        $newUser = User::updateOrCreate(['username' => $row['staffid']], $data);

        //
        $newUser->assignRole('staff');
        $newUser->assignRole('lecturer');

        //get staff department

        $profiledata = [
            'department_id' => getDepartmentByName($row['department'])->id,
            'user_id' => $newUser->id
        ];

        //create a UserProfile Model for the user
        $userProfile = UserProfile::updateOrCreate($profiledata ,$profiledata);

        return $newUser;
    }

}
