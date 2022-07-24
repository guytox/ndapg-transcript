<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

                //get a list of all faculties ready to return to the page, fetch everything

                //$faculties = Faculty::all()->pluck('facultyDescription', 'facultyName', 'facultyDescription','id');
                if (user()->hasRole('admin')) {
                    $faculties = Faculty::all();

                    return view('admin.configs.viewFaculties', compact('faculties'));
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
        abort(403, "You should not be here");
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

                    'name'=>'required',
                    'description'=>'required',

                ]);

                Faculty::upsert($request->except('_token'), $uniqueBy =['facultyName'], $update=[

                    'name',
                    'description'
                ]);

                return redirect(route('faculties.index'));

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
        abort(403, "you should not come here");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
                abort(403, "You should not come here");

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
                    'id',
                    'facultyName',
                    'facultyDescription',


                ]);

                //return $request;

                // Prepare fields for update
                // Check if record exist for this member, then update, else
                Faculty::upsert($request->except('_token', '_method'), $uniqueBy ='id', $update=[
                    'name',
                    'description',
                    'uid'
                ]);

                return redirect(route('faculties.index'));


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

                $toDelete = Faculty::findOrFail($id);

                try {

                    $toDelete->delete();

                    return redirect(route('faculties.index'));

                } catch (\Illuminate\Database\QueryException $e) {

                    return redirect(route('faculties.index'))->with('error',"Error!!! Faculty could not be deleted");
                }


                return redirect(route('faculties.index'));

                abort(403, "This action is dangerous, proceed with caution!!!");


            }


        abort(403, "<br> You do not have the required permission to visit this page");
    }

    public function getDeans(){

       //get a list of all faculties ready to return to the page, fetch everything

                //$faculties = Faculty::all()->pluck('facultyDescription', 'facultyName', 'facultyDescription','id');
                if (user()->hasRole('admin')) {
                    $faculties = Faculty::all();

                    $staffList = User::role('staff')->get()->pluck('name','id');


                    return view('admin.configs.viewDeans', compact('faculties','staffList'));
                }

                abort(403, "You do not have permission to view this page");
    }

    public function revokeDeans(Request $request, $id){

        if (user()->hasRole('admin')) {
            $revokeDean = Faculty::find($id);
            $revokeDean->dean_id = null;
            $revokeDean->save();

            return redirect()->route('appointments.get.deans')->with('info',"Dean Appointment Revoked successfully !!!");
        }

        return redirect()->route('appointments.get.deans')->with('error',"There was a problem revoking Dean, Contact ICT !!!");


    }

    public function assignDean(Request $request, $id){

        if (user()->hasRole('admin')) {
            $revokeDean = Faculty::find($id);

            if ($revokeDean->dean_id!='') {
                $presentDean = User::find($revokeDean->dean_id);
                $presentDean->removeRole('dean');
            }

            $revokeDean->dean_id = $request->dean_id;
            $revokeDean->save();

            $dean = User::find($request->dean_id);
            $dean->assignRole('dean');

            return redirect()->route('appointments.get.deans')->with('info',"Dean Appointment Successful !!!");
        }

        return redirect()->route('appointments.get.deans')->with('error',"There was a problem Appointing Dean, Contact ICT !!!");


    }






}
