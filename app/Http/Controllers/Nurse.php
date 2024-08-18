<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Medical_examination;
use App\Models\Nurse as ModelsNurse;
use App\Models\Blood_pressure;
use App\Models\Patient;
use App\Models\Sugar_blood;
use App\Models\Nurse_section;
use App\Models\Drugs_supplies;
use App\Models\Report_patient_doctor;
use App\Models\Request_medical_supplies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class Nurse extends Controller
{
public function view_profile(){
$nurse=Auth::guard('nurse')->user();
$nurse_profile=ModelsNurse::with([
    'section'
])->find($nurse->id);
return response()->json(['message'=>'nurse profile','profile'=>$nurse_profile]);
}
public function update_profile(Request $request, StoreUserRequest $requestuser){
$nurse=Auth::guard('nurse')->user();
ModelsNurse::where('id','=',$nurse->id)->update($request->only(
    'name',
    // 'image',
    'about_him',
    'birthdate',
    'phoneNumber',
    // 'userName',
    'password',
    'address'
));
if($request->hasFile('image')){
    $nurse=ModelsNurse::where('id','=',$nurse->id)->first();
    $nurse->image=$this->uploadeImage($request);
    $nurse->save();
}
if($request['userName']){
    ModelsNurse::where('id','=',$nurse->id)->first()->update($requestuser->only('userName'));
}
if($request['password']){
    $validate=Validator::make($request->all(),['password'=>'required|min:8']);
    if($validate->fails()){
        return response()->json(['message'=>$validate->errors()]);
    }
    $nurse=ModelsNurse::where('id','=',$nurse->id)->first();
    $nurse->password=Hash::make($request->password);
    $nurse->save();
}}
public function make_examination(Request $request,Medical_examination $medical,Patient $patient){
$medical_examination=Medical_examination::where('id','=',$medical->id)->first();
if($medical_examination->name_examination == 'Blood pressure'){
    $medical=Medical_examination::where('name_examination','=','Blood pressure')->first();
    $validate2=Validator::make($request->all(),[
     'systolic_blood_pressure'=>'required',
    'diastolic_blood_pressure'=>'required',
    'number_pulses'=>'required',
    'result'=>'required',
    'date'=>'required|date'
    ]);
    if($validate2->fails()){
        return response()->json(['message'=>$validate2->errors()]);
    }
    $blood=Blood_pressure::create([
        'medical_examination_id'=>$medical->id,
        'patient_id'=>$patient,
        // 'id_doctor'=>$doctor->id,
        'id_nurse'=>Auth::guard('nurse')->user()->id,
        'date'=>$request->date,
        'systolic_blood_pressure'=>$request->systolic_blood_pressure,
        'diastolic_blood_pressure'=>$request->diastolic_blood_pressure,
        'number_pulses'=>$request->number_pulses,
        'result'=>$request->result
    ]);
    return response()->json(['message'=>'blood examination for patient','blood examination'=>$blood]);
}
if($medical_examination->name_examination == 'Sugar blood'){
    $medical=Medical_examination::where('name_examination','=','Sugar blood')->first();
    $validate2=Validator::make($request->all(),[
        'date'=>'required|date',
        'blood_sugar'=>'required',
        'result'=>'required'
       ]);
       if($validate2->fails()){
           return response()->json(['message'=>$validate2->errors()]);
       }
       $sugar=Sugar_blood::create([
        'medical_examination_id'=>$medical->id,
        'patient_id'=>$patient->id,
        'id_nurse'=>Auth::guard('nurse')->user()->id,
        'date'=>$request->date,
        'blood_sugar'=>$request->blood_sugar,
        'result'=>$request->result
       ]);
       return response()->json(['message'=>'sugar examination for patient','sugar examination'=>$medical]);
}}
public function Request_supplies( Request $request ,Patient $patient){
    $validate=Validator::make($request->all(),[
        'drugs_supplies_id'=>'required',
        'quentity'=>'required',
        'date'=>'required|date'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $nurse=Auth::guard('nurse')->user();
        $operationId=Nurse_section::where('nurses_id','=',$nurse->id)->first()->id;
        $drugs=Drugs_supplies::where('id','=',$request->drugs_supplies_id)->first();
        if($drugs){
            if($drugs->quentity>=$request->quentity){
                $medical_supplies=Request_medical_supplies::create([
                    'nurse_id'=>$nurse->id,
                    'drugs_supplies_id'=>$request->drugs_supplies_id,
                    'quentity'=>$request->quentity,
                    'operation_sections_id'=>$operationId,
                    'patient_id'=>$patient->id,
                    'date'=>$request->date,
                    'status_request'=>false
                    ]);
                    return response()->json(['message'=>'medical supplies that requested','medical supplies'=>$medical_supplies]);
            }
            else{
                return response()->json(['message'=>'quentity not enough']);
            }
        }else{
            return response()->json(['message'=>'drugs not found']);
        }}
        public function update_supplies_requests(){
            
        }
        public function get_drugs_supplies(){
            $drugs=Drugs_supplies::select('name','id')->get();
            $array=[];
            foreach($drugs as $d){
                $name=$d->name;
                $id=$d->id;
                $array=array(
                    'name'=>$name,
                    'id'=>$id);
            }
            return response()->json(['message'=>'all drugs available','drugs'=>$array]);
        }
        ////////use notifications
public function report_patient_doctor(Request $request,Patient $patient){
$nurse=Auth::guard('nurse')->user();
$section=Nurse_section::where('nurses_id','=',$nurse->id)->first();
$validate=Validator::make($request->all(),[
    // 'nurses_id',
    'doctors_id'=>'required',
    'operation_sections_id'=>'required',
    'name_examination'=>'required',
    'id_examination'=>'required',
    'status_patient'=>'required',
    'explanation'=>'required',
    'date'=>'required|date'
]);
if($validate->fails()){
    return response()->json(['message'=>$validate->errors()]);
}
$report=Report_patient_doctor::create([
        'nurses_id'=>$nurse->id,
        'doctors_id'=>$request->doctors_id,
        'patient_id'=>$patient->id,
        'operation_sections_id'=>$section->operation_sections_id,
        'name_examination'=>$request->name_examination,
        'id_examination'=>$request->id_examination,
        'status_patient'=>$request->status_patient,
        'explanation'=>$request->explanation,
        'date'=>$request->date
]);
return response()->json(['message'=>'report patient to doctor','report'=>$report]);
}
public function get_id_examination(Request $request,Patient $patient){
    $array=[];
$name_examination=$request['name_examination'];
if($name_examination=='Blood pressure'){
    $blood=Blood_pressure::where('patient_id','=',$patient->id)->where('id_nurse','=',Auth::guard('nurse')->user())->get();
    return response()->json(['message'=>'all blood pressure examination for patient','blood examination'=>$blood]);
}
if($name_examination=='Sugar blood'){
    $blood=Sugar_blood::where('patient_id','=',$patient->id)->where('id_nurse','=',Auth::guard('nurse')->user())->get();
    return response()->json(['message'=>'all blood sugar examination for patient','blood sugar examination'=>$blood]);
}
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
    return   Storage::url($path);
}
}
