<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreUserRequest;
use App\Models\Doctor;
use App\Models\Laboratory;
use App\Models\Laboratory_section;
use App\Models\Line_queue;
use App\Models\Magnetic_resonnance_imaging;
use App\Models\Medical_clinic;
use App\Models\Operation_rooms;
use App\Models\Operation_section;
use App\Models\Patient;
use App\Models\Radiation_section;
use App\Models\Reseption_employee as ModelsReseption_employee;
use App\Models\Stay_operation_rooms;
use App\Models\Visit_details;
use App\Models\Visit_laboratory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;
class Reseption_employee extends Controller
{
public function update_employee_reseption(Request $request,StoreUserRequest $requestuser){
    $employee=Auth::guard('reseption_employee')->user();
    ModelsReseption_employee::where('id','=',$employee->id)->first()->update($request->only
    ('name',
    'phoneNumber',
    'birthdate',
    'address',
    'password',
    'section_name',
    'email'));
    if($request['userName']){
        ModelsReseption_employee::where('id','=',$employee->id)->first()->update($requestuser->only('userName'));
    }
    if($request->hasFile('image')){
   $imageUpdate=ModelsReseption_employee::where('id','=',$employee->id)->first();
        $imageUpdate->image=$this->uploadeImage($request);
        $imageUpdate->save();
    }
    if($request['password']){
        $validate=Validator::make($request->all(),['password'=>'required|min:8']);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $nurse=ModelsReseption_employee::where('id','=',$employee->id)->first();
        $nurse->password=Hash::make($request->password);
        $nurse->save();
    }
    $update=ModelsReseption_employee::where('id','=',$employee->id)->first();
    return response()->json(['message'=>'reseption employee update successfuly','update'=>$update]);
}
public function profile(){
    $reseption=Auth::guard('reseption_employee')->user();
    return response()->json(['message'=>'reseption employee profile','profile'=>$reseption]);
}
public function Register_patients_visit(Request $request){
    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'gender'=>'required',
        'birthdate'=>'required',
        'birth_address'=>'required',
        'cur_address'=>'required',
        'phoneNumber'=>'required',
        'phoneNumber_near'=>'required',
        'enterDate'=>'required|date',
        'typeVisit'=>'required',
        'section_id'=>'required'
    ]);
if($validate->fails()){
    return response()->json(['message'=>$validate->errors(),400]);
}
if($request->has('info_health_insurance','NumberDocument_ins','details_covering_ins','condition_ins','contact_info_companany')){
    $patient=Patient::create([
        'id_file'=>rand(100000,999999),
        'name'=>$request->name,
        'gender'=>$request->gender,
        'birthdate'=>$request->birthdate,
        'birth_address'=>$request->birth_address,
        'cur_address'=>$request->cur_address,
        'phoneNumber'=>$request->phoneNumber,
        'phoneNumber_near'=>$request->phoneNumber_near,
        'info_health_insurance'=>$request->info_health_insurance,
        'NumberDocument_ins'=>$request->NumberDocument_ins,
        'details_covering_ins'=>$request->details_covering_ins,
        'condition_ins'=>$request->condition_ins,
        'contact_info_companany'=>$request->contact_info_companany,
    ]);
}else{
    $patient=Patient::create([
        'id_file'=>rand(100000,999999),
        'name'=>$request->name,
        'gender'=>$request->gender,
        'birthdate'=>$request->birthdate,
        'birth_address'=>$request->birth_address,
        'cur_address'=>$request->cur_address,
        'phoneNumber'=>$request->phoneNumber,
        'phoneNumber_near'=>$request->phoneNumber_near,
    ]);
}
//add to other table
if(Auth::guard('reseption_employee')->user()->section_name=='Laboratory section'){
    $visit=Visit_laboratory::create([
        'patient_id'=>$patient->id,
        'section_name'=>Auth::guard('reseption_employee')->user()->section_name,
        'laboratorys_id'=>$request->laboratorys_id,
        'enterDate'=>$request->enterDate,
        'typeVisit'=>$request->typeVisit,
        'section_id'=>$request->section_id
    ]);
}else{
    $visit=Visit_details::create([
    'patient_id'=>$patient->id,
    'section_name'=>Auth::guard('reseption_employee')->user()->section_name,
    'doctors_id'=>$request->doctors_id,
    'enterDate'=>$request->enterDate,
    'typeVisit'=>$request->typeVisit,
    'section_id'=>$request->section_id
]);}

if(Line_queue::where('section_name','=',Auth::guard('reseption_employee')->user()->section_name)->get()->isEmpty()){
    $line_queue=Line_queue::create([
        'patient_id'=>$patient->id,
        'num_char'=>$this->generateRandomString(),
        'position'=>1,
        'section_id'=>$request->section_id,
        'section_name'=>Auth::guard('reseption_employee')->user()->section_name,
        'visit_id'=>$visit->id
    ]);
}else
    {
        $line_queue=Line_queue::create([
        'patient_id'=>$patient->id,
        'num_char'=>$this->generateRandomString(),
        'position'=>Line_queue::max('position')+1,
        'section_id'=>$request->section_id,
        'section_name'=>Auth::guard('reseption_employee')->user()->section_name,
        'visit_id'=>$visit->id

    ]);}
