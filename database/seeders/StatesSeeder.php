<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $states = [
            ['1', 'Abia'],
            ['2', 'Adamawa'],
            ['3', 'Akwa Ibom'],
            ['4', 'Anambra'],
            ['5', 'Bauchi'],
            ['6', 'Bayelsa'],
            ['7', 'Benue'],
            ['8', 'Borno'],
            ['9', 'Cross River'],
            ['10', 'Delta'],
            ['11', 'Ebonyi'],
            ['12', 'Edo'],
            ['13', 'Ekiti'],
            ['14', 'Enugu'],
            ['15', 'FCT'],
            ['16', 'Gombe'],
            ['17', 'Imo'],
            ['18', 'Jigawa'],
            ['19', 'Kaduna'],
            ['20', 'Kano'],
            ['21', 'Katsina'],
            ['22', 'Kebbi'],
            ['23', 'Kogi'],
            ['24', 'Kwara'],
            ['25', 'Lagos'],
            ['26', 'Nasarawa'],
            ['27', 'Niger'],
            ['28', 'Ogun'],
            ['29', 'Ondo'],
            ['30', 'Osun'],
            ['31', 'Oyo'],
            ['32', 'Plateau'],
            ['33', 'Rivers'],
            ['34', 'Sokoto'],
            ['35', 'Taraba'],
            ['36', 'Yobe'],
            ['37', 'Zamfara'],
            ['38', 'Not Nigerian State']
        ];

        foreach ($states as $v ) {
            State::updateOrCreate(['name'=>$v[1]],[
                'id' => $v[0],
                'name' => $v[1]

            ]

            );
        }
    }
}
