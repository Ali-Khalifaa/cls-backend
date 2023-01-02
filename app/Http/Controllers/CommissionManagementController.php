<?php

namespace App\Http\Controllers;

use App\Models\ComissionManagement;
use App\Models\Commission;
use App\Models\SalesComissionPlan;
use App\Models\SalesTarget;
use App\Models\TargetEmployees;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommissionManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commissions = Commission::with(['commissionType','per'])->get();

        return response()->json($commissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'commission_type_id' => 'required|exists:commission_types,id',
            'per_id' => 'required|exists:pers,id',
            'amount' => 'required|numeric',
            'percentage' => 'required|numeric|lte:100'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $commissions = Commission::create([
            'name' => $request->name,
            'commission_type_id' => $request->commission_type_id,
            'per_id' => $request->per_id,
            'amount' => $request->amount,
            'percentage' => $request->percentage,
        ]);

        return response()->json($commissions);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $commissions = Commission::where('commission_type_id',$id)->with(['commissionType','per'])->get();

        return response()->json($commissions);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'commission_type_id' => 'required|exists:commission_types,id',
            'per_id' => 'required|exists:pers,id',
            'amount' => 'required|numeric',
            'percentage' => 'required|numeric|lte:100'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $commissions = Commission::findOrFail($id);

        $commissions->update([
            'name' => $request->name,
            'commission_type_id' => $request->commission_type_id,
            'per_id' => $request->per_id,
            'amount' => $request->amount,
            'percentage' => $request->percentage,
        ]);

        return response()->json($commissions);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $commissions = Commission::findOrFail($id);
        $commissions->delete();

        return response()->json('deleted success');
    }
}
