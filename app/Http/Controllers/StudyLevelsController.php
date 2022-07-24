<?php

namespace App\Http\Controllers;

use App\Models\StudyLevel;
use Illuminate\Http\Request;

class StudyLevelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get a list of all Study Levels ready to return to the page, fetch everything


                if (user()->hasRole('admin')) {
                    $studyLevels = StudyLevel::all();

                    return view('admin.configs.viewStudyLevels', compact('studyLevels'));
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

                'level'=>'required',
                'description'=>'required',
                'uid'=>'required'

            ]);

            StudyLevel::upsert($request->except('_token'), $uniqueBy =['facultyName'], $update=[

                'level',
                'description'
            ]);

            return redirect(route('studylevels.index'));

            return "You are good to go, we are in store";


        }

        abort(403,"You do not have priviledges to view this page");
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

                'uid',
                'level',
                'description',


            ]);

            //return $request;

            // Prepare fields for update
            // Check if record exist for this member, then update, else
            StudyLevel::upsert($request->except('_token', '_method'), $uniqueBy ='id', $update=[
                'level',
                'description',
                'uid'
            ]);

            return redirect(route('studylevels.index'));


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

            $toDelete = StudyLevel::findOrFail($id);

            try {

                $toDelete->delete();

                return redirect(route('studylevels.index'));

            } catch (\Illuminate\Database\QueryException $e) {

                return redirect(route('studylevels.index'))->with('error',"Error!!! StudyLevel could not be deleted");
            }


            return redirect(route('studylevels.index'));

            abort(403, "This action is dangerous, proceed with caution!!!");


        }


    abort(403, "<br> You do not have the required permission to visit this page");
    }
}
