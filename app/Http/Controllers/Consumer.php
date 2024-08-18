<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Consumer_employee;
use App\Models\Patient;
use App\Models\Patient_graduation;
use App\Models\Consumers;
use App\Models\Accounter;
use App\Models\Medical_operation;
use App\Models\Patient_ready_accounting;
use App\Models\Request_medical_supplies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Consumer extends Controller
{
    public function view_profile(){
        $consumer_embloyee=Auth::guard('consumer_employee')->user();
        $profile=Consumer_employee::where('id','=',$consumer_embloyee->id)->first();
        return response()->json(['message'=>'consumer embloyee profile','profile'=>$profile]);
    }
    public function update_profile(Request $request, StoreUserRequest $requestuser){
        $consumer_embloyee=Auth::guard('consumer_employee')->user();
        Consumer_employee::where('id','=',$consumer_embloyee->id)->update($request->only(
            'name',
            // 'image',
            'birthdate',
            'about_him',
            'phoneNumber',
            // 'userName',
            // 'password',
            'email',
            'operation_sections_id'
        ));
        if($request->hasFile('image')){
        $consumer=Consumer_employee::where('id','=',$consumer_embloyee->id)->first();
        $consumer->image=$this->uploadeImage($request);
        $consumer->save();
        }
        if($request['userName']){
        Consumer_employee::where('id','=',$consumer_embloyee->id)->update($requestuser->only('userName'));
        }
        if($request->password){
            $validate=Validator::make($request->all(),['password'=>'required|min:8']);
            if($validate->fails()){
                return response()->json(['message'=>$validate->errors()]);
            }
            $consumer=Consumer_employee::where('id','=',$consumer_embloyee->id)->first();
            $consumer->password=Hash::make($request->password);
            $consumer->save();
        }
        $consumer=Consumer_employee::where('id','=',$consumer_embloyee->id)->first();
        return response()->json(['message'=>'profile updated successfully','updeted'=>$consumer]);
    }
//all patient graduation from operation section
public function show_all_patient(){
$patient_graduation=Patient_graduation::where('calc_consumers','=',false)->get();
$patients=[];
foreach($patient_graduation as $graduation){
    $patient=Patient::where('id','=',$graduation->patient_id)->first();
    $patients=array(
        'patient'=>$patient,
        'graduation'=>$graduation
    );}
return response()->json(['message'=>'all patients  that graduation','patients'=>$patients]);
}// all supplies for all operations
public function get_requests_supplies_patient(Patient $patient){
    $supplies_operation=[];
$patient_requests=Patient::with(['Requests_supplies','consumers','medical_operation'])->find($patient->id);
if($patient_requests->medical_operation()->exists()){
    foreach($patient_requests->medical_operation as $medical_operation){
        $graduation=Patient_graduation::where('medical_operation_id','=',$medical_operation->id)->where('calc_consumers','=',false)->get();
        if(!$graduation->isEmpty()){
            foreach($graduation as $gra){
                $id_operation=$gra->medical_operation_id;
                $operation=Medical_operation::where('id','=',$id_operation)->first();
                $request_suppllies=Request_medical_supplies::where('medical_operation_id','=',$id_operation)->get();
                $supplies_operation=array('request supplies'=>$request_suppllies,
                    'medical operation'=>$operation);
        }
        return response()->json(['message'=>'all supplies request for patient','suppllies request'=>$supplies_operation]);
        }
        }
       return response()->json(['message'=>'patient dont have a request suppllies for adding to consumers']);
    }
    return response()->json(['message'=>'patient dont have any medical operations']);
}//all
public function add_requests_to_consumers(Patient $patient,Medical_operation $operation){
$consumers=[];
$consumers_id=[];

$graduation=Patient_graduation::where('medical_operation_id','=',$operation)->where('calc_consumers','=',false)->first();
if($graduation){
    $requests=Request_medical_supplies::where('patient_id','=',$patient)->where('medical_operation_id','=',$operation)->get();
    if(!$requests->isEmpty()){
        foreach($requests as $request){
            $consumer=Consumers::create([
                'patient_id'=>$patient->id,
                'drugs_supplies_id'=>$request->drugs_supplies_id,
                'quentity'=>$request->quentity,
                'consumer_employee_id'=>Auth::guard('consumer_employee')->user()->id,
                'request_medical_supplies_id'=>$request->id,
                'medical_operation_id'=>$operation->id
            ]);
            $consumers=array('consumer'=>$consumer);
            $consumers_id=array($consumer->id);
        }
        $graduation=Patient_graduation::where('medical_operation_id','=',$operation->id)->where('patient_id','=',$patient)->first();
        $graduation->calc_consumers=true;
        $graduation->save();
        $accounter=Accounter::where('operation_sections_id','=',Auth::guard('consumer_employee')->user()->operation_sections_id)->first();
        $patient_ready=Patient_ready_accounting::create([
            'patient_id'=>$patient->id,
            'consumer_employee_id'=>Auth::guard('consumer_employee')->user()->id ,
            'accounter_id'=>$accounter->id,
            'medical_operation_id'=>$operation->id,
            'consumers_id'=>$consumers_id,
            'accounting'=>false
        ]);
        //use notification
    return response()->json(['message'=>'request supplies added to consumers list','consumers'=>$consumers]);
    }
    return response()->json(['message'=>'dont have any supplies for adding to consumers']);
    }
    return response()->json(['message'=>'dont have any supplies for adding to consumers']);
}


public function get_Medical_operation(Patient $patient){
    $array=[];
   $graduations=Patient_graduation::where('patient_id','=',$patient->id)->where('calc_consumers','=',false)->get();
   if(!$graduations->isEmpty()){
    foreach($graduations as $graduation){
        $medical_operation_id=$graduation->medical_operation_id;
        $medical_operation=Medical_operation::where('id','=',$medical_operation_id)->first();
    $array=array('id_medical_operation'=>$medical_operation_id,'medical_operation'=>$medical_operation);
       }
   return response()->json(['message'=>'all medical operation for patient','medical operation'=>$array]);
   }
   return response()->json(['message'=>'patient dont have any medical operation',404]);
}
public function get_consumers_patient(Patient $patient,Medical_operation $operation){
    $patient_consumers=Patient::with(['consumers'])->find($patient->id);
    $consumers=Consumers::where('medical_operation_id','=',$operation->id)->where('patient_id','=',$patient->id)->get();
    if(!$consumers->isEmpty()){
    return response()->json(['message'=>'all consumers for patient on medical operation','consumers'=>$consumers,'operation'=>$operation]);
    }
    return response()->json(['message'=>'consumers not found',404]);
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
