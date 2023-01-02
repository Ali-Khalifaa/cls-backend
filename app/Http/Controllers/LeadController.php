<?php

namespace App\Http\Controllers;

use App\Imports\LeadImport;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadCourse;
use App\Models\LeadDiploma;
use App\Models\LeadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    /**
     * import data excel
     */
    public function leadImport(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        Excel::import(new LeadImport, $request->file('file')->store('temp'));

        $leads = Lead::where('lead_source_id','=',null)->get();

        foreach ($leads as $lead)
        {
            $lead->update([
               'lead_source_id' => 1
            ]);
        }

        return response()->json("successfully");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leads = Lead::with(['country','city','interestingLevel','leadSources','leadCourses','leadDiplomas','leadFile','leadsFollowup',
            'leadActivities'=>function($q){
            $q->with(['subject','employees']);
        }])->where([
            ['is_client',0],
            ['lead_type',0],
            ['black_list',0],
        ])->get();

        return response()->json($leads);
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
            'name_en' => 'required|string|max:150',
            'name_ar' => 'nullable|string|max:150',
            'registration_remark' => 'nullable|string|max:250',
            'mobile' => 'required|unique:leads,mobile',
            'phone' => 'nullable|unique:leads,phone',
            'email' => 'required|string|email|max:255|unique:leads,email',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'interesting_level_id' => 'required|exists:interesting_levels,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'Job_title' => 'nullable|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'birth_day' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $request_data = $request->all();
        $lead = Lead::create($request_data);

        // file upload

        if($request->hasFile('file'))
        {
            $img = $request->file('file');
            $ext = $img->getClientOriginalExtension();
            $image_name = $lead->id . "-". $request->file_name . ".$ext";
            $img->move( public_path('uploads/leads/') , $image_name);

            LeadFile::create([
                'name'=>$request->file_name,
                'type'=>$ext,
                'file'=>$image_name,
                'lead_id'=>$lead->id,
            ]);
        }

        //create courses lead

        if ($request->courses)
        {
            $courses = $request->courses;

            foreach ($courses as $course)
            {
                LeadCourse::create([
                    'course_id' =>$course['course_id'],
                    'lead_id' =>$lead->id,
                    'category_id'=>$course['category_id'],
                    'vendor_id'=>$course['vendor_id'],
                ]);
            }
        }

        //create diplomas lead
        if ($request->diplomas)
        {
            $diplomas = $request->diplomas;
            foreach ($diplomas as $diploma)
            {
                LeadDiploma::create([
                    'diploma_id' =>$diploma['diploma_id'],
                    'lead_id' =>$lead->id,
                    'category_id'=>$diploma['category_id'],
                    'vendor_id'=>$diploma['vendor_id'],
                ]);
            }
        }

        return response()->json($lead);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lead = Lead::with(['country','city','interestingLevel','leadSources','leadCourses','leadDiplomas','leadFile'])
            ->findOrFail($id);
        return response()->json($lead);
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
            'name_en' => 'required|string|max:150',
            'name_ar' => 'nullable|string|max:150',
            'registration_remark' => 'nullable|string|max:250',
            'mobile' => 'required|unique:leads,mobile' . ($id ? ",$id" : ''),
            'phone' => 'nullable|unique:leads,phone' . ($id ? ",$id" : ''),
            'email' => 'required|string|email|max:255|unique:leads,email' . ($id ? ",$id" : ''),
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'interesting_level_id' => 'required|exists:interesting_levels,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'Job_title' => 'nullable|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'birth_day' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $lead = Lead::findOrFail($id);

        $lead->update($request->all());


        // image upload
        if ($request->image != "null" || $request->image != null) {
            if ($request->hasFile('image')) {
                $img_name = $lead->leadFile->name;

                if ($img_name != null ) {
                    unlink(public_path('uploads/leads/') . $img_name);
                }

                $img = $request->file('image');
                $ext = $img->getClientOriginalExtension();
                $image_name = "employee-image-" . uniqid() . ".$ext";
                $img->move(public_path('uploads/leads/'), $image_name);

                $lead->leadFile()->update([
                    'name'=>$request->file_name,
                    'type'=>$ext,
                    'file'=>$image_name,
                ]);
            }
        }

        //update courses lead

        if ($request->courses)
        {
            $courses = $request->courses;
            $oldLeadCourses = LeadCourse::where('lead_id','=',$id)->get();

            foreach ($oldLeadCourses as $oldLeadCourse)
            {
                $oldLead = LeadCourse::findOrFail($oldLeadCourse->id);
                $oldLead->delete();
            }

            foreach ($courses as $course)
            {
                LeadCourse::create([
                    'course_id' =>$course['course_id'],
                    'lead_id' =>$lead->id,
                    'category_id'=>$course['category_id'],
                    'vendor_id'=>$course['vendor_id'],
                ]);
            }
        }

        //update diplomas lead
        if ($request->diplomas)
        {
            $diplomas = $request->diplomas;

            $oldLeadDiplomas = LeadDiploma::where('lead_id','=',$id)->get();

            foreach ($oldLeadDiplomas as $oldLeadDiploma)
            {
                $oldLead = LeadDiploma::findOrFail($oldLeadDiploma->id);
                $oldLead->delete();
            }

            foreach ($diplomas as $diploma)
            {
                LeadDiploma::create([
                    'diploma_id' =>$diploma['diploma_id'],
                    'lead_id' =>$lead->id,
                    'category_id'=>$diploma['category_id'],
                    'vendor_id'=>$diploma['vendor_id'],
                ]);
            }
        }

        return response()->json($lead);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();
        return response()->json('deleted successfully');
    }

    /**
     * Moving lead to another Employee.
     */
    public function movingLeadToAnotherEmployee(Request $request,$id)
    {
        $leads = Lead::findOrFail($id);
        $leads->update([
            'employee_id' => $request->employee_id,
            'add_list' => 0
        ]);

        return response()->json('moving successfully');
    }

    /**
     * get 10 lead to employee.
     */
    public function getTenLeadToEmployee($id)
    {
        $leadEmployees = Lead::where([
            ['employee_id','=',$id],
            ['is_client','=',0],
            ['add_placement','=',0],
            ['add_interview_sales','=',0],
            ['add_interview','=',0],
            ['add_course_sales','=',0],
            ['add_selta','=',0],
            ['add_list','=',0],
            ['black_list',0],
        ])->get();

        if (count($leadEmployees) == 0)
        {
            $leads = Lead::where([
                ['employee_id','=',null],
                ['is_client','=',0],
                ['add_placement','=',0],
                ['add_interview_sales','=',0],
                ['add_interview','=',0],
                ['add_course_sales','=',0],
                ['add_selta','=',0],
                ['add_list','=',0],
                ['lead_type','=',0],
                ['black_list',0],
            ])->get();

            if (count($leads) == 0){
                return response()->json("sorry no leads now",422);
            }

            if (count($leads) >= 10){
                $leads = Lead::where([
                    ['employee_id','=',null],
                    ['is_client','=',0],
                    ['add_placement','=',0],
                    ['add_interview_sales','=',0],
                    ['add_interview','=',0],
                    ['add_course_sales','=',0],
                    ['add_selta','=',0],
                    ['add_list','=',0],
                    ['lead_type','=',0],
                    ['black_list',0],
                ])->get()->random(10);

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
     * get leads by employee id
     */

    public function getLeadsEmployee($id)
    {
        $leads = Lead::with(['country','city','interestingLevel','leadSources','leadCourses','leadDiplomas','leadFile','leadsFollowup',
            'leadActivities'=>function($q){
            $q->with(['subject','employees']);
        }])->where([
            ['employee_id','=',$id],
            ['is_client','=',0],
            ['add_placement','=',0],
            ['add_interview_sales','=',0],
            ['add_interview','=',0],
            ['add_course_sales','=',0],
            ['add_selta','=',0],
            ['add_list','=',0],
            ['black_list',0],
        ])->get();

        return response()->json($leads);
    }

    /**
     * get leads open task by employee id
     */

    public function getLeadsOpenTaskEmployee($id)
    {
        $leads = Lead::with(['country','city','interestingLevel','leadSources','leadCourses','leadDiplomas','leadFile','leadsFollowup',
            'leadActivities'=>function($q){
            $q->with(['subject','employees']);
        }])->where([
            ['employee_id','=',$id],
            ['is_client','=',0],
            ['add_placement','=',0],
            ['add_interview_sales','=',0],
            ['add_interview','=',0],
            ['add_course_sales','=',0],
            ['add_selta','=',0],
            ['add_list','=',1],
            ['black_list',0],
        ])->get();

        return response()->json($leads);
    }

    /**
     * get leads by employee id to Register track
     */

    public function getLeadsRegisterTrackEmployee($id)
    {
        $leads = Lead::with(['country','city','interestingLevel','leadSources','leadCourses','leadDiplomas','leadFile'])->where([
            ['employee_id','=',$id],
            ['is_client','=',0],
            ['black_list','=',0],
        ])->get();

        return response()->json($leads);
    }

    /**
     * get clients by employee id
     */

    public function getClintEmployee($id)
    {
        $leads = Lead::with(['country','city','interestingLevel','leadSources','leadCourses','leadDiplomas','leadFile'])->where([
            ['employee_id','=',$id],
            ['is_client','=',1],
            ['black_list','=',0],
        ])->get();

        return response()->json($leads);
    }

    /**
     * get clients
     */

    public function getClint()
    {
        $leads = Lead::where([
            ['is_client','=',1],
            ['black_list','=',0],
        ])->get();

        foreach($leads as $lead)
        {
            $lead->country;
            $lead->city;
            $lead->employee;
            $lead->has_account = 0;

            if($lead->user != null)
            {
                $lead->has_account = 1;
            }
        }

        return response()->json($leads);
    }


}
