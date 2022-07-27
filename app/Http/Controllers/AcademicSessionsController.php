<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;

class AcademicSessionsController extends Controller
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
            $academicSessions = AcademicSession::all();

            return view('admin.configs.viewAcademicSessions', compact('academicSessions'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return back()->with('info', "Not allowed");
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
            //validate request;
            $this->validate($request,[
                'uid' => 'required',
                'name' => 'required',
                'currentSemester' => 'required',
                'description' => 'required',
                'status' => 'required'
            ]);


            $newSession = AcademicSession::updateOrCreate(['name'=>$request->name],$request->except('_token'));


            return back()->with('success', "New Academic Session created successfuly");


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
        if (user()->hasRole('admin')) {
            //validate request;
            $this->validate($request,[
                'uid' => 'required',
                'name' => 'required',
                'currentSemester' => 'required',
                'description' => 'required',
                'status' => 'required'
            ]);

            $data = [
                'description' => $request->description,
                'currentSemester' => $request->currentSemester,
                'status' => $request->status
            ];


            $newSession = AcademicSession::updateOrCreate(['id'=>$id],$data);


            return back()->with('success', "New Academic Session updated successfuly");


        }
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

            //return 'user is admin';

            $toDelete = AcademicSession::where('id','>',activeSession()->id)->first();

            if (!$toDelete) {
                return back()->with('error', "You cannot delete this session now, Contact ICT");
            }

            try {

                $toDelete->delete();

                return back()->with('info', "Academic Session Deleted Successfully!!!");

            } catch (\Illuminate\Database\QueryException $e) {

                return back()->with('error',"Error!!! Academic Session could not be deleted!!!");
            }


            return redirect(route('acadsessions.index'));

            abort(403, "This action is dangerous, proceed with caution!!!");


        }


    abort(403, "<br> You do not have the required permission to visit this page");
    }
}
