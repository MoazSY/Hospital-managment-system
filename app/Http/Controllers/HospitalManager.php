<?php
namespace App\Http\Controllers;
use App\Models\Accounter;
use App\Models\Consumer_employee;
use App\Models\Doctor;
use App\Models\Hospital_manager;
use App\Models\Laboratory;
use App\Models\Laboratory_section;
use App\Models\Magnetic_resonnance_imaging;
use App\Models\Medical_clinic;
use App\Models\medical_clinic_doctor;
use App\Models\Nurse;
use App\Models\Operation_rooms;
use App\Models\Operation_section;
use App\Models\Docor_operation_section;
use App\Models\Doctor_operation_section;
use App\Models\Nurse_section;
use App\Models\Radiation_section;
use App\Models\Reseption_employee;
use App\Models\User;
use App\Models\Warehouse_manager;
use App\Models\Pharmatical_warehouse;
use Illuminate\Support\Facades\Schema;
use Dotenv\Validator as DotenvValidator;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator ;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Validator as ValidationValidator;
use Illuminate\Contracts\Auth\Authenticatable;
use Carbon\Carbon;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Storage;
class HospitalManager extends Controller
{
      public function Register(Request $request,StoreUserRequest $requestUserName){

    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'birthdate'=>'required|date',
        'phoneNumber'=>'required|integer|unique:hospital_manager',
        'about_him'=>'required',
        'userName'=>'required|unique:hospital_manager',
        'password'=>'required|min:8',
        'email'=>'required|email|unique:hospital_manager',
        'address'=>'required'
    ]);
    if($validate->fails()){
    return Response()->json(['error'=>$validate->errors()]);
    }

    $hospitalM=Hospital_manager::create([
        'name'=>$request['name'],
        'image'=>$this->uploadeImage($request),
        'birthdate'=>$request['birthdate'],
        'phoneNumber'=>$request['phoneNumber'],
        'about_him'=>$request['about_him'],
        'userName'=>$requestUserName['userName'],
        'password'=>Hash::make($request['password']),
        'email'=>$request['email'],
        'address'=>$request->address
    ]);

    $token=$hospitalM->createToken('authToken')->plainTextToken;
    return Response()->json(['hospitalManager'=>$hospitalM,'token'=>$token]);

  }
  public function login(Request  $request){
    $validate=Validator::make($request->all(),[
  'userName'=>'required',
  'password'=>'required|min:8|alpha_num'
]);

if($validate->fails()){
  return Response()->json(['error'=>$validate->errors()],400);
}
    $credentials = $request->only('userName', 'password');
    // List of user tables
    $userTables = [
        'hospital_manager' => \App\Models\Hospital_Manager::class,
        'doctors' => \App\Models\Doctor::class,
        'reseption_employee' => \App\Models\Reseption_employee::class,
        'nurses' => \App\Models\Nurse::class,
        'warehouse_manager' => \App\Models\Warehouse_manager::class,
        'accounter' => \App\Models\Accounter::class,
        'laboratorys' => \App\Models\Laboratory::class,
        'consumer_employee' => \App\Models\Consumer_employee::class,
        'patient' => \App\Models\Patient::class
    ];
    $user = null;
    $userType = null;
    // Check each table for the username
    foreach ($userTables as $table => $model) {
        if (Schema::hasColumn($table, 'userName')){
        $user = $model::where('userName', $credentials['userName'])->first();
        if ($user) {
            $userType = $table;
            break;
        }
    }}
    // If no user found, return error
    if (!$user) {
        return response()->json(['message' => 'Invalid username '], 400);
    }
    // Check password
    if (!Hash::check($credentials['password'], $user->password)) {
        return response()->json(['message' => 'Invalid password'], 400);
    }
    // Generate token for authenticated user
    $token = $user->createToken('authToken')->plainTextToken;
    return response()->json([
        'message' => 'Login successfully',
        'user' => $user,
        'token' => $token,
        'user_type' => $userType
    ]);
  }
  public function profile(){
    $manager=Auth::guard('hospital_manager')->user();
    return response()->json(['message'=>'hospital manager profile','profile'=>$manager]);
  }
  public function add_doctor(Request $request,StoreUserRequest $requestUserName){

    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'birthdate'=>'required|date',
        'about_him'=>'required',
        'userName'=>'required|unique:doctors',
        'password'=>'required|min:8|alpha_num',
        'specialization'=>'required',
        'contact_info'=>'required',
        'phoneNumber'=>'required|unique:doctors',
        'email'=>'required|email|unique:doctors',
        'address'=>'required'
    ]);
    if($validate->fails()){
        return response()->json(['message'=>$validate->errors()]);
    }

    if($request->hasFile('image')){
        $doctor=Doctor::create([
            'name'=>$request->name,
            'image'=>$this->uploadeImage($request),
            'birthdate'=>$request['birthdate'],
            'about_him'=>$request['about_him'],
            'userName'=>$requestUserName['userName'],
            'password'=>Hash::make($request->password),
            'specialization'=>$request->specialization,
            'contact_info'=>$request->contact_info,
            'phoneNumber'=>$request->phoneNumber,
            'address'=>$request->address,
            'email'=>$request->email,

        ]);

        return response()->json(['message'=>'doctor add successfully','doctor'=>$doctor]);
    }
    else{
        $doctor=Doctor::create([
            'name'=>$request->name,
            'birthdate'=>$request->birthdate,
            'about_him'=>$request->about_him,
            'userName'=>$requestUserName->userName,
            'password'=>Hash::make($request->password),
            'specialization'=>$request->specialization,
            'contact_info'=>$request->contact_info,
            'phoneNumber'=>$request->phoneNumber,
            'address'=>$request->address,
            'email'=>$request->email,

        ]);
        return response()->json(['message'=>'doctor add successfully','doctor'=>$doctor]);
    }
  }
    public function delete_doctor(Doctor $doctor){

    Doctor::where('id','=',$doctor->id)->first()->delete();
    return Response()->json(['message'=>'doctor deleted successfully']);
  }
