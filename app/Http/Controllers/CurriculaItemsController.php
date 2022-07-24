<?php

namespace App\Http\Controllers;

use App\Models\CurriculumItem;
use Illuminate\Http\Request;

class CurriculaItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

            //return $request;

            $this->validate($request, [

                '_token'=>'required',
                'curricula_id'=>'required',
                'semester_courses_id'=>'required',
                'category'=>'required',
            ]);

            if ($request->semester_courses_id ===$request->alternative) {
                return redirect()->route('curricula.show',['curriculum'=>$request->curricula_id])->with('error','Duplicate Alternative Course Found, Correct and try again !!!');
            }

            if ($request->category=='core/optional') {
                CurriculumItem::upsert($request->except('_token'), $uniqueBy ='id', $update=[

                    'curricula_id',
                    'semester_courses_id',
                    'alternative',
                    'category',
                ]);

                return redirect()->route('curricula.show',['curriculum'=>$request->curricula_id])->with('info','Core/Optional Course Added Successfuly !!!');
            }

            CurriculumItem::upsert($request->except('_token','alternative'), $uniqueBy ='id', $update=[

                'curricula_id',
                'semester_courses_id',
                'category',
            ]);

            return redirect()->route('curricula.show',['curriculum'=>$request->curricula_id])->with('info','Course Added Successfuly !!!');

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

                '_token'=>'required',
                'curricula_id'=>'required',
                'semester_courses_id'=>'required',
                'category'=>'required',
            ]);



            if ($request->semester_courses_id ===$request->alternative) {
                return redirect()->route('curricula.show',['curriculum'=>$request->curricula_id])->with('error','Duplicate Alternative Course Found, Correct and try again !!!');
            }

            if ($request->category=='core/optional') {

                $course = CurriculumItem::find($id);

                $course->semester_courses_id = $request->semester_courses_id;
                $course->category = 'core/optional';
                $course->alternative = $request->alternative;
                $course->save();

                return redirect()->route('curricula.show',['curriculum'=>$request->curricula_id])->with('info','Core/Optional Course Added Successfuly !!!');
            }

            if ($request->category=='core') {

                $course = CurriculumItem::find($id);

                $course->semester_courses_id = $request->semester_courses_id;
                $course->category = 'core';
                $course->alternative = null;
                $course->save();

                return redirect()->route('curricula.show',['curriculum'=>$request->curricula_id])->with('info','Core Course Added Successfuly !!!');
            }

            $course = CurriculumItem::find($id);

                $course->semester_courses_id = $request->semester_courses_id;
                $course->category = 'elective';
                $course->alternative = null;
                $course->save();

            return redirect()->route('curricula.show',['curriculum'=>$request->curricula_id])->with('info','Elective Course Updated Successfuly !!!');

            return "You are good to go, we are in store";


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
            $toDelete = CurriculumItem::findOrFail($id);
            //return $toDelete->curricula_id;

            try {

                $toDelete->deleteOrFail();

                return redirect()->route('curricula.show',['curriculum'=>$toDelete->curricula_id])->with('info','Course Deleted Successfuly !!!');

            } catch (\Illuminate\Database\QueryException $e) {

                return redirect()->route('curricula.show',['curriculum'=>$toDelete->curricula_id])->with('error','Course could not be deleted, Please contact ICT Admin');
            }



            return "You are good to go and destroy, This is dangerous so you are not allowed, Call me";


        }
    return "<br> You do not have the required permission to visit this page";
    }
}
