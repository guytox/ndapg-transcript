<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;

class LandingSwitchController extends Controller
{
    public function landingpage(){
        switch (isApplicationOpen()->value) {
            case 'Off':
                 return redirect(route('login'));
                break;
            case 'On':
                 return view('apphome');
                break;

            default:
                # code...
                break;
        }
        return isApplicationOpen();


    }


    public function getProfessionalProgrammes(){
        $faculties = Faculty::join('departments as d', 'd.faculty_id','=','faculties.id')
                            ->join('programs as p','p.id','=','d.id')
                            ->where('p.category','professional')
                            ->where('faculties.academic', 1)
                            ->distinct('faculties.id')
                            ->select('faculties.*')
                            ->orderBy('faculties.name','asc')
                            ->get();

        if ($faculties ) {
            return view('admissions.availableProfessionalProgrammes',compact('faculties'));
        }
    }


    public function getAcademicProgrammes(){
        $faculties = Faculty::join('departments as d', 'd.faculty_id','=','faculties.id')
                            ->join('programs as p','p.id','=','d.id')
                            ->where('p.category','academic')
                            ->where('faculties.academic', 1)
                            ->distinct('faculties.id')
                            ->select('faculties.*')
                            ->get();

        if ($faculties ) {
            return view('admissions.availableAcademicProgrammes',compact('faculties'));
        }
    }


}