public function add_section_operation(Request $request){
        $validate=Validator::make($request->all(),[
            'Section_name'=>'required',
            'info_section'=>'required',
            'contact_info'=>'required',
            'doctors_id'=>'required',
        ]);
        if($validate->fails()){
            return Response()->json(['message'=>$validate->errors()]);
        }
        $operation=Operation_section::create([
            'Section_name'=>$request->Section_name,
            'info_section'=>$request->info_section,
            'contact_info'=>$request->contact_info,
            'doctors_id'=>$request->doctors_id,
        ]);
        return Response()->json(['message'=>'operation_section added successfully','operation_section'=>$operation]);
}
public function delete_section_operation(Operation_section $section){
Operation_section::where('id','=',$section->id)->first()->delete();
return response()->json(['message'=>'operation section deleted successfully']);
}
public function update_section_operation(Request $request,Operation_section $section){
   Operation_section::where('id','=',$section->id)->first()->update($request->all());
   $operation= Operation_section::where('id','=',$section->id)->first();
return response()->json(['message'=>'operation section updated successfully','operation'=>$operation]);
}
public function add_nurse(Request $request,StoreUserRequest $requestUserName){
    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'about_him'=>'required',
        'birthdate'=>'required|date',
        'phoneNumber'=>'required',
        'userName'=>'required|unique:nurses',
        'password'=>'required|min:8|alpha_num'
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    if($request->hasFile('image')){
        $nurse=Nurse::create([
            'name'=>$request->name,
            'image'=>$this->uploadeImage($request),
            'about_him'=>$request->about_him,
            'birthdate'=>$request->birthdate,
            'phoneNumber'=>$request->phoneNumber,
            'userName'=>$requestUserName->userName,
            'address'=>$request->address,
            'password'=>$request->password
        ]);
        return Response()->json(['message'=>'nurse added successfully','nurse'=>$nurse]);
    }
    else{
        $nurse=Nurse::create([
            'name'=>$request->name,
            'about_him'=>$request->about_him,
            'birthdate'=>$request->birthdate,
            'phoneNumber'=>$request->phoneNumber,
            'userName'=>$requestUserName->userName,
            'address'=>$request->address,
            'password'=>$request->password
        ]);
        return Response()->json(['message'=>'nurse added successfully','nurse'=>$nurse]);
    }

}
public function deleteNurse(Nurse $nurse){
    Nurse::where('id','=',$nurse->id)->first()->delete();
    return Response()->json(['message'=>'nurse deleted successfully']);
}
public function add_laboratory(Request $request,StoreUserRequest $requestUserName){

    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'birthdate'=>'required|date',
        'about_him'=>'required',
        'phoneNumber'=>'required',
        'userName'=>'required|unique:laboratorys',
        'password'=>'required|min:8|alpha_num',
        'email'=>'required|email|unique:laboratorys'

    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    if($request->hasFile('image')){
        $laboratory=Laboratory::create([
            'name'=>$request->name,
            'image'=>$this->uploadeImage($request),
            'birthdate'=>$request->birthdate,
            'about_him'=>$request->about_him,
            'phoneNumber'=>$request->phoneNumber,
            'userName'=>$requestUserName->userName,
            'password'=>Hash::make($request->password),
            'address'=>$request->address,
            'email'=>$request->email
        ]);
        return Response()->json(['message'=>'laboratory added successfully','laboratory'=>$laboratory]);
    }
    else{
        $laboratory=Laboratory::create([
            'name'=>$request->name,
            'birthdate'=>$request->birthdate,
            'about_him'=>$request->about_him,
            'phoneNumber'=>$request->phoneNumber,
            'password'=>Hash::make($request->password),
            'userName'=>$requestUserName->userName,
            'address'=>$request->address,
            'email'=>$request->email
        ]);
        return Response()->json(['message'=>'laboratory added successfully','laboratory'=>$laboratory]);
    }
}
public function deleteLaboratory(Laboratory $lab){
    Laboratory::where('id','=',$lab->id)->first()->delete();
    return response()->json(['message'=>'laboratory deleted successfully']);
}
public function add_accounter(Request $request,StoreUserRequest $requestUserName){
    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'birthdate'=>'required|date',
        'about_him'=>'required',
        'address'=>'required',
        'phoneNumber'=>'required',
        'userName'=>'required|unique:accounter',
        'password'=>'required|min:8|alpha_num',
        'email'=>'required|email|unique:accounter',
        'operation_sections_id'=>'required'
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    if($request->hasFile('image')){
        $accounter=Accounter::create([
            'name'=>$request->name,
            'image'=>$this->uploadeImage($request),
            'birthdate'=>$request->birthdate,
            'about_him'=>$request->about_him,
            'phoneNumber'=>$request->phoneNumber,
            'password'=>Hash::make($request->password),
            'userName'=>$requestUserName->userName,
            'email'=>$request->email,
            'address'=>$request->address,
            'operation_sections_id'=>$request->operation_sections_id,
        ]);
        return Response()->json(['message'=>'accounter added successfully','accounter'=>$accounter]);
    }
    else{
        $accounter=Accounter::create([
            'name'=>$request->name,
            'birthdate'=>$request->birthdate,
            'about_him'=>$request->about_him,
            'phoneNumber'=>$request->phoneNumber,
            'password'=>Hash::make($request->password),
            'userName'=>$requestUserName->userName,
            'email'=>$request->email,
            'address'=>$request->address,
            'operation_sections_id'=>$request->operation_sections_id,

        ]);
        return Response()->json(['message'=>'accounter added successfully','accounter'=>$accounter]);
    }
}
public function deleteAccounter(Accounter $acc){
    Accounter::where('id','=',$acc->id)->first()->delete();
    return response()->json(['message'=>'accounter deleted seccessfuly']);
}
public function add_operation_rooms(Request $request){

    $validate=Validator::make($request->all(),[
        'operation_sections_id'=>'required',
        'numberRoom'=>'required',
        'hour_price_stay'=>'required'
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    $rooms=Operation_rooms::create([
        'operation_sections_id'=>$request->operation_sections_id,
        'numberRoom'=>$request->numberRoom,
        'hour_price_stay'=>$request->hour_price_stay,
        'available'=>true
    ]);
    return Response()->json(['message'=>'operation room added successfully','operation room'=>$rooms]);
}
public function delete_operation_room(Operation_rooms $Oproom){
    Operation_rooms::where('id','=',$Oproom->id)->first()->delete();
    return response()->json(['message'=>'operation room deleted successfully']);
}
public function update_operation_room(Request $request ,Operation_rooms $Oproom){
    Operation_rooms::where('id','=',$Oproom->id)->first()->update($request->all());
    return response()->json(['message'=>'operation room updated successfully']);
}
public function add_reseption_employee(Request $request,StoreUserRequest $requestUserName){
    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'birthdate'=>'required',
        'phoneNumber'=>'required',
        'address'=>'required',
        'userName'=>'required|unique:reseption_employee',
        'password'=>'required|min:8|alpha_num',
        'email'=>'required|email|unique:reseption_employee',
        'section_name'=>'required',
        'id_section'=>'required'
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    if($request->hasFile('image')){
        $reseption=Reseption_employee::create([
            'name'=>$request->name,
            'image'=>$this->uploadeImage($request),
            'birthdate'=>$request->birthdate,
            'phoneNumber'=>$request->phoneNumber,
            'userName'=>$requestUserName->userName,
            'password'=> Hash::make($request->password) ,
            'email'=>$request->email,
            'section_name'=>$request->section_name,
            'id_section'=>$request->id_section,
            'address'=>$request->id_section
        ]);
        return Response()->json(['message'=>'employee added successfully','employee'=>$reseption]);
    }
    else{
        $reseption=Reseption_employee::create([
            'name'=>$request->name,
            'birthdate'=>$request->birthdate,
            'phoneNumber'=>$request->phoneNumber,
            'userName'=>$requestUserName->userName,
            'password'=>Hash::make($request->password) ,
            'email'=>$request->email,
            'section_name'=>$request->section_name,
            'id_section'=>$request->id_section,
            'address'=>$request->id_section

        ]);
        return Response()->json(['message'=>'employee added successfully','employee'=>$reseption]);
    }
}
public function delete_employee_reseption(Reseption_employee $employee){
    Reseption_employee::where('id','=',$employee->id)->first()->delete();
    return response()->json(['message'=>'reseption employee deleted successfuly']);
}
public function update_employee_reseption(Request $request){
    Reseption_employee::where('id','=',$request->id)->first()->update($request->all());
    return response()->json(['message'=>'reseption employee update successfuly']);
}
public function add_radiation_section(Request $request){
    $validate=Validator::make($request->all(),[
        'doctors_id'=>'required',
        'address'=>'required',
        'contact_info'=>'required',
        'info_about'=>'required',
        'name'=>'required'
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    $radiation=Radiation_section::create([
        'doctors_id'=>$request->doctors_id,
        'address'=>$request->address,
        'contact_info'=>$request->contact_info,
        'info_about'=>$request->info_about,
        'name'=>$request->name

    ]);
    return response()->json(['message'=>'radiation_section added successfully','radiation_section'=>$radiation]);
}
public function delete_radiation_section(Radiation_section $section){
    Radiation_section::where('id','=',$section->id)->first()->delete();
    return response()->json(['message'=>'radiation section deleted successfully']);
}
public function update_radiation_section(Request $request ,Radiation_section $section){
    Radiation_section::where('id','=',$section->id)->first()->update($request->all());
    $update= Radiation_section::where('id','=',$section->id)->first();
    return response()->json(['message'=>'radiation section updated successfully','update'=>$update]);
}
public function add_magnitic_section(Request $request){
    $validate=Validator::make($request->all(),[
    'doctors_id'=>'required',
    'address'=>'required',
    'contact_info'=>'required',
    'info_about'=>'required',
    'name'=>'required'
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    $magnitic=Magnetic_resonnance_imaging::create([
        'doctors_id'=>$request->doctors_id,
        'address'=>$request->address,
        'contact_info'=>$request->contact_info,
        'info_about'=>$request->info_about,
        'name'=>$request->name
    ]);
    return response()->json(['message'=>'magnetic section added successfully','magnetic'=>$magnitic]);
}
public function delete_magnitic_section(Magnetic_resonnance_imaging $section){
    Magnetic_resonnance_imaging::where('id','=',$section->id)->first()->delete();
    return response()->json(['message'=>'magnitic section deleted successfully']);
}
public function update_magnitic_section(Request $request ,Magnetic_resonnance_imaging $section){
    Magnetic_resonnance_imaging::where('id','=',$section->id)->first()->update($request->all());
    $update= Magnetic_resonnance_imaging::where('id','=',$section->id)->first();
    return response()->json(['message'=>'magnitic section updated successfully','update'=>$update]);
}
public function add_laboratory_section(Request $request){
    $validate=Validator::make($request->all(),[
        'type_laboratory'=>'required',
        'start_time'=>'required|date_format:H:i',
        'end_time'=>'required|date_format:H:i',
        'days'=>'required|array',
        'address'=>'required',
        'contact_info'=>'required',
        'about_him'=>'required',
        'laboratorys_id'=>'required',
        ]);
        if($validate->fails()){
            return Response()->json(['message'=>$validate->errors()]);
        }
        $laboratory_section=Laboratory_section::create([
        'type_laboratory'=>$request->type_laboratory,
        'address'=>$request->address,
        'contact_info'=>$request->contact_info,
        'about_him'=>$request->about_him,
        'start_time'=>$request->start_time,
        'end_time'=>$request->end_time,
        'days'=>$request->days,
        'laboratorys_id'=>$request->laboratorys_id
        ]);
        return response()->json(['message'=>'laboratory section added successfully','laboratory section'=>$laboratory_section]);
}
public function delete_laboratory_section(Laboratory_section $section){
    Laboratory_section::where('id','=',$section->id)->first()->delete();
    return response()->json(['message'=>'laboratory section deleted successfully']);
}
public function update_laboratory_section(Request $request ,Laboratory_section $section){
    Laboratory_section::where('id','=',$request->id)->first()->update($request->all());
    $update=Laboratory_section::where('id','=',$request->id)->first();
    return response()->json(['message'=>'laboratory section updated successfully','update'=>$update]);
}
public function add_consumer_employee(Request $request,StoreUserRequest $requestUserName){
    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'birthdate'=>'required',
        'about_him'=>'required',
        'phoneNumber'=>'required',
        'operation_sections_id'=>'required',
        'userName'=>'required|unique:reseption_employee',
        'password'=>'required|min:8|alpha_num',
        'email'=>'required|email|unique:consumer_employee',
        'address'=>'required'
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    if($request->hasFile('image')){
        $consumer=Consumer_employee::create([
            'name'=>$request->name,
            'image'=>$this->uploadeImage($request),
            'birthdate'=>$request->birthdate,
            'phoneNumber'=>$request->phoneNumber,
            'userName'=>$requestUserName->userName,
            'password'=>Hash::make($request->password),
            'email'=>$request->email,
            'operation_sections_id'=>$request->operation_sections_id,
            'about_him'=>$request->about_him,
            'address'=>$request->address
        ]);
        return Response()->json(['message'=>' consumer employee added successfully','consumer employee '=>$consumer]);
    }
    else{
        $consumer=Consumer_employee::create([
            'name'=>$request->name,
            'birthdate'=>$request->birthdate,
            'phoneNumber'=>$request->phoneNumber,
            'password'=>Hash::make($request->password),
            'userName'=>$requestUserName->userName,
            'email'=>$request->email,
            'operation_sections_id'=>$request->operation_sections_id,
            'address'=>$request->address,
            'about_him'=>$request->about_him
        ]);
        return Response()->json(['message'=>'employee added successfully','employee'=>$consumer]);
    }
}
public function delete_consumer_employee(Consumer_employee $employee){
    Consumer_employee::where('id','=',$employee->id)->first()->delete();
    return response()->json(['message'=>'consumer employee deleted successfully']);
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
 public function operation_secion() {
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
    public function add_medical_clinic(Request $request){
    $validate=Validator::make($request->all(),[
        'name'=>'required',
        'start_time'=>'required|date_format:H:i',
        'end_time'=>'required|date_format:H:i',
        'days'=>'required|array',
        'address'=>'required',
        'contact_info'=>'required',
        'info_clinic'=>'required'
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    $medicalClinic=Medical_clinic::create([
        'name'=>$request->name,
        'start_time'=>$request->start_time,
        'end_time'=>$request->end_time,
        'days'=>$request->days,
        'address'=>$request->address,
        'contact_info'=>$request->contact_info,
        'info_clinic'=>$request->info_clinic
    ]);
    return response()->json(['message'=>'medical clinic added successfully','clinic'=>$medicalClinic]);
}
public function delete_medical_clinic(Medical_clinic $medical){
    Medical_clinic::where('id','=',$medical->id)->first()->delete();
    return response()->json(['message'=>'medical clinic deleted successfully']);
}
public function update_medical_clinic(Request $request,Medical_clinic $medical){
    Medical_clinic::where('id','=',$medical->id)->first()->update($request->all());
    return response()->json(['message'=>'medical clinic updated successfully']);
}
public function  get_medical_clinic(){
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
public function add_doctor_clinic(Request $request){
    $validate=Validator::make($request->all(),[
        'price'=>'required',
        'start_time'=>'required|date_format:H:i',
        'end_time'=>'required|date_format:H:i',
        'days'=>'required|array',
        'medical_clinic_id'=>'required',
        'doctors_id'=>'required',
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    $medicalClinic=medical_clinic_doctor::create([
        'price'=>$request->price,
        'start_time'=>$request->start_time,
        'end_time'=>$request->end_time,
        'days'=>$request->days,
        'medical_clinic_id'=>$request->medical_clinic_id,
        'doctors_id'=>$request->doctors_id,
    ]);
    return response()->json(['message'=>'medical clinic added successfully','clinic'=>$medicalClinic]);
}
public function update_medical_clinic_doctor(Request $request,medical_clinic_doctor $medical_doctor){
    medical_clinic_doctor::where('id','=',$request->id)->first()->update($request->all());
    return response()->json(['message'=>'medical clinic doctor updated successfully']);
}
public function add_operation_section_doctor(Request $request){
$validate=Validator::make($request->all(),[
    'startWorkTime'=>'required|date_format:H:i',
    'endWorkTime'=>'required|date_format:H:i',
    'days'=>'required|array',
    'operation_sections_id'=>'required',
    'doctors_id'=>'required',
]);
if($validate->fails()){
    return Response()->json(['message'=>$validate->errors()]);
}
$docor_operation=Doctor_operation_section ::create([
    'startWorkTime'=>$request->startWorkTime,
    'endWorkTime'=>$request->endWorkTime,
    'days'=>$request->days,
    'operation_sections_id'=>$request->operation_sections_id,
    'doctors_id'=>$request->doctors_id,
]);
return Response()->json(['message'=>'operation section doctor added successfully','doctor operation section'=>$docor_operation]);
}
public function update_operation_section_doctor(Request $request,Doctor_operation_section $doctorOperation){
    Doctor_operation_section::where('id','=',$doctorOperation->id)->first()->update($request->all());
    $doctorOP= Doctor_operation_section::where('id','=',$doctorOperation->id)->first();
    return response()->json(['message'=>'doctor operation section updated successfully','doctor operation section'=>$doctorOP]);
}
public function add_nurse_section(Request $request){
    $validate=Validator::make($request->all(),[
        'startTime'=>'required|date_format:H:i',
        'endTime'=>'required|date_format:H:i',
        'days'=>'required|array',
        'operation_sections_id'=>'required',
        'nurses_id'=>'required',
    ]);
    if($validate->fails()){
        return Response()->json(['message'=>$validate->errors()]);
    }
    $nurse_operation=Nurse_section::create([
        'nurses_id'=>$request->nurses_id,
        'operation_sections_id'=>$request->operation_sections_id,
        'startTime'=>$request->startTime,
        'endTime'=>$request->endTime,
        'days'=>$request->days
    ]);
return Response()->json(['message'=>'operation section nurse added successfully','nurse operation section'=>$nurse_operation]);
}
public function update_nurse_section(Request $request,Nurse_section $nurseSection){
    Nurse_section::where('id','=',$nurseSection->id)->first()->update($request->all());
    $update=Nurse_section::where('id','=',$nurseSection->id)->first();
    return response()->json(['message'=>'doctor operation section updated successfully','update'=>$update]);
}
public function add_warehouse_manager(Request $request ,StoreUserRequest $requestUserName){
$validate=Validator::make($request->all(),[
    'name'=>'required',
    // 'image',
    'birthdate'=>'required|date',
    'about_him'=>'required',
    'phoneNumber'=>'required',
    'userName'=>'required|unique:warehouse_manager',
    'password'=>'required|min:8|alpha_num',
    'email'=>'required|email|unique:warehouse_manager'
]);
if($validate->fails()){
    return response()->json(['message'=>$validate->errors()]);
}
if($request->hasFile('image')){
    $warehouseManager=Warehouse_manager::create([
        'name'=>$request->name,
        'birthdate'=>$request->birthdate,
        'about_him'=>$request->about_him,
        'phoneNumber'=>$request->phoneNumber,
        'userName'=>$requestUserName->userName,
        'password'=>$request->password,
        'email'=>$request->email,
        'image'=>$this->uploadeImage($request)
    ]);
    return response()->json(['message'=>'warehouse added successfully','warehouse'=>$warehouseManager]);
}else{
       $warehouseManager=Warehouse_manager::create([
        'name'=>$request->name,
        'birthdate'=>$request->birthdate,
        'about_him'=>$request->about_him,
        'phoneNumber'=>$request->phoneNumber,
        'userName'=>$requestUserName->userName,
        'password'=>$request->password,
        'email'=>$request->email,
    ]);
    return response()->json(['message'=>'warehouse added successfully','warehouse'=>$warehouseManager]);
}
}
public function add_pharmatical_warehouse(Request $request){
    $validate=Validator::make($request->all(),[
        'warehouse_manager_id'=>'required',
        'address'=>'required',
        'contact_info'=>'required',
        'details_info'=>'required'
    ]);
    if($validate->fails()){
        return response()->json(['message'=>$validate->errors()]);
    }
    $pharmatical=Pharmatical_warehouse::create([
        'warehouse_manager_id'=>$request->warehouse_manager_id,
        'address'=>$request->address,
        'contact_info'=>$request->contact_info,
        'details_info'=>$request->details_info
    ]);
    return response()->json(['message'=>'pharmatical warehouse added successfully','pharmatical warehouse'=>$pharmatical]);
}
public function update_pharmatical_warehouse(Request $request,Pharmatical_warehouse $pharmatical){
 $update=Pharmatical_warehouse::where('id','=',$pharmatical->id)->first()->update($request->all());
 $Pharmatical_warehouse=Pharmatical_warehouse::where('id','=',$pharmatical->id)->first();
 return response()->json(['message'=>'pharmatical warehouse updated successfully','pharmatical warehouse'=>$Pharmatical_warehouse]);
}
public function delete_pharmatical_warehouse(Pharmatical_warehouse $pharmatical){
 $delete=Pharmatical_warehouse::where('id','=',$pharmatical->id)->first()->delete();
 return response()->json(['message'=>'pharmatical warehouse deleted successfully']);

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
