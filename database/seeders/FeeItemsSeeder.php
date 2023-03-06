<?php

namespace Database\Seeders;

use App\Models\FeeItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeeItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = ['Late Registration'.'Tuition','Library','Examination','Medical Expenses',  'Hall Maintenance', 'ICT Training', 'Development Levy', 'Entrepreneurship Training', 'Sports Levy', 'Departmental Registration Fee', 'Campus Wide Internet Service', 'Hospital Development Levy', 'Faculty Registration', 'Security Fees', 'Utility Charges', 'Laboratory Fees','Departmental Handbook', 'Donations', 'Electricity Charges', 'Entrepreneurship Development Center', 'Field Trips', 'Hire of Academic Gowns', 'Identity (ID) Card', 'Transcript Fees', 'Statement Of Result', 'Certificate Fees', 'Postgraduate Acceptance Fees', 'Postgraduate Screening Fees', 'Leadership Training', 'Deferment Fees', 'Postgraduate Application Fees' ];


        foreach ($items as $v ) {

        $paymentItem = FeeItem::updateOrCreate( ['name' => $v], [
            'name' => $v,
            'description' => Str::slug(strtolower($v))
        ]);

        }
    }






}
