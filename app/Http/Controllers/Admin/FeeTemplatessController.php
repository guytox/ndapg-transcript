<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeItem;
use App\Models\FeeTemplate;
use App\Models\FeeTemplateItem;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTemplatessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feeTemplates = FeeTemplate::orderBy('narration')->with('FeeTemplateItems')->get();

        $feeType = FeeType::all();

        $feeTypes = $feeType->pluck('name', 'id');

        //return $feeTemplates;

        return view('bursar.viewAllFeeTemplates', compact('feeTemplates', 'feeTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return "Are you ready to crete?";
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
            'fee_type_id' => 'required|integer',
            'narration' => 'required|string',

        ]);

        $newFeeTemplate = FeeTemplate::updateOrCreate(['narration'=> $request->narration],[

            'fee_type_id'=> $request->fee_type_id,
            'narration'=> $request->narration

        ]);

        return back()->with(['success'=>'Fee Template Created Successfully !!!']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $templateDetails = FeeTemplate::where('id', $id)->with('feeTemplateItems')->first();

        $feeTemplateItem = FeeItem::orderBy('name')->get();

        $feeTemplateItems = $feeTemplateItem->pluck('name', 'id');



        //return $templateDetails;
        return view('bursar.viewSingleFeeTemplate', compact('templateDetails', 'feeTemplateItems'));
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

        //return $request;
        $validated = $request->validate([
            'fee_type_id' => 'required|integer',
            'name' => 'required|string',
            'id' => 'required|integer'

        ]);

        $newFeeTemplate = FeeTemplate::where('id', $id)->update([

            'fee_type_id'=> $request->fee_type_id,
            'narration'=> $request->name

        ]);

        return back()->with(['success'=>'Fee Template Edited Successfully !!!']);



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deletedTemplate = FeeTemplate::find($id);

        if($deletedTemplate->deleteOrFail()){
            return back()->with(['success'=>'Fee Template Deleted Successfully !!!']);
        }else{
            return back()->with(['success'=>'Error!!! This template is used by a fee config and cannot be deleted, Please try again']);
        }




    }
}