return response()->json(['message'=>'patient file added successfully','patient'=>$patient,'visit'=>$visit,'queue'=>$line_queue]);
}
public function search_file(Request $request){
   $name=$request->name;
   $fileN=$request->id_file;
   $query=Patient::query();
   if($request->has('id_file'))
   $query->where('id_file','=',$fileN);
if($request->has('name'))
$query->where('name','=',$name);
 if($request->has('phoneNumber'))
$query->where('phoneNumber','=',$request->phoneNumber);
$result=$query->get();
if($result->isEmpty()){
    return response()->json(['message'=>'not found patient',404]);
}
return response()->json(['message'=>'patient files','files'=>$result]);
}
public function show_patient_file(Patient $patient){
    $patientfile=Patient::with([
        'details_visit',
        'doctor_examination',
        'imaging_report',
        'radiology_report',
        'result_laboratory',
        'medical_operation',
    ])->find($patient->id);
    return response()->json([
        'patient'=>$patientfile,
        'doctor_examination'=>$patientfile->doctor_examination,
        'imaging_report'=>$patientfile->imaging_report,
        'radiology_report'=>$patientfile->radiology_report,
        'result_laboratory'=>$patientfile->result_laboratory,
        'medical_operation'=>$patientfile->medical_operation
    ]);
}
 public function add_patient_visit(Request $request,Patient $patient){
    $validate=Validator::make($request->all(),[
        'enterDate'=>'required|date',
        'typeVisit'=>'required',
        'section_id'=>'required'
    ]);
    //add to other table
    if(Auth::guard('reseption_employee')->user()->section_name=='Laboratory section'){
        $visit=Visit_laboratory::create([
            'patient_id'=>$patient->id,
            'section_name'=>Auth::guard('reseption_employee')->user()->section_name,
            'laboratorys_id'=>$request->laboratorys_id,
            'enterDate'=>$request->enterDate,
            'typeVisit'=>$request->typeVisit,
            'section_id'=>$request->section_id
        ]);
    }else{
         $visit=Visit_details::create([
        'patient_id'=>$patient->id,
        'doctors_id'=>$request->doctors_id,
        'enterDate'=>$request->enterDate,
        'typeVisit'=>$request->typeVisit,
        'section_name'=>Auth::guard('reseption_employee')->user()->section_name,
        'section_id'=>$request->section_id
    ]);}
    $line_queue=Line_queue::create([
        'patient_id'=>$patient->id,
        'num_char'=>$this->generateRandomString(),
        'position'=>Line_queue::max('position')+1,
        'section_id'=>$request->section_id,
        'section_name'=>Auth::guard('reseption_employee')->user()->section_name,
        'visit_id'=>$visit->id
    ]);
return response()->json(['message'=>'visit added successfully','visit'=>$visit]);
}
public function getDoctor(){
    $doctor=Doctor::select('name','id')->get();
$array=[];
foreach($doctor as $d){
    $name=$d->name;
    $id=$d->id;
    $array=array(
        'id'=>$id,
        'name'=>$name
    );
}
    return response()->json(['doctor name and id'=>$array]);
}
public function get_section_id(){
    $section_name=Auth::guard('reseption_employee')->user()->section_name;
    if($section_name=='Medical clinic'){
        $clinic=Medical_clinic::select('name','id')->get();
        $array=[];
        foreach($clinic as $d){
            $name=$d->name;
            $id=$d->id;
            $array=array(
                'id'=>$id,
                'name'=>$name
            );
        }
            return response()->json(['clinic name and id'=>$array]);
    }
    if($section_name=='Operations section'){
    $array=[];
$operationName= Operation_section::select('Section_name','id')->get();
 foreach($operationName as $op){
     $id=$op->id;
     $name=$op->Section_name;
     $array=array(
         'id'=>$id,
         'name'=>$name
     );
 }
 return  Response()->json(['operation names and id'=>$array]);
    }
    if($section_name=='Radiation section'){
        $array=[];
        $operationName= Radiation_section::select('name','id')->get();
         foreach($operationName as $op){
             $id=$op->id;
             $name=$op->name;
             $array=array(
                 'id'=>$id,
                 'name'=>$name
             );
         }
         return  Response()->json(['operation names and id'=>$array]);
    }
    if($section_name=='Magnitic section'){
        $array=[];
        $operationName= Magnetic_resonnance_imaging::select('name','id')->get();
         foreach($operationName as $op){
             $id=$op->id;
             $name=$op->name;
             $array=array(
                 'id'=>$id,
                 'name'=>$name
             );
         }
         return  Response()->json(['operation names and id'=>$array]);
    }
    if($section_name=='Laboratory section'){
        $array=[];
        $operationName= Laboratory_section::select('type_laboratory','id')->get();
         foreach($operationName as $op){
             $id=$op->id;
             $name=$op->type_laboratory;
             $array=array(
                 'id'=>$id,
                 'name'=>$name
             );
         }
         return  Response()->json(['operation names and id'=>$array]);
    }
}
public function show_queue(){
    $lineQueue = Line_queue::where('section_name','=',Auth::guard('reseption_employee')->user()->section_name)->orderBy('position')->get();
    return response()->json(['message'=>'all content of queue','queue'=>$lineQueue]);
}
public function delete_from_queue(Line_queue $lineQ){
   $line=Line_queue::where('id','=',$lineQ->id)->first();
   $line->delete();
   Line_queue::where('position', '>=', $line->position)
   ->decrement('position');
return response()->json(['message'=>'patient queue deleted successfully']);
}
public function get_visit_details(Request $request,Patient $patient){
    $array=[];
    $visit=Visit_details::where('patient_id','=',$patient->id)->where('section_name','=',Auth::guard('reseption_employee')->user()->section_name)->get();
    foreach($visit as $v){
        $array[]=array(
            'patient_id'=>$patient->id,
            'doctors_id'=>$v->doctors_id,
            'enterDate'=>$v->enterDate,
            'enterTime'=>$v->enterTime,
            'endTime'=>$v->endTime,
            'typeVisit'=>$v->typeVisit,
            'section_name'=>$v->section_name,
            'section_id'=>$v->section_id,
            'id'=>$v->id,
        );
    }
    return response()->json(['message','visit details about patient are'=>$array]);
}
public function insert_to_queue(Request $request,Patient $patient,Visit_details $visit){
    $validate=Validator::make($request->all(),[
        'position'=>'required',
        'section_id'=>'required'
    ]);
    if($validate->fails()){
        return response()->json(['message'=>$validate->errors()]);
    }
    Line_queue::where('section_name','=',Auth::guard('reseption_employee')->user()->section_name)->where('position', '>=', $request->position)
    ->increment('position');
    $line=Line_queue::create([
        'patient_id'=>$patient->id,
        'num_char'=>$this->generateRandomString(),
        'position'=>$request->position,
        'section_id'=>$request->section_id,
        'section_name'=>Auth::guard('reseption_employee')->user()->section_name,
        'visit_id'=>$visit->id
    ]);
    return response()->json(['message'=>'patient added to queue successfully']);
}
public function show_available_rooms(Operation_section $section){
    $rooms=Operation_rooms::where('available','=',true)->where('operation_sections_id','=',$section->id)->get();
    return Response()->json(['message'=>'all rooms available for patient','rooms'=>$rooms]);
}
public function input_patient_Room(Request $request,Patient $patient,Operation_rooms $room){
$validate=Validator::make($request->all(),[
    'enter_time'=>'required|date_format:H:i',
    'enter_date'=>'required|date'
]);
if($validate->fails()){
    return response()->json(['error'=>$validate->errors(),400]);
}
    $patient_room=Stay_operation_rooms::create([
        'patient_id'=>$patient->id,
        'operation_rooms_id'=>$room->id,
        'enter_time'=>$request->enter_time,
        'enter_date'=>$request->enter_date,
    ]);
    $room->available=false;
    $room->save();
    return response()->json(['message'=>'patient move to room successfully','patient room'=>$patient_room,'room'=>$room]);
}
public function getLaboratory(){
    $laboratory=Laboratory::select('name','id')->get();
    $array=[];
    foreach($laboratory as $d){
        $name=$d->name;
        $id=$d->id;
        $array=array(
            'id'=>$id,
            'name'=>$name
        );
    }
        return response()->json(['laboratory name and id'=>$array]);
}
    public function generateRandomString() {
    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // Generate a random letter
    $randomLetter = $letters[rand(0, strlen($letters) - 1)];
    // Generate a random number between 0 and 999 and pad with leading zeros if necessary
    $randomNumber = rand(0, 999);
    $randomNumberPadded = str_pad($randomNumber, 3, '0', STR_PAD_LEFT);
    // Combine and return the letter and numbers
    return $randomLetter . $randomNumberPadded;
}
//routing to section
public function routing(){
    $reseption=Auth::guard('reseption_employee')->user();
    if($reseption->section_name=='Medical clinic'){
        return response()->json(['message'=>'reception employee login to Medical clinic']);
    }
    if($reseption->section_name=='Operations section'){
        return response()->json(['message'=>'reception employee login to Operations section']);
    }
    if($reseption->section_name=='Radiation section'){
        return response()->json(['message'=>'reception employee login to Radiation section']);
    }
    if($reseption->section_name=='Magnitic section'){
        return response()->json(['message'=>'reception employee login to Magnitic section']);
    }
    if($reseption->section_name=='Laboratory section'){
        return response()->json(['message'=>'reception employee login to Laboratory section']);
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
    return  Storage::url($path);
}
}
