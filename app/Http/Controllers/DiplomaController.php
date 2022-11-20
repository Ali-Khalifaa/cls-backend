<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use App\Models\DiplomaPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiplomaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $diplomas =Diploma::with(['diplomaPrices','caterings','courses'])->get();

        return response()->json($diplomas);
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
            'configuration_pcs' => 'nullable|string|max:300',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'diploma_code' => 'required|string|max:100|unique:diplomas,diploma_code',
            'catering'=>'required',
            'courses'=>'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();
            return response()->json($errors,422);

        }

        $diploma = Diploma::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'vendor_id' => $request->vendor_id,
            'diploma_code' => $request->diploma_code,
            'configuration_pcs' => $request->configuration_pcs,
        ]);

        $request_data = $request->all();

        $request_data['diploma_id']=$diploma->id;

        DiplomaPrice::create($request_data);

        $diploma->courses()->syncWithoutDetaching($request->courses);
        $diploma->caterings()->syncWithoutDetaching($request->catering);

        return response()->json($diploma);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $diploma = Diploma::with(['courses','vendor','diplomaPrices','courses','traningDiplomas','caterings'])->findOrFail($id);

        return response()->json($diploma);
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
            'configuration_pcs' => 'nullable|string|max:300',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'diploma_code' => 'required|string|max:100|unique:diplomas,diploma_code' . ($id ? ",$id" : ''),
            'catering'=>'required',
            'courses'=>'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();
            return response()->json($errors,422);

        }
        $diploma = Diploma::findOrFail($id);
        $diploma->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'vendor_id' => $request->vendor_id,
            'diploma_code' => $request->diploma_code,
            'configuration_pcs' => $request->configuration_pcs,
        ]);

        $diploma->courses()->sync($request->courses);
        $diploma->caterings()->sync($request->catering);

        return response()->json($diploma);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $diploma = Diploma::findOrFail($id);
        $diploma->delete();

        return response()->json('deleted success');
    }

    /**
     * Activation diploma.
     */

    public function activationDiplomas($id)
    {

        $diploma = Diploma::findOrFail($id);
        if ($diploma->active == 1){

            $diploma->update([
                'active' => 0,
            ]);

        }else{

            $diploma->update([
                'active' => 1,
            ]);
        }

        return response()->json($diploma);
    }

    /**
     * get Active diplomas.
     */
    public function getActiveDiplomas(): \Illuminate\Http\JsonResponse
    {
        $diplomas = Diploma::where('active',1)->get();
        return response()->json($diplomas);
    }

    /**
     * get des Active diplomas.
     */
    public function getDeactivateDiplomas(): \Illuminate\Http\JsonResponse
    {
        $diplomas = Diploma::where('active',0)->get();
        return response()->json($diplomas);
    }

    /**
     * add courses to diploma.
     */
    public function addCoursesToDiploma(Request $request,$id): \Illuminate\Http\JsonResponse
    {
        $diploma = Diploma::find($id);

        $diploma->courses()->sync($request->courses);

        return response()->json($diploma);

    }

    /**
     * detach courses to diploma.
     */
    public function detachCoursesToDiploma(Request $request,$id): \Illuminate\Http\JsonResponse
    {
        $diploma = Diploma::find($id);

        $diploma->courses()->detach($request->courses);

        return response()->json($diploma);

    }

    /**
     * get courses by diploma id.
     */
    public function getCoursesByDiplomaId($id): \Illuminate\Http\JsonResponse
    {
        $diploma = Diploma::with('courses')->find($id);

        return response()->json($diploma['courses']);

    }

}
