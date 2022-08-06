<?php

namespace App\Http\Controllers;

use App\Imports\AdmissionOfferImport;
use App\Models\Admission;
use App\Models\Program;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Traits\HasRoles;

class AdmissionController extends Controller
{
    use HasRoles;

    public function uploadStudentAdmissionList(Request $request){
        if (user()->hasRole('admin')) {
            //return $request;

            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:3048',
                'programme' => 'required'

            ]);

            $admissionList = $request->file('file');

            Excel::import(new AdmissionOfferImport($request->programme), $admissionList);

            return redirect(route('student.admissionoffer.form'))->with('success', "Admission List Uploaded Successfully!!!");
        } else{

            return back()->with('error', 'You do not have the privileges to perform this action, contact ICT');

        }
    }

    public function uploadStudentsAdmissionForm(){
        if (user()->hasRole('admin')) {

            $admitted = Admission::where('session_id', activeSession()->id)->get();

            $programmes = Program::all()->pluck('name','id');


            return view('admin.configs.import-admission-list', compact('admitted', 'programmes'));
        }
    }
}
