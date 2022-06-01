<?php

namespace Database\Seeders;

use App\Models\BillingItem;
use Illuminate\Database\Seeder;

class BillingItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $billitems = [
            ['001', 'SPGS Application Form'],
            ['002', 'Acceptance Fees'],
            ['003', 'Departmental Charges'],
            ['004', 'Examination Fee'],
            ['005', 'Faculty Charges'],
            ['006', 'Sport/Games'],
            ['007', 'ICT/Portal Charges'],
            ['008', 'ID Card'],
            ['009', 'Laboratory/Workshop/Studio/Practical Charges'],
            ['010', 'Computer Services'],
            ['011', 'Library'],
            ['012', 'Matriculation Fee'],
            ['013', 'Medical Fee'],
            ['014', 'Orientation/Students Handbook'],
            ['015', 'Sanitation & Utilities'],
            ['016', 'Statement of Result'],
            ['017', 'Tuition Fee'],
            ['018', 'Development Levy'],
            ['019', 'Hostel Accomodation (Bed Space)'],
            ['020', 'Certificate Verfication'],
            ['021', 'SPGS Late Registration Fee'],
            ['022', 'Supplementary Admission Charge'],
            ['023', 'Entrance Examination Charge'],
            ['024', 'Change of Course'],
            ['025', 'SPGS Charges'],
            ['026', 'Bank Charges'],
            ['027', 'Progress Report'],
            ['028', 'Teaching Practice'],
            ['029', 'Half School Fees Payment Surcharge'],
            ['030', 'Hostel Maintenance'],
            ['031', 'Late Application Fee'],
            ['032', 'Re-sit Examination Fee'],
            ['033', 'SPGS Services Charge'],
            ['034', 'Seminar Fee'],
            ['035', 'Maintenance Charges'],
            ['036', 'Suspension of Studies Form'],
            ['037', 'Cost of Publication'],
            ['038', 'Transcript'],
            ['039', 'Resumption from Suspension of Studies']
        ];

        foreach ($billitems as $key=> $b) {
            BillingItem::updateOrCreate(['title' => [1]],[
                'code' => $b['0'],
                'title' => $b[1]
            ]);
        }
    }
}
