<?php

namespace App\Http\Controllers;

use App\Models\RegMonitor;
use App\Models\StudentRecord;
use Illuminate\Http\Request;

class StudentDeffermentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        # we return a view requesting for matric number
        return view('admin.select-student-for-defferment');
    }

    public function viewStudentDetails(Request $request){

        if (user()->hasRole('admin|dean_pg|dap')) {

        }else{
            return back()->with('error', "Error!!!, You do not have the right priviledges to perform this action");
        }

        $this->validate($request, [

            'd_std'=>'required',
            'c_sess'=>'required',
            'r_sess'=>'required',
            'd_amount'=>'required',

        ]);

        $std = StudentRecord::where('matric', $request->d_std)->first();

        #get the regMonitors for this session
        $thisSessionReg = RegMonitor::join('curricula as c', 'c.id','=','reg_monitors.curricula_id')
                                    ->where('reg_monitors.student_id', $std->id)
                                    ->where('reg_monitors.session_id', '>=', $request->c_sess)
                                    ->select('reg_monitors.*','c.title')
                                    ->get();
        $title = "Preview Student Information before Semester Defferment deferment";
        $beginSess = $request->c_sess;
        $returnSess = $request->r_sess;
        $amt = $request->d_amount;

        return view('admin.previewStudentBeforeDefferment',compact('std','thisSessionReg','title', 'beginSess','returnSess', 'amt'));
        return $request;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $request;
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
