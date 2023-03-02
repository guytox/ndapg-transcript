<?php

namespace App\Http\Controllers;

use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Http\Request;

class ChangeOfNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.update-name-form')->with('action','new');
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
        $studentDetail = User::where('username', $request->matric)->select('id','name')->first();

        if ($studentDetail) {
            //return $studentDetail;
            return view('admin.update-name-form', compact('studentDetail'))->with('action','update');
        }else{
            return back()->with('error',"Error!!! User Not found, Please try again");
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
        $userToUpdate = User::find($id);
       // return $userToUpdate;
        $userToUpdate->name = $request->newname;
        $userToUpdate->save();

        return redirect(route('home'))->with('info',"Name Changed Successfully!!!!");
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
