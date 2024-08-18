<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Laboratory as ModelsLaboratory;
use App\Models\Laboratory_section;
use App\Models\Line_queue;
use App\Models\Patient;
use App\Models\Visit_details;
use App\Models\Laboratory_anylysis;
use App\Models\Result_Laboratory_anylysis;
use App\Models\Request_laboratory_analysis;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Laboratory extends Controller
{
    public function profile(){
        $laboratory=Auth::guard('laboratory')->user();
        $laboratory=ModelsLaboratory::with(['Laboratory_section'])->find($laboratory->id);
        return response()->json(['message'=>'laboratory profile','profile'=>$laboratory]);
    }
    public function update_profile(Request $request,StoreUserRequest $requestuser){
        $laboratory=Auth::guard('laboratory')->user()->id;
        $update=ModelsLaboratory::where('id','=',$laboratory)->first()->update($request->all(),[
            'name',
            'birthdate',
            'about_him',
            'phoneNumber',
            'password',
            'email',
            'address'
        ]);
        if($request->hasFile('image')){
           $image_laboratory= ModelsLaboratory::where('id','=',$laboratory)->first();
           $image_laboratory->image=$this->uploadeImage($request);
           $image_laboratory->save();
        }
        if($request['userName']){
            $updateUserName=ModelsLaboratory::where('id','=',$laboratory)->first()->update($requestuser->only('userName'));
        }
        if($request['password']){
            $validate=Validator::make($request->all(),['password'=>'required|min:8']);
            if($validate->fails()){
                return response()->json(['message'=>$validate->errors()]);
            }
            $nurse=ModelsLaboratory::where('id','=',$laboratory)->first();
            $nurse->password=Hash::make($request->password);
            $nurse->save();
        }
        $update=ModelsLaboratory::where('id','=',$laboratory)->first();
return response()->json(['message'=>'laboratory profile updated successfully','update'=>$update]);
    }
public function ectract_queue(){
$laboratory=Auth::guard('laboratory')->user();
$laboratory=ModelsLaboratory::where('id','=',$laboratory->id)->first();
if($laboratory->Laboratory_section()->exists()){
    $laboratory_section=Laboratory_section::where('laboratorys_id','=',$laboratory->id)->where('available','=',true)->first();
    $line_queue=Line_queue::where('section_name','=','Laboratory section')->where('section_id','=',$laboratory_section->id)->orderBy('position','asc')->first();
    if($line_queue){
        $line_queue->delete();
        $patient=Patient::where('id','=',$line_queue->patient_id)->first();
        Line_queue::where('section_id','=',$laboratory_section->id)->where('section_name','=','Laboratory section')->where('position','>',$line_queue->position)->decrement('position');
        $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
        $visit_details->enterTime=Carbon::now();
        $visit_details->save();
        return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
    }
    return response()->json(['message'=>'line queue is empty']);
}}
public function pass_patient(Line_queue $line){
    $laboratory=Auth::guard('laboratory')->user();
    $laboratory=ModelsLaboratory::where('id','=',$laboratory->id)->first();
    if($laboratory->Laboratory_section()->exists()){
        $laboratory_section=Laboratory_section::where('laboratorys_id','=',$laboratory->id)->where('available','=',true)->first();
        $line_queue=Line_queue::where('section_name','=','Laboratory section')->where('section_id','=',$laboratory_section->id)->where('position','=',$line->position+1)->first();
        if($line_queue){
        $line_queue->delete();
        $patient=Patient::where('id','=',$line_queue->patient_id)->first();
        Line_queue::where('section_id','=',$laboratory_section->id)->where('section_name','=','Laboratory section')->where('position','>',$line_queue->position)->decrement('position');
        $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
        $visit_details->enterTime=Carbon::now();
        $visit_details->save();
        Line_queue::create([
            'patient_id'=>$line->patient_id,
            'num_char'=>$line->num_char,
            'position'=>$line->position,
            'section_name'=>$line->section_name,
            'section_id'=>$line->section_id,
            'visit_id'=>$line->visit_id,
            'wating'=>true
        ]);
        Line_queue::where('section_id','=',$laboratory_section->id)->where('section_name','=','Laboratory section')->where('position','>',$line->position)->increment('position');
        return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
        }
        Line_queue::create([
            'patient_id'=>$line->patient_id,
            'num_char'=>$line->num_char,
            'position'=>$line->position,
            'section_name'=>$line->section_name,
            'section_id'=>$line->section_id,
            'visit_id'=>$line->visit_id,
            'wating'=>true
        ]);
        Line_queue::where('section_id','=',$$laboratory_section->id)->where('section_name','=','Laboratory section')->where('position','>',$queue->position)->increment('position');
        return response()->json(['message'=>'dont found other patients']);
    }}
public function show_patient_file(Patient $patient){
    $patientfile=Patient::with([
        'details_visit',
        'doctor_examination',
        'imaging_report',
        'radiology_report',
        'result_laboratory',
        'medical_operation',
        'laboratory_visit',
    ])->find($patient->id);
    return response()->json([
        'patient'=>$patientfile,
        'doctor_examination'=>$patientfile->doctor_examination,
        'imaging_report'=>$patientfile->imaging_report,
        'radiology_report'=>$patientfile->radiology_report,
        'result_laboratory'=>$patientfile->result_laboratory,
        'medical_operation'=>$patientfile->medical_operation,
        'laboratory_visit'=>$patientfile->laboratory_visit
    ]);
}
public function show_requested_anylasis(Patient $patient){
    $array=[];
    $patient_examination=Patient::with(
        ['doctor_examination'])->find($patient->id);
        if($patient_examination->doctor_examination()->exists()){
            foreach($patient_examination->doctor_examination as $examination){
                $analysis_id=$examination->laboratory_analysis;
                foreach($analysis_id as $analyse_id){
                    $laboratory_analysis=Laboratory_anylysis::where('id','=',$analyse_id)->first();
                    if($laboratory_analysis){
                        $array=array('examination'=>$examination,'laboratory analysis'=>$laboratory_analysis);
                    }
                }
            }
            return response()->json('message'=>'requested laboratory for patient','laboratory'=>$array);
        }
        return response()->json(['message'=>'not found any requested analysis']);
}
public function add_laboratory_analysis(Request $request){
    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'type'=>'required',
        'masurement_unit'=>'required',
        'natural_limit'=>'required',
        'price'=>'required'
    ]);
    if($validate->fails()){
        return response()->json(['message'=>$validate->errors()]);
    }
    $laboratory_analysis=Laboratory_anylysis::create([
        'name'=>$request->name,
        'type'=>$request->type,
        'masurement_unit'=>$request->masurement_unit,
        'natural_limit'=>$request->natural_limit,
        'price'=>$request->price
    ]);
    return response()->json(['message'=>'laboratory analysis added successfully','analysis'=>$laboratory_analysis]);
}
public function write_analysis(Request $request,Patient $patient){
$validate=Validator::make($request->all(),[
    // 'laboratorys_id',
    // 'patient_id',
    'laboratory_anylysis_id'=>'required',
    'result_date'=>'required|date',
    'result'=>'required'
]);
if($validate->fails()){
    return response()->json(['message'=>$validate->errors(),404]);
}
$request_analysis=Request_laboratory_analysis::where('patient_id','=',$patient->id)->where('status_request','=',false)->first();
if($request_analysis){
    $result=Result_Laboratory_anylysis::create([
        'laboratorys_id'=>$request->laboratorys_id,
        'patient_id'=>$request->$patient->id,
        'laboratory_anylysis_id'=>$request->laboratory_anylysis_id,
        'result_date'=>$request->result_date,
        'result'=>$request->result,
        'for_operations'=>true
    ]);
    $request_analysis->status_request=true;
    $request_analysis->save();
  return response()->json(['message'=>'result laboratory for patient','result'=>$result]);
}
$result=Result_Laboratory_anylysis::create([
    'laboratorys_id'=>$request->laboratorys_id,
    'patient_id'=>$request->$patient->id,
    'laboratory_anylysis_id'=>$request->laboratory_anylysis_id,
    'result_date'=>$request->result_date,
    'result'=>$request->result,
    'for_operations'=>false
]);
return response()->json(['message'=>'result laboratory for patient','result'=>$result]);
}
public function show_request_from_operations(){
    $requests=Request_laboratory_analysis::where('status_request','=',false)->get();
    return response()->json(['message'=>'all request from operations','requests'=>$requests]);
}
    public function uploadeImage(Request $request)
{
    $validator = Validator::make($request->all(), [
        'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048', // Adding a max size for the image
    ]);
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }
    $image = $request->file('image');
    $name = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
    $path = $image->storeAs('public/images', $name);
    return  Storage::url($path);
}
}
