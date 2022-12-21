<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assets =Asset::with(['employee','lab','branch','office','configuration','software'])->get();

        return response()->json($assets);
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
            'date_of_purchasing' => 'required|date',
            'color' => 'required|string|max:100',
            'price' => 'required|numeric',
            'serial_number' => 'required|max:100',
            'pdf' => 'required|mimes:pdf|max:10000',
            'employee_id' => 'required|exists:employees,id',
            'lab_id' => 'nullable|exists:labs,id',
            'office_id' => 'nullable|exists:offices,id',
            'branch_id' => 'required|exists:branches,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        if ($request->hasFile('pdf')){
            $img = $request->file('pdf');
            $ext = $img->getClientOriginalExtension();
            $pdf_name = "asset-" . uniqid() . ".$ext";
            $img->move(public_path('uploads/assets/pdf/'), $pdf_name);
        }else{
            $pdf_name = "no file";
        }

        $assets = Asset::create([
            'name' => $request->name,
            'date_of_purchasing' => $request->date_of_purchasing,
            'color' => $request->color,
            'price' => $request->price,
            'serial_number' => $request->serial_number,
            'employee_id' => $request->employee_id,
            'lab_id' => $request->lab_id,
            'branch_id' => $request->branch_id,
            'office_id' => $request->office_id,
            'pdf' => $pdf_name,
        ]);

        foreach (json_decode($request->configurations , true) as $configuration){
            $assets->configuration()->create([
                'configuration_name' => $configuration['configuration_name'],
                'configuration_value' => $configuration['configuration_value']
            ]);
        }

        $assets->software()->create([
           'name' => $request->software_name,
           'end_date' => $request->software_end_date,
        ]);

        return response()->json($assets);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $assets =Asset::with(['employee','lab','branch','office','configuration','software'])->findOrFail($id);
        return response()->json($assets);
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
            'date_of_purchasing' => 'required|date',
            'color' => 'required|string|max:100',
            'price' => 'required|numeric',
            'serial_number' => 'required|max:100',
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'employee_id' => 'required|exists:employees,id',
            'lab_id' => 'nullable|exists:labs,id',
            'office_id' => 'nullable|exists:offices,id',
            'branch_id' => 'required|exists:branches,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $assets =Asset::findOrFail($id);

        $pdf_name = $assets->pdf;
        // c.v upload
        if ($request->pdf != "null" || $request->pdf != null) {
            if ($request->hasFile('pdf')) {

                $validator = Validator::make($request->all(), [
                    'pdf' => 'required|mimes:pdf|max:10000',
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return response()->json($errors, 422);
                }

                if (File::exists('uploads/assets/pdf/' . $pdf_name) && $pdf_name != null) {
                    unlink(public_path('uploads/assets/pdf/') . $pdf_name);
                }

                $img = $request->file('pdf');
                $ext = $img->getClientOriginalExtension();
                $pdf_name = "asset-" . uniqid() . ".$ext";
                $img->move(public_path('uploads/assets/pdf/'), $pdf_name);
            }
        }

        $assets->update([
            'name' => $request->name,
            'date_of_purchasing' => $request->date_of_purchasing,
            'color' => $request->color,
            'price' => $request->price,
            'serial_number' => $request->serial_number,
            'employee_id' => $request->employee_id,
            'lab_id' => $request->lab_id,
            'branch_id' => $request->branch_id,
            'office_id' => $request->office_id,
            'pdf' => $pdf_name,
        ]);

        foreach ($assets->configuration as $value){
            $value->delete();
        }

        foreach (json_decode($request->configurations , true) as $configuration){
            $assets->configuration()->create([
                'configuration_name' => $configuration['configuration_name'],
                'configuration_value' => $configuration['configuration_value']
            ]);
        }

        $assets->software()->update([
            'name' => $request->software_name,
            'end_date' => $request->software_end_date,
        ]);

        return response()->json($assets);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assets =Asset::find($id);
        $assets->delete();

        return response()->json('deleted success');

    }

}
