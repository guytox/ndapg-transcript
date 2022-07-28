<?php

namespace App\Http\Controllers;

use App\Models\GradingSystem;
use App\Models\GradingSystemItems;
use App\Models\Program;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;

class GradingSystemController extends Controller
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
            $gradingSystems = GradingSystem::with('gradingSystemItems')->get();

            return view('admin.configs.viewGradingSystemAll', compact('gradingSystems'));
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
        if (user()->hasRole('admin')) {

            $this->validate($request, [
                '_token'=>'required',
                'uid'=>'required',
                'name'=>'required',
                'description'=>'required',
            ]);

            GradingSystem::upsert($request->except('_token'), $uniqueBy ='name', $update=[
                'description'
            ]);

            return back()->with('success', "Record added successfully!!!");

        }

    return "<br> You do not have the required permission to visit this page";


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GradingSystem  $gradingSystem
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (user()->hasRole('admin')) {

            $gradingSystemWithItems = GradingSystem::with('gradingSystemItems')->find($id);



            return view('admin.configs.viewGradingSystemSingle', compact('gradingSystemWithItems'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GradingSystem  $gradingSystem
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
     * @param  \App\Models\GradingSystem  $gradingSystem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (user()->hasRole('admin')) {
            $gradingSystem = GradingSystem::find($id);

            if ($gradingSystem) {
                $gradingSystem->description = $request->description;
                $gradingSystem->status = $request->status;
                $gradingSystem->save();

                return back()->with('success', "Record Updated Successfully!!!");
            }else{
                return back()->with('error', "Error!!! Grading System Not Found, Try again later or contact ICT");
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GradingSystem  $gradingSystem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return back()->with('error', "This action is disabled by admin, Contact ICT");
    }



    public function addGradingItem(Request $request){
        if (user()->hasRole('admin')) {

            $this->validate($request, [
                'grading_system_id' => 'required',
                'lower_boundary' => 'required',
                'upper_boundary' => 'required',
                'grade_letter' => 'required',
                'weight_points' => 'required',
                'credit_earned' => 'required'
            ]);

            $newItem = GradingSystemItems::upsert($request->except('_token'), $uniqueBy = ['grade_letter','grading_system_id'], $update =[
                'lower_boundary',
                'upper_boundary',
                'weight_points',
                'credit_earned',
                'grade_letter'
            ]);

            return back()->with('success', "Item added Successfully!!!");
        }else {
            return back()->with('error', "Error!!! You do not have permission to view this page, Contact ICT");
        }
    }



    public function deleteGradingItem(Request $request){
        if (user()->hasRole('admin')) {
            $toDelete = GradingSystemItems::find($request->id);
            $toDelete->delete();

            return back()->with('success', "Record deleted successfully!!!");
        }
    }

    public function editGradingItem(Request $request){
        if (user()->hasRole('admin')) {

            $this->validate($request, [
                'grading_system_id' => 'required',
                'lower_boundary' => 'required',
                'upper_boundary' => 'required',
                'grade_letter' => 'required',
                'weight_points' => 'required',
                'credit_earned' => 'required',
                'id' => 'required'
            ]);

            $newItem = GradingSystemItems::upsert($request->except('_token'), $uniqueBy = ['id'], $update =[
                'lower_boundary',
                'upper_boundary',
                'weight_points',
                'credit_earned',
                'grade_letter'
            ]);

            return back()->with('success', "Item updated Successfully!!!");
        }else {
            return back()->with('error', "Error!!! You do not have permission to view this page, Contact ICT");
        }

    }




}
