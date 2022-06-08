<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
            ['1', 'Abia State'],
            ['2', 'Adamawa State'],
            ['3', 'Akwa Ibom State'],
            ['4', 'Anambra State'],
            ['5', 'Bauchi State'],
            ['6', 'Bayelsa State'],
            ['7', 'Benue State'],
            ['8', 'Borno State'],
            ['9', 'Cross River State'],
            ['10', 'Delta State'],
            ['11', 'Ebonyi State'],
            ['12', 'Edo State'],
            ['13', 'Ekiti State'],
            ['14', 'Enugu State'],
            ['15', 'FCT'],
            ['16', 'Gombe State'],
            ['17', 'Imo State'],
            ['18', 'Jigawa State'],
            ['19', 'Kaduna State'],
            ['20', 'Kano State'],
            ['21', 'Katsina State'],
            ['22', 'Kebbi State'],
            ['23', 'Kogi State'],
            ['24', 'Kwara State'],
            ['25', 'Lagos State'],
            ['26', 'Nasarawa State'],
            ['27', 'Niger State'],
            ['28', 'Ogun State'],
            ['29', 'Ondo State'],
            ['30', 'Osun State'],
            ['31', 'Oyo State'],
            ['32', 'Plateau State'],
            ['33', 'Rivers State'],
            ['34', 'Sokoto State'],
            ['35', 'Taraba State'],
            ['36', 'Yobe State'],
            ['37', 'Zamfara State'],
            ['38', 'Not Nigerian State']
        ];

        foreach ($states as $state) {
            State::updateOrCreate(
                ['name' => $state[1]],
                [
                    'id' => $state[0],
                    'name' => $state[1]
                ]
            );
        }
    }
}
