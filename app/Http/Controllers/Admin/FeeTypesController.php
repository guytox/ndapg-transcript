<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeeTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feeTypes = FeeType::orderBy('name')->get();

        //return $feeTypes;

        return view('bursar.viewFeeTypes', compact('feeTypes'));
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
            'name' => 'required|string'
        ]);


        if ($this->middleware(['role:bursar'])) {

            $paymentItem = FeeType::updateOrCreate( ['name' => $request->name], [
                'name' => $request->name,
                'description' => Str::slug(strtolower($request->name))
            ]);

            return redirect(route('fee-types.index'))->with(['message' => 'Fee Type Created Successfully']);
        }

        return redirect(route('logout'));





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
        $validated = $request->validate([
            'name' => 'required|string'
        ]);

        //return $request;

        $paymentType = FeeType::find($request->id);

        $paymentType->name  = $request->name;
        $paymentType->description  = $request->description;
        $paymentType->save();


        // $paymentItem = FeeType::updateOrCreate( ['name' => $request->name], [
        //     'name' => $request->name,
        //     'description' => Str::slug(strtolower($request->name))
        // ]);

        return redirect(route('fee-types.index'))->with(['message' => 'Fee Item Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $feeType = FeeType::find($id);
        $feeType->delete();

        return redirect(route('fee-types.index'))->with(['message' => "Record deleted Successfully! ! !"]);
    }
}
