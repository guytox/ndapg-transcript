<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\UserTraits;
use App\Models\FeeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeeItemsController extends Controller
{

    use UserTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feeItems = FeeItem::orderBy('name')->get();

        //return $feeItems;

        return view('bursar.viewFeeItems', compact('feeItems'));
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


        $paymentItem = FeeItem::updateOrCreate( ['name' => $request->name], [
            'name' => $request->name,
            'description' => Str::slug(strtolower($request->name))
        ]);

        return redirect(route('fee-items.index'))->with(['message' => 'Fee Item Created Successfully']);
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
        return redirect(route('fee-items.index'))->with(['message' => "Error !!!, Update Not allowed, Contact ICT"]);
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

            $toDelete = FeeItem::find($id);
            $toDelete->delete();

            return redirect(route('fee-items.index'))->with(['message' => "Deleting a Fee Item Not allowed, Be Careful"]);

        }else{

            return redirect(route('fee-items.index'))->with(['message' => "Error !!!, Deleting a Fee Item Not allowed, Contact ICT"]);
        }

    }
}
