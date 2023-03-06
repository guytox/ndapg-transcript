<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get a list of all Scholarships ready to return to the page, fetch everything

                //$faculties = Faculty::all()->pluck('facultyDescription', 'facultyName', 'facultyDescription','id');
                if (user()->hasRole('admin')) {
                    $scholarships = Scholarship::all();

                    return view('admin.configs.viewScholarshipTypes', compact('scholarships'));
                }

                abort(403, "You do not have permission to view this page");
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
        if ((user()->hasRole('admin'))) {

            //Enter you code here
            //first valdate the code, the enter the details into the form, lets go
            $this->validate($request, [

                'type'=>'required',
                'description'=>'required',
                'uid' => 'required'

            ]);

            Scholarship::upsert($request->except('_token'), $uniqueBy =['type'], $update=[

                'type',
                'description',
                'active'
            ]);

            return back()->with('success', "Scholarship type created successfully");

            return "You are good to go, we are in store";


        }


    return "<br> You do not have the required permission to visit this page";
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
        if (user()->hasRole('admin')) {

                $this->validate($request, [

                    'uid' =>'required',
                    'id' => 'required',
                    'type' => 'required',
                    'description' => 'required',
                    'active' => 'required'


                ]);

                //return $request;

                // Prepare fields for update
                // Check if record exist for this member, then update, else
                Scholarship::upsert($request->except('_token', '_method'), $uniqueBy ='id', $update=[
                    'type',
                    'description',
                    'uid',
                    'active'
                ]);

                return back()->with('success', "Scholarship type updated successfully !!!");


            }


        return "<br> You do not have the required permission to visit this pages";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (user()->hasRole('admin')) {

            //Enter you code here

            $toDelete = Scholarship::findOrFail($id);

            try {

                $toDelete->delete();

                return redirect(route('scholarsips.index'));

            } catch (\Illuminate\Database\QueryException $e) {

                return redirect(route('scholarsips.index'))->with('error',"Error!!! Schorlarship Type could not be deleted");
            }


            return redirect(route('scholarsips.index'));

            abort(403, "This action is dangerous, proceed with caution!!!");


        }


    abort(403, "<br> You do not have the required permission to visit this page");
    }

    //start scholarship processing functions from here begining with import


    




}
