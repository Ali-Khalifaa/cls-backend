<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyActivity;
use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyActivityController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'due_date' => 'required|date|after:yesterday',
            'description' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $companyActivity = CompanyActivity::create($request->all());
        $lead = Company::findOrFail($request->company_id);
        $lead->update([
            'add_list' => 1
        ]);

        return response()->json($companyActivity);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $companyActivity = CompanyActivity::with(['company','subject','employees'])->where('company_id','=',$id)->get();

        return response()->json($companyActivity);
    }

    // close task

    public function closeTaskCompany(Request $request){
        $validator = Validator::make($request->all(), [
            'company_activity_id' => 'required|exists:company_activities,id',
            'close_open' => 'required|integer'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        if ($request->close_open == 1){
            $validator = Validator::make($request->all(), [
                'subject_id' => 'required|exists:subjects,id',
                'due_date' => 'required|date|after:yesterday',
                'description' => 'required|string',
                'company_id' => 'required|exists:companies,id',
                'employee_id' => 'required|exists:employees,id',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json($errors,422);
            }

            CompanyActivity::create($request->all());
        }

        $companyActivity = CompanyActivity::find($request->lead_activity_id);

        $companyActivity->update([
            'close_date' => now()
        ]);

        $companyActivity->company()->update([
            'add_list' => $request->close_open
        ]);

        return response()->json('successfully');
    }

    /**
     * get lead interview by employee id and company id
     */

    public function getLeadInterviewByEmployeeIdAndCompanyId($employee_id,$company_id)
    {
        $leads = Lead::with(['country','city','employee','interestingLevel','leadSources','leadCourses','leadDiplomas','leadActivities'])
            ->where([
                ['employee_id',$employee_id],
                ['is_client','=',0],
                ['add_interview_sales','=',1],
                ['add_interview','=',0],
                ['add_course_sales','=',0],
                ['add_selta','=',0],
                ['lead_type','=',1],
                ['company_id','=',$company_id],
            ])->get();

        return response()->json($leads);
    }

     /**
     * get lead courses by employee id and company id
     */

    public function getLeadCourseByEmployeeIdAndCompanyId($employee_id,$company_id)
    {
        $leads = Lead::with(['country','city','employee','interestingLevel','leadSources','leadCourses','leadDiplomas','leadActivities'])
            ->where([
                ['employee_id',$employee_id],
                ['is_client','=',0],
                ['add_course_sales','=',1],
                ['lead_type','=',1],
                ['company_id','=',$company_id],

            ])->get();

        return response()->json($leads);
    }

}
