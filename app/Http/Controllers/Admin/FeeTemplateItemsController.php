<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeTemplateItem;
use Illuminate\Http\Request;

class FeeTemplateItemsController extends Controller
{

    public function addFeeTemplateItem($feeTemplateId, $feeItemId, $feeItemAmount){

        $item = FeeTemplateItem::updateOrCreate(['fee_template_id'=>$feeTemplateId, 'fee_item_id' => $feeItemId],[

            'fee_template_id'=>$feeTemplateId,
            'fee_item_id' => $feeItemId,
            'item_amount' => convertToKobo($feeItemAmount)

        ]);

        return back()->with(['message'=>'Congratulations!!! Fee Template Item Updated Successfully !!!']);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function deleteFeeTemplateItem(Request $request, $id){

        $deletedItem = FeeTemplateItem::where('id', $id)->first();
        $deletedItem ->delete();

        return back()->with(['message'=> 'Fee Template Item Deleted Successfully from Fee Template !!!']);

    }


    public function addNewTemplateItem(Request $request){

        $validated = $request->validate([
            'fee_template_id' => 'required|integer',
            'fee_item_id' => 'required|integer',
            'item_amount' => 'required|numeric',
        ]);

        //return $request;

        $item = FeeTemplateItem::updateOrCreate(['fee_template_id'=>$request->fee_template_id, 'fee_item_id' => $request->fee_item_id],[

            'fee_template_id'=>$request->fee_template_id,
            'fee_item_id' => $request->fee_item_id,
            'item_amount' => convertToKobo($request->item_amount)

        ]);

        return back()->with(['message'=>'Congratulations!!! Fee Template Item Added Successfully !!!']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function editFeeTemplateItem(Request $request, $id){

        $validated = $request->validate([
            'fee_template_id' => 'required|integer',
            'fee_item_id' => 'required|integer',
            'item_amount' => 'required|numeric',
        ]);

        $item = FeeTemplateItem::where('id', $id)->where('fee_template_id', $request->fee_template_id)->update([

            'fee_template_id'=>$request->fee_template_id,
            'fee_item_id' => $request->fee_item_id,
            'item_amount' => convertToKobo($request->item_amount)

        ]);

        //return $item;


        return back()->with(['message'=>'Congratulations!!! Fee Template Item Modified Successfully !!!']);
    }


}
