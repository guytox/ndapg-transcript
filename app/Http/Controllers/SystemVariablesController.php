<?php

namespace App\Http\Controllers;

use App\Models\SystemVariable;
use Illuminate\Http\Request;

class SystemVariablesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    
        //get a list of all variables ready to return to the page, fetch everything
                if (user()->hasRole('admin')) {
                    $systemVariables = SystemVariable::all();

                    return view('admin.configs.viewSystemVariables', compact('systemVariables'));
                }

                return back()->with('error', "You do not have permission to view this page");
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
                'uid'=>'required',
                'description'=>'required',
                'value' => 'required'

            ]);

            SystemVariable::upsert($request->except('_token'), $uniqueBy =['name'], $update=[

                'name',
                'description',
                'value'
            ]);

            return redirect(route('systemvariables.index'))->with('success', "System Varible Uploaded Successfully");




        }


        return back()->with('error', "You do not have the required permission to visit this page");

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
                'name' => 'required',
                'description' => 'required',
                'uid' => 'required'


            ]);

            //return $request;

            // Prepare fields for update
            // Check if record exist for this member, then update, else
            SystemVariable::upsert($request->except('_token', '_method'), $uniqueBy ='id', $update=[
                'name',
                'description',
                'uid',
                'value'
            ]);

            return redirect(route('systemvariables.index'))->with('info', "System Varible Updated Successfully");


        }


        return back()->with('error', "You do not have the required permission to visit this page");


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

            $toDelete = SystemVariable::findOrFail($id);

            try {

                $toDelete->delete();

                return redirect(route('systemvariables.index'));

            } catch (\Illuminate\Database\QueryException $e) {

                return redirect(route('systemvariables.index'))->with('error',"Error!!! System Variable could not be deleted");
            }


            return redirect(route('systemvariables.index'));


            return back()->with('error', "This action is dangerous, proceed with caution!!!");


        }


        return back()->with('error', "You do not have the required permission to visit this page");
    }
}
