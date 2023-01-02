<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $employees = Employee::where('admin','=',0)->with(['bankAccount','user','department','job','branch','commissions'])->get();
        foreach ($employees as $employee)
        {
            if ($employee->has_account != null)
            {
                $employee->role_id = $employee->user->roles[0]->id;
            }else{
                $employee->role_id = null;
            }
            $employee->noAction = 0;
//            if (count($instructor->traningCategories) > 0 || count($instructor->traningDiplomas) > 0 || count($instructor->traningCourses) > 0 || count($instructor->interview) > 0 || $instructor->courseTrack > 0 || count($instructor->courseTrackSchedule) > 0 || $instructor->diplomaTrack > 0 || count($instructor->diplomaTrackSchedule) > 0 ) {
//                $instructor->noAction = 1;
//            }
        }
        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_en' => 'required|string|max:100',
            'name_ar' => 'required|string|max:100',
            'mobile' => 'required|unique:employees,mobile',
            'mobile_two' => 'nullable|unique:employees,mobile_two',
            'email_two' => 'required|string|email|max:255|unique:employees,email_two',
            'department_id' => 'required|exists:departments,id',
            'job_id' => 'required|exists:jobs,id',
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'birth_date' => 'required|date',
            'hiring_date' => 'required|date',
            'date_of_resignation' => 'nullable|date',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|required|max:10000',
            'has_account' => 'required',
            'insurance_number' => 'nullable|numeric',
            'ID_number' => 'nullable|numeric',
            'military_id' => 'required|exists:militaries,id',
            'relation_status' => 'required|in:married,single',
            'name_of_company_insurance' => 'nullable|string|max:100',
            'salary' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'branch_id' => 'required|exists:branches,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        //replase boolean
        $tempData = str_replace("", "",$request->has_account);

        if($tempData == true)
        {
            $has_account = 1;
        }else
        {
            $has_account = 0 ;
        }

        //crete account
        if ($has_account ==1){

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json($errors,422);
            }

            $user = User::create([
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'type' => 'employee',
            ]);
            $user->attachRole($request->role_id);
            $user_id =$user->id;
        }else{
            $user_id = null ;
        }

        // image upload

        if($request->hasFile('image'))
        {
            $img = $request->file('image');
            $ext = $img->getClientOriginalExtension();
            $image_name = "employee-image-". uniqid() . ".$ext";
            $img->move( public_path('uploads/employee/') , $image_name);
        }

        if ($request->hasFile('pdf')){
            $img = $request->file('pdf');
            $ext = $img->getClientOriginalExtension();
            $pdf_name = "employee-pdf-" . uniqid() . ".$ext";
            $img->move(public_path('uploads/employee/'), $pdf_name);
        }

        $employee = Employee::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'mobile' => $request->mobile,
            'mobile_two' => $request->mobile_two,
            'email' => $request->email,
            'email_two' => $request->email_two,
            'job_id' => $request->job_id,
            'department_id' => $request->department_id,
            'pdf' => $pdf_name,
            'hiring_date' => $request->hiring_date,
            'date_of_resignation' => $request->date_of_resignation,
            'insurance_number' => $request->insurance_number,
            'ID_number' => $request->ID_number,
            'birth_date' => $request->birth_date,
            'military_id' => $request->military_id,
            'relation_status' => $request->relation_status,
            'name_of_company_insurance' => $request->name_of_company_insurance,
            'salary' => $request->salary,
            'img' => $image_name,
            'user_id' => $user_id,
            'branch_id' => $request->branch_id,
            'has_account' => $has_account,
        ]);

        if ($request->bank_id != "undefined" && $request->IBAN != "undefined" && $request->account_number != "undefined") {
            BankAccount::create([
                'bank_id' => $request->bank_id,
                'employee_id' => $employee->id,
                'IBAN' => $request->IBAN,
                'account_number' => $request->account_number,
                'branch_name' => $request->branch_name,
            ]);
        }

        $employee->commissions()->syncWithoutDetaching($request->commissions);

        return response()->json('created success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::with(['bankAccount','user','department','job','branch','commissions'])->findOrFail($id);
        return response()->json($employee);
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
            'name_ar' => 'required|string|max:100',
            'mobile' => 'required|unique:employees,mobile'. ($id ? ",$id" : ''),
            'mobile_two' => 'nullable|unique:employees,mobile_two'. ($id ? ",$id" : ''),
            'email_two' => 'required|string|email|max:255|unique:employees,email_two'. ($id ? ",$id" : ''),
            'department_id' => 'required|exists:departments,id',
            'job_id' => 'required|exists:jobs,id',
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'birth_date' => 'required|date',
            'hiring_date' => 'required|date',
            'date_of_resignation' => 'nullable|date',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|required|max:10000',
            'has_account' => 'required',
            'insurance_number' => 'nullable|numeric',
            'ID_number' => 'nullable|numeric',
            'military_id' => 'required|exists:militaries,id',
            'relation_status' => 'required|in:married,single',
            'name_of_company_insurance' => 'nullable|string|max:100',
            'salary' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'branch_id' => 'required|exists:branches,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $employee =Employee::findOrFail($id);
        $img_name = $employee->img;
        $pdf_name = $employee->pdf;

        $request_data = $request->all();

        // image upload
        if ($request->image != "null" || $request->image != null) {
            if ($request->hasFile('image')) {
                if (File::exists('uploads/employee/' . $img_name)  && $img_name != 'admin00100.png') {
                    unlink(public_path('uploads/employee/') . $img_name);
                }

                $img = $request->file('image');
                $ext = $img->getClientOriginalExtension();
                $image_name = "employee-image-" . uniqid() . ".$ext";
                $img->move(public_path('uploads/employee/'), $image_name);
                $request_data['img'] = $image_name;
            }
        }else{
            $request_data['img'] = $img_name;
        }

        if ($request->image != "null" || $request->image != null) {
            if ($request->hasFile('pdf')) {
                if (File::exists('uploads/employee/' . $pdf_name)  && $pdf_name != null) {
                    unlink(public_path('uploads/employee/') . $pdf_name);
                }

                $img = $request->file('pdf');
                $ext = $img->getClientOriginalExtension();
                $pdf_name = "employee-pdf-" . uniqid() . ".$ext";
                $img->move(public_path('uploads/employee/'), $pdf_name);
            }
        }else{
            $request_data['pdf'] = $pdf_name;
        }

        $employee->update($request_data);

        $bankaccount = BankAccount::where('employee_id','=',$id)->first();

        if ($bankaccount != null)
        {
            if ($request->bank_id != "undefined" && $request->IBAN != "undefined" && $request->account_number != "undefined") {

                $bankaccount->update([
                    'bank_id' => $request->bank_id,
                    'IBAN' => $request->IBAN,
                    'account_number' => $request->account_number,
                    'branch_name' => $request->branch_name,
                ]);
            }

        }else{

            if ($request->bank_id != "undefined" && $request->IBAN != "undefined" && $request->account_number != "undefined") {

                BankAccount::create([
                    'bank_id' => $request->bank_id,
                    'employee_id' => $id,
                    'IBAN' => $request->IBAN,
                    'account_number' => $request->account_number,
                    'branch_name' => $request->branch_name,
                ]);
            }
        }

        $employee->commissions()->sync($request->commissions);

        return response()->json('updated success');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $employee =Employee::findOrFail($id);
        $employee->delete();
        return response()->json("deleted successfully");

    }

    /**
     * Activation employee.
     */

    public function activationEmployee( $id)
    {
        $employee =Employee::findOrFail($id);
        if ($employee->active == 1){

            $employee->update([
                'active' => 0,
            ]);

        }else{

            $employee->update([
                'active' => 1,
            ]);
        }
        return response()->json($employee);
    }

    /**
     * get Active employees.
     */

    public function getActiveEmployee()
    {

        $employee = Employee::where([
            ['active',1],
            ['admin',0],
        ])->get();
        return response()->json($employee);
    }

    /**
     * get des Active employees.
     */
    public function getDeactivateEmployee()
    {
        $employee = Employee::where([
            ['active',0],
            ['admin',0],
        ])->get();
        return response()->json($employee);
    }

    /**
     * get Create Account Employee.
     */
    public function createAccountEmployee(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $employee = Employee::findOrFail($id);
        if ($employee->has_account != 0){
            return response()->json('this Employee has account',422);
        }
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->get('password')),
            'type' => 'employee'
        ]);

        $user->attachRole($request->role_id);

        $employee->update([
            'has_account' => 1,
            'user_id' => $user->id
        ]);

        return response()->json($employee);

    }

    /**
     * change role.
     */
    public function changeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $user = User::findOrFail($request->user_id);
        $user->syncRoles([$request->role_id]);
        return response()->json('the role is changed');
    }

}
