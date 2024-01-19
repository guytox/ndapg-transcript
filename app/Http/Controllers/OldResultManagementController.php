<?php

namespace App\Http\Controllers;

use App\Imports\OldResultImport;
use App\Models\AcademicSession;
use App\Models\Semester;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OldResultManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user()->hasRole('admin') ) {

            $sessionList = AcademicSession::all()->pluck('name','id');
            $semesterList = Semester::all()->pluck('name','id');

            return view('admin.configs.import-old-student-results',compact('sessionList', 'semesterList'));

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
        if (user()->hasRole('admin') ) {

            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:3048',
                'session_id' =>'required',
                'semester_id' => 'required',
            ]);

            $grades = $request->file('file');

            Excel::import(new OldResultImport($request->session_id, $request->semester_id, user()->id, now()), $grades);



            return redirect(route('oldResultUpload.index'))->with('info', 'Old Result Upload Successful, Please check!!!');
        }else {
            return back()->with('error', "This action is for Admins only contact Support");
        }
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



}
