<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeeCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feeCategories = FeeCategory::orderBy('category_name')->get();

        //return $feeItems;

        return view('bursar.viewFeeCategories', compact('feeCategories'));
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
        $validated = $request->validate([
            'category_name' => 'required|string'
        ]);


        $paymentCategory = FeeCategory::updateOrCreate( ['category_name' => $request->category_name], [
            'category_name' => $request->category_name,
            'description' => Str::slug(strtolower($request->category_name))
        ]);

        return back()->with(['message' => 'Fee Category Created Successfully']);
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
        if ($this->middleware(['role:admin'])) {

            return redirect(route('fee-categories.index'))->with(['message' => "Error !!!, Are you sure you want to deleted, Contact ICT"]);

        }else{

            return back()->with(['message' => "Error !!!, Not Allowed, Contact ICT"]);
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
        if ($this->middleware(['role:admin'])) {

            $deleted = FeeCategory::find($id);
            $deleted->delete();

            return back()->with(['message' => "Fee Category deleted Successfully"]);

        }else{

            return back()->with(['message' => "Error !!!, Deleting a Fee Item Not allowed, Contact ICT"]);
        }
    }
}
