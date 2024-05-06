<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class LandingSwitchController extends Controller
{
    public function landingpage(){
        switch (isApplicationOpen()->value) {
            case 'Off':
                 return redirect(route('login'));
                break;
            case 'On':
                 return view('index');
                break;

            default:
                # code...
                break;
        }
        return isApplicationOpen();


    }


    public function getProfessionalProgrammes(){
         $faculties = Faculty::join('departments as d', 'd.faculty_id','=','faculties.id')
                            ->join('programs as p','p.department_id','=','d.id')
                            ->where('p.category','professional')
                            ->distinct('faculties.id')
                            ->select('faculties.*')
                            ->orderBy('faculties.name','asc')
                            ->get();

            $depts = Department::join('programs as p','p.department_id','=','departments.id')
                                ->where('p.category','professional')
                                ->where('p.is_advertised','1')
                                ->distinct('departments.id')
                                ->select('departments.id')
                                ->orderBy('departments.id','asc')
                                ->get();


        if ($faculties ) {
            return view('admissions.availableProfessionalProgrammes',compact('faculties', 'depts'));
        }
    }


    public function getAcademicProgrammes(){
        $faculties = Faculty::join('departments as d', 'd.faculty_id','=','faculties.id')
                            ->join('programs as p','p.department_id','=','d.id')
                            ->where('p.category','academic')
                            ->where('faculties.academic', 1)
                            ->distinct('faculties.id')
                            ->select('faculties.*')
                            ->orderBy('faculties.name','asc')
                            ->get();

        $depts = Department::join('programs as p','p.department_id','=','departments.id')
                                ->where('p.category','academic')
                                ->where('p.is_advertised','1')
                                ->distinct('departments.id')
                                ->select('departments.id')
                                ->orderBy('departments.id','asc')
                                ->get();

        if ($faculties ) {
            return view('admissions.availableAcademicProgrammes',compact('faculties', 'depts'));
        }
    }


}
