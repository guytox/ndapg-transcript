<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCategory;
use App\Models\FeeConfig;
use App\Models\FeeTemplate;
use App\Models\Program;
use App\Models\StudyLevel;
use Illuminate\Http\Request;
use Psy\Command\WhereamiCommand;

class FeeConfigurationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feeConfigs = FeeConfig::orderBy('narration')->with('feeTemplate')->get();

        //dd($feeConfigs);

        //get fee templates
        $feeTemplate = FeeTemplate::all();
        $feeTemplates = $feeTemplate->pluck('narration', 'id');
        $accounts =[
            "school"=>"tuition",
            "internet_service"=>"UMM Portal Services",
            'late_reg' => "Late Registration",
            'acceptance' => "Acceptance Fees",
            'other' => "EDC Payments",
        ];
        // get fee categories
        $categories = FeeCategory::all()->pluck('category_name', 'id');



        //return $categories;

        return view('bursar.viewAllFeeConfigs', compact('feeConfigs', 'feeTemplates', 'categories','accounts'));
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

        //dd($request);

        $validated = $request->validate([
            'fee_template_id' => 'required|integer',
            'fee_category_id' => 'required|integer',
            'narration' => 'required|string',
            'account' => 'required|string',
            'study_level' => 'present',
            'program_id' => 'present',
            'semester' => 'present',
            'in_state' => 'present',
            'user_id' => 'present',

        ]);

        if($request->in_state == ''){

             $in_state=null;

        }elseif ($request->in_state == 0) {

            $in_state = false;

        }elseif ($request->in_state == 1) {

            $in_state = true;

        }

        //return $in_state;

        $newFeeTemplate = FeeConfig::updateOrCreate(['narration'=> $request->narration],[

            'fee_template_id' => $request->fee_template_id,
            'fee_category_id' => $request->fee_category_id,
            'narration' => $request->narration,
            'study_level_id' => $request->study_level,
            'program_id' => $request->program_id,
            'semester_id' => $request->semester,
            'in_state' => $in_state,
            'user_id' => $request->user_id,
            'created_by' => user()->id,
            'account' => $request->account,

        ]);

        return back()->with(['success'=>'Fee Config Created Successfully !!!']);
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
            'fee_template_id' => 'required|integer',
            'fee_category_id' => 'required|integer',
            'narration' => 'required|string',
            'study_level' => 'present',
            'program_id' => 'present',
            'semester' => 'present',
            'in_state' => 'present',
            'user_id' => 'present',
            'account' => 'required|string',

        ]);

        if($request->in_state == ''){

             $in_state=null;

        }elseif ($request->in_state == 0) {

            $in_state = false;

        }elseif ($request->in_state == 1) {

            $in_state = true;

        }

        //return $request;

        $newConfig = FeeConfig::find($request->id);

        //return $newConfig;

        $newConfig->fee_template_id = $request->fee_template_id;
        $newConfig->fee_category_id = $request->fee_category_id;
        $newConfig->narration = $request->narration;
        $newConfig->study_level_id = $request->study_level;
        $newConfig->program_id = $request->program_id;
        $newConfig->semester_id = $request->semester;
        $newConfig->in_state = $in_state;
        $newConfig->user_id = $request->user_id;
        $newConfig->account = $request->account;
        $newConfig->created_by = user()->id;
        $newConfig->save();

        return back()->with(['success'=>'Fee Config Updated Successfully !!!']);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deletedTemplate = FeeConfig::find($id);

        $deletedTemplate->delete();

        return back()->with(['success'=>'Fee Config Deleted Successfully !!!']);
    }
}
