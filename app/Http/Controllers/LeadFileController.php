<?php

namespace App\Http\Controllers;

use App\Models\LeadFile;
use Illuminate\Http\Request;

class LeadFileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $leads = LeadFile::where('lead_id',$request->lead_id)->get();
        foreach ($leads as $lead){
            $lead->delete();
        }
        // file upload
        if($request->hasFile('file'))
        {
            $img = $request->file('file');
            $ext = $img->getClientOriginalExtension();
            $image_name = $request->lead_id . "-". $request->file_name . ".$ext";
            $img->move( public_path('uploads/leads/') , $image_name);

            LeadFile::create([
                'name'=>$request->file_name,
                'type'=>$ext,
                'file'=>$image_name,
                'lead_id'=>$request->lead_id,
            ]);
        }

        return response()->json("successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lead = LeadFile::where('lead_id',$id)->first();
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
        $lead = LeadFile::findOrFail($id);
        $lead->delete();
        return response()->json('deleted successfully');
    }
}
