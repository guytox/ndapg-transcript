<?php

namespace App\Http\Controllers;

use App\Imports\AdmissionListImport;
use App\Imports\StudentPaymentUploadImport;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Traits\HasRoles;

class StudentInformationController extends Controller
{
    use HasRoles;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user()->hasRole('admin')) {

            $studentList = User::role('student')->select('id','name','email','username', 'phone_number','current_level')->get();

            $programlist = Program::all()->pluck('name','id');

            return view('admin.view-student-list',compact('studentList', 'programlist'));


        }else{

            return redirect(route('home'))->with('error',"You do not have rights to view this page, contact ICT");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    public function uploadStudentList(Request $request){

        if (user()->hasRole('admin')) {

            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:2048',
                'program_id' =>'required'
            ]);

            $studentList = $request->file('file');

            Excel::import(new AdmissionListImport($request->program_id), $studentList);

            return back()->with('success', "Congratulations!!! Student list uploaded successfully !!! .");

        }else{
            return back()->with('info', "This action is for administrator's Only, Contact ICT");
        }

        return back()->with('error',"Nothing found");


    }

    public function uploadStudentsForm(){

        if (user()->hasRole('admin') or user()->hasRole('pay_processor')) {

            $programlist = Program::all()->pluck('name','id');

            return view('admin.configs.import-students',compact('programlist'));

        }
    }


    public function uploadStudentAdmissionList(Request $request){

        if (user()->hasRole('admin')) {

            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:2048',
                'program_id' =>'required'
            ]);

            $studentList = $request->file('file');

            Excel::import(new AdmissionListImport($request->program_id), $studentList);

            return back()->with('success', "Congratulations!!! Student list uploaded successfully !!! .");

        }else{
            return back()->with('info', "This action is for administrator's Only, Contact ICT");
        }

        return back()->with('error',"Nothing found");


    }

    public function uploadStudentsAdmissionForm(){

        if (user()->hasRole('admin') or user()->hasRole('pay_processor')) {

            $programlist = Program::all()->pluck('name','id');

            return view('admin.configs.import-students',compact('programlist'));

        }
    }

    public function uploadStudentPayments(Request $request){

        //return $request;

        if (user()->hasRole('admin') or user()->hasRole('pay_processor')) {

            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:2048',
                'semester' =>'required'
            ]);

            $studentList = $request->file('file');

            Excel::import(new StudentPaymentUploadImport($request->semester, user()->id), $studentList);

            return back()->with('success', "Congratulations!!! Payments uploaded successfully !!! .");

        }else{
            return back()->with('info', "This action is for administrator's Only, Contact ICT");
        }

        return back()->with('error',"Nothing found");
    }
}
