<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class RoleManagementController extends Controller
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
            $staffRoles = Role::all();

            return view('admin.configs.viewRoles', compact('staffRoles'));
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

                'name'=>'required',

            ]);

            $data = [
                'name'=>$request->name,
                'guard_name' => 'web'
            ];

            Role::upsert($data, $uniqueBy =['name'], $update=[

                'name',
            ]);

            return redirect(route('rolemanagement.index'));

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
                'id',
                'name',

            ]);

            $prevent = ['applicant', 'admin', 'student', 'dean', 'lecturer', 'hod', 'exam_officer','reg_officer','bursar','vc','audit','pg_sec', 'pg_dean', 'ict_support','staff'];

            $preventIds = Role::where('id','<=',14)->get()->pluck('id');


            //return $preventIds;

            $toUpdate = Role::whereNotIn('id', $preventIds)->where('id', $id)->first();

            //return $toUpdate;

            if ($toUpdate) {
                $toUpdate->name = $request->name;
                $toUpdate->save();
                return redirect(route('rolemanagement.index'))->with('info', "Record updated successfully !!!");
            }else{
                return redirect(route('rolemanagement.index'))->with('error', "Error!!! You cannot edit this role, Contact Developer");
            }


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
        return redirect(route('rolemanagement.index'))->with('error', "This action is disabled");
    }
}
