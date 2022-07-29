<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


            if (user()->hasRole('admin')) {

                //Enter you code here

                //get a list of all faculties ready to return to the page, fetch everything

                //$faculties = Faculty::all()->pluck('facultyDescription', 'facultyName', 'facultyDescription','id');
                $departments = Department::orderBy('faculty_id','asc')
                                            ->orderBy('name','asc')
                                            ->get();

                //return $departments;

                $faculty = Faculty::all();
                $faculties = $faculty->pluck('name','id');

                //return $departments.$faculties;



                return view('admin.configs.viewDepartments', compact('departments','faculties'));


            }


        return "<br> You do not have the required permission to visit this page";




    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        auth()->logout();
        return redirect(route('login'));
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
                    'faculty_id'=>'required',
                    'name'=>'required',
                    'description'=>'required',
                    'uid'=>'required',
                    'academic' => 'required'

                ]);

                Department::upsert($request->except('_token'), $uniqueBy ='id', $update=[

                    'faculty_id',
                    'name',
                    'uid',
                    'description',
                    'academic'
                ]);

                return redirect(route('departments.index'));

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
        return redirect(route('logout'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect(route('logout'));

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

                    '_token'=>'required',
                    'faculty_id'=>'required',
                    'name'=>'required',
                    'description'=>'required',
                    'uid'=>'required',
                    'academic' => 'required'

                ]);

                // Prepare fields for update
                // Check if record exist for this member, then update, else
                Department::upsert($request->except('_token', '_method'), $uniqueBy ='id', $update=[

                    'faculty_id',
                    'name',
                    'description',
                    'academic'
                ]);

                return redirect(route('departments.index'));


            }
        return "<br> You do not have the required permission to visit this page";




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

                //return $id;
                //Enter you code here
                $toDelete = Department::findOrFail($id);

                try {

                    $toDelete->deleteOrFail();

                    return redirect(route('departments.index'));

                } catch (\Illuminate\Database\QueryException $e) {

                    return redirect(route('departments.index'))->with('error',"Error!!! Department could not be deleted");
                }



                return "You are good to go and destroy, This is dangerous so you are not allowed, Call me";


            }
        return "<br> You do not have the required permission to visit this page";




    }


    public function getHods(){
        //get a list of all Hods ready to return to the page, fetch everything

                //$faculties = Faculty::all()->pluck('facultyDescription', 'facultyName', 'facultyDescription','id');
                if (user()->hasRole('admin')) {
                    $departments = Department::all();

                    $faculties = Faculty::all()->pluck('name','id');

                    $staffList = User::role('staff')->get()->pluck('name','id');


                    return view('admin.configs.viewHods', compact('departments','staffList','faculties'));
                }

                abort(403, "You do not have permission to view this page");
    }


    public function revokeHods(Request $request, $id){

        if (user()->hasRole('admin')) {
            $revokeHod = Department::find($id);
            $revokeHod->hod_id = null;
            $revokeHod->exam_officer_id = null;
            $revokeHod->registration_officer_id = null;
            $revokeHod->save();

            return redirect()->route('appointments.get.hods')->with('info',"All Appointments Revoked successfully !!!");
        }

        return redirect()->route('appointments.get.hods')->with('error',"There was a problem revoking Dean, Contact ICT !!!");


    }

    public function assignHod(Request $request, $id){

        if (user()->hasRole('admin')) {
            if ($request->uid != '') {
                abort(403, "You do not have permission to view this page");
            }

            $param = $request->role;

            switch ($request->role) {
                case 'hod_id':
                    $role = 'hod';
                    break;

                case 'registration_officer_id':
                    $role = 'reg_officer';
                    break;

                case 'exam_officer_id':
                    $role = 'exam_officer';
                    break;

                default:
                    $role = 'staff';
                    break;
            }



            $revokeDept = Department::find($id);


            if ($revokeDept->$param!='') {
                $presentApt = User::find($revokeDept->$param);
                $presentApt->removeRole($role);
            }

            $revokeDept->$param = $request->staff_id;
            $revokeDept->save();

            $newRole = User::find($request->staff_id);
            $newRole->assignRole($role);

            return redirect()->route('appointments.get.hods')->with('info',$role." Appointment Successful !!!");
        }

        return redirect()->route('appointments.get.hods')->with('error',"There was a problem with the Appointment, Contact ICT !!!");


    }




}
