<?php

namespace App\Http\Traits;

use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\FeeCategory;
use App\Models\FeePayment;
use App\Models\PaymentConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait UserTraits
{
    public function getUserNameById($id)
    {
        // Return a users full name

        $getUser = User::where('id',$id)->first();

        return $getUser->name ?? '';
    }

    public function getStudentMatricById($id)
    {
        // Return a users full name
        $getUser = User::where('id',$id)->first();

        return $getUser->username;
    }

    public function getStudentPhoneNumberById($id)
    {
        // Return a users full name
        $getUser = User::where('id',$id)->first();

        return $getUser->phone_number;
    }


    public function getStudentsProgrammeNameById($id)
    {
        // Return a users full name
        $getUser = User::with('programme')->find($id);

        return $getUser->programme->name;
    }

    public function getStudentDepartmentNameById($id)
    {
        // Return a users full name
        $getUser = Department::where('id',$id)->first();

        return $getUser->name;
    }

    public function getStudentFacultyNameById($id)
    {
        // Return a users full name
        $getUser = Department::where('id',$id)->first();

        return $getUser->name;
    }

    public function getStudentCurrentLevelById($id)
    {
        // Return a users full name
        $getUser = User::where('id',$id)->first();

        return $getUser->current_level;
    }

    public function getStudentIdByEmail($email)
    {
        // Return a users full name

        $getData = User::where('email',$email)->first();

        if ($getData!='') {

            return $getData->id;

        }else{

            return false;
        }

    }

    public function getSchoolSessionNameById($id)
    {
        // Return a users full name
        $getData = AcademicSession::where('id',$id)->first();
        return $getData->name;
    }

    public function getSchoolSessionsForDropDown(){
        $getData = AcademicSession::select('id', 'name')->get();

        return $getData;
    }

    public function getPaymentDescriptionById($id)
    {
        // Return a users full name

        $getData = FeeCategory::where('id',$id)->first();

        return $getData->description;
    }


    public function getStudentBalancesById($id)
    {
        // Returns a students Total billing, and balances

        $getData = FeePayment::where('user_id',$id)
                                ->select (
                                    DB::raw("SUM(amount_billed) as totalBilled"),
                                    DB::raw("SUM(discount_scholarship ) as totalScholarshipDiscounts"),
                                    DB::raw("SUM(discount_nkst ) as totalNkstDiscounts"),
                                    DB::raw("SUM(discount_amount) as totalDiscount"),
                                    DB::raw("SUM(discount_other) as totalOtherDiscounts"),
                                    DB::raw("SUM(amount_paid) as totalPayments"),
                                    DB::raw("SUM(balance) as totalBalance"),
                                    DB::raw("SUM(excess_fee) as totalExcessPayment"),
                                )
                                ->get();

        //dd($getData->toArray());

        return $getData;
    }


    public function getStudentProgrammeNameById($id){
        $userProgramme = User::join('programs as p', 'p.id', '=', 'users.program_id')
                            ->where('users.id',$id)
                            ->select('p.*')
                            ->first();

        return $userProgramme->name;
    }


}
