<?php

namespace App\Http\Controllers;

use App\Jobs\FireApproveStudentMigrationJob;
use App\Jobs\FireStudentMigrationJob;
use App\Jobs\RecommendStudentMigrationJob;
use App\Models\User;
use Illuminate\Http\Request;

class StudentMigrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = User::role('student')->get();
        return count($students);
        return "welcome to student migration";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $time = now();

        FireStudentMigrationJob::dispatch(user()->id, $time);

        return redirect(route('home'))->with('success', "Student Recommended for Migration Successfully");

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

    public function approveMigration(){
        $time = now();

        FireApproveStudentMigrationJob::dispatch(user()->id, $time);

        return redirect(route('home'))->with('success', "Student Migration Approval Submitted Successfully Successfully");
    }

    public function effectMigration(Request $request){
        return $request;
    }

    public function clearApplicantRecords(){
        return getApplicationSession();
        return $unadmitted = User::Role('hod')->get();
    }


}
