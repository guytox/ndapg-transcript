<?php

namespace App\Imports;

use App\Models\Admission;
use Maatwebsite\Excel\Concerns\ToModel;

class AdmissionOfferImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Admission([
            //
        ]);
    }
}
