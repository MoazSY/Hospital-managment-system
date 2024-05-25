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
use App\Models\Nurse;
use App\Models\Operation_rooms;
use App\Models\Operation_section;
use App\Models\Radiation_section;
use App\Models\Reseption_employee;
use App\Models\User;
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
use Illuminate\Support\Facades\Storage;
class HospitalManager extends Controller
{
      public function Register(Request $request){

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
        'userName'=>$request['userName'],
        'password'=>Hash::make($request['password']),
        'email'=>$request['email'],
        'address'=>$request->address
    ]);

    $token=$hospitalM->createToken('authToken')->plainTextToken;
    return Response()->json(['hospitalManager'=>$hospitalM,'token'=>$token]);

  }
  public function login(Request $request){

$validate=Validator::make($request->all(),[
  'userName'=>'required',
  'password'=>'required|min:8|alpha_num'
]);

if($validate->fails()){
  return Response()->json(['error'=>$validate->errors()],400);
}


$credentials = $request->only('userName', 'password');
$user=Hospital_manager::where('userName','=',$credentials['userName'])->first();
if(!$user || !Hash::check($credentials['password'],$user->password)){
    if(!Hospital_manager::where('userName','=',$credentials['userName'])->first()){
        return response()->json(['message'=>' error userName'." ". $credentials['userName']." ".'resend Right value'],400);
    }
    if(!Hospital_manager::where('password','=',$credentials['password'])->first()){
        return response()->json(['message'=>' error password' ." ". $credentials['password'] ." ".'resend Right value'],400);
    }
    return response()->json(['message'=>' error value resend right value'],200);

}


  $hospitalM=Hospital_manager::where('userName','=',$credentials['userName'])->first();
  $token=$hospitalM->createToken('authToken')->plainTextToken;
  return response()->json(['message'=>'login successfully','manager'=>$hospitalM,'token'=>$token]);
  }
  public function add_doctor(Request $request){

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
        return response()->json(['message'=>'error',$validate->errors()]);
    }

    if($request->hasFile('image')){
        $doctor=Doctor::create([
            'name'=>$request->name,
            'image'=>$this->uploadeImage($request),
            'birthdate'=>$request['birthdate'],
            'about_him'=>$request['about_him'],
            'userName'=>$request['userName'],
            'password'=>$request->password,
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
            'userName'=>$request->userName,
            'password'=>$request->password,
            'specialization'=>$request->specialization,
            'contact_info'=>$request->contact_info,
            'phoneNumber'=>$request->phoneNumber,
            'address'=>$request->address,
            'email'=>$request->email,

        ]);
        return response()->json(['message'=>'doctor add successfully','doctor'=>$doctor]);
    }

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
public function add_nurse(Request $request){
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
            'userName'=>$request->userName,
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
            'userName'=>$request->userName,
            'address'=>$request->address,
            'password'=>$request->password
        ]);
        return Response()->json(['message'=>'nurse added successfully','nurse'=>$nurse]);
    }

}
public function add_laboratory(Request $request){

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
            'userName'=>$request->userName,
            'password'=>$request->password,
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
            'userName'=>$request->userName,
            'password'=>$request->password,
            'address'=>$request->address,
            'email'=>$request->email
        ]);
        return Response()->json(['message'=>'laboratory added successfully','laboratory'=>$laboratory]);
    }

}
public function add_accounter(Request $request){
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
            'userName'=>$request->userName,
            'password'=>$request->password,
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
            'userName'=>$request->userName,
            'password'=>$request->password,
            'email'=>$request->email,
            'address'=>$request->address,
            'operation_sections_id'=>$request->operation_sections_id,

        ]);
        return Response()->json(['message'=>'accounter added successfully','accounter'=>$accounter]);
    }

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
        'hour_price_stay'=>$request->hour_price_stay
    ]);
    return Response()->json(['message'=>'operation room added successfully','operation room'=>$rooms]);
}

public function add_reseption_employee(Request $request){
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
            'userName'=>$request->userName,
            'password'=>$request->password,
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
            'userName'=>$request->userName,
            'password'=>$request->password,
            'email'=>$request->email,
            'section_name'=>$request->section_name,
            'id_section'=>$request->id_section,
            'address'=>$request->id_section

        ]);
        return Response()->json(['message'=>'employee added successfully','employee'=>$reseption]);
    }
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
public function add_laboratory_section(Request $request){

    $validate=Validator::make($request->all(),[
        'type_laboratory'=>'required',
        'address'=>'required',
        'contact_info'=>'required',
        'about_him'=>'required',
        'laboratorys_id'=>'required'
        ]);
        if($validate->fails()){
            return Response()->json(['message'=>$validate->errors()]);
        }
        $laboratory_section=Laboratory_section::create([
        'type_laboratory'=>$request->type_laboratory,
        'address'=>$request->address,
        'contact_info'=>$request->contact_info,
        'about_him'=>$request->about_him,
        'laboratorys_id'=>$request->laboratorys_id
        ]);
        return response()->json(['message'=>'laboratory section added successfully','laboratory section'=>$laboratory_section]);
}
public function add_consumer_employee(Request $request){
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
            'userName'=>$request->userName,
            'password'=>$request->password,
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
            'userName'=>$request->userName,
            'password'=>$request->password,
            'email'=>$request->email,
            'operation_sections_id'=>$request->operation_sections_id,
            'address'=>$request->address,
            'about_him'=>$request->about_him
        ]);
        return Response()->json(['message'=>'employee added successfully','employee'=>$consumer]);
    }
}
 public function operation_secion() {
//    $query=Operation_section::query();
//    $query->select('Section_name','id')->from('operation_section')->get();

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