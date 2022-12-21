<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyActivity;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::with(['companyContacts','leadSource','companyDeals','companyActivities','employee','leads','companyFollowup'
            ,'companyActivities'=>function($q){
            $q->with(['subject','employees']);
        }])->where([
            ['is_client',0],
            ['add_placement',0],
        ])->get();

        return response()->json($companies);
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
            'name_en' => 'required|string|max:100',
            'name_ar' => 'nullable|string|max:100',
            'phone' => 'nullable|unique:companies,phone',
            'website' => 'required|string|max:250',
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'lead_source_id' => 'required|exists:lead_sources,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $request_data = $request->all();
        if($request->hasFile('file'))
        {
            $img = $request->file('file');
            $ext = $img->getClientOriginalExtension();
            $image_name = $request->name_en . "-". uniqid() . ".$ext";
            $img->move( public_path('uploads/companies/') , $image_name);

            $request_data['pdf'] = $image_name;
        }
        $company = Company::create($request_data);

        return response()->json($company);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::with(['companyContacts','leadSource','leads','companyActivities','employee','companyFollowup',
            'companyActivities'=>function($q){
            $q->with(['subject','employees']);
        }])->findOrFail($id);

        return response()->json($company);
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
            'name_en' => 'required|string|max:100',
            'name_ar' => 'nullable|string|max:100',
            'phone' => 'nullable|unique:companies,phone' . ($id ? ",$id" : ''),
            'website' => 'required|string|max:250',
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'lead_source_id' => 'required|exists:lead_sources,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $company = Company::findOrFail($id);
        $img_name = $company->pdf;

        $request_data = $request->all();
        // image upload
        if ($request->pdf != "null" || $request->pdf != null) {
            if ($request->hasFile('pdf')) {

                if ($img_name != null) {
                    unlink(public_path('uploads/companies/') . $img_name);
                }

                $img = $request->file('image');
                $ext = $img->getClientOriginalExtension();
                $image_name =  $company->name_en . "-". uniqid() .".$ext";
                $img->move(public_path('uploads/companies/'), $image_name);
                $request_data['img'] = $image_name;
            }
        }else{
            $request_data['img'] = $img_name;
        }

        $company->update($request_data);

        return response()->json($company);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        $company->delete();
        return response()->json('deleted successfully');
    }

    /**
     * Moving company to another Employee.
     */
    public function movingCompanyToAnotherEmployee(Request $request,$id)
    {
        $company = Company::findOrFail($id);
        $company->update([
            'employee_id' => $request->employee_id,
            'add_list' => 0
        ]);

        return response()->json('moving successfully');
    }

    /**
     * get one company to employee.
     */
    public function getTenCompanyToEmployee($id)
    {
        $leadEmployees = Company::where([
            ['employee_id','=',$id],
            ['add_list','=',0],
            ['is_client','=',0],
            ['add_placement','=',0],
        ])->get();

        if (count($leadEmployees) == 0)
        {
            $leads = Company::where([
                ['employee_id','=',null],
                ['add_list','=',0],
                ['is_client','=',0],
                ['add_placement','=',0],
            ])->get();

            if (count($leads) == 0){
                return response()->json("sorry no companies now",422);
            }

            if (count($leads) >= 1){
                $leads = Company::where([
                    ['employee_id','=',null],
                    ['add_list','=',0],
                    ['is_client','=',0],
                    ['add_placement','=',0],
                ])->get()->random(1);

                foreach ($leads as $lead)
                {
                    $lead->update([
                        'employee_id' => $id
                    ]);
                }
            }else{
                foreach ($leads as $lead)
                {
                    $lead->update([
                        'employee_id' => $id
                    ]);
                }
            }
            return response()->json($leads);

        }else{

            return response()->json('sorry you have leads',422);

        }

    }

    /**
     * get companies by employee id
     */

    public function getCompaniesEmployee($id)
    {
        $leads = Company::with(['companyContacts','leadSource','leads','companyActivities'=>function($q){
            $q->with(['subject','employees']);
        },'employee','companyFollowup'])
            ->where([
            ['employee_id','=',$id],
            ['add_list','=',0],
            ['is_client','=',0],
        ])->get();

        return response()->json($leads);
    }

    /**
     * get companies Open Task by employee id
     */

    public function getCompaniesOpenTaskEmployee($id)
    {
        $leads = Company::with(['companyContacts','leadSource','leads','companyActivities'=>function($q){
            $q->with(['subject','employees']);
        },'employee','companyFollowup'])
            ->where([
                ['employee_id','=',$id],
                ['add_list','=',1],
                ['is_client','=',0],
            ])->get();

        return response()->json($leads);
    }

}
