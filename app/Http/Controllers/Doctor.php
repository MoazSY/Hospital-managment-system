<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreUserRequest;
use App\Models\Blood_pressure;
use App\Models\Doctor as ModelsDoctor;
use App\Models\Doctor_examination;
use App\Models\Doctor_operation_section;
use App\Models\Ecg;
use App\Models\Eco;
use App\Models\Laboratory_anylysis;
use App\Models\Line_queue;
use App\Models\Magnetic_resonnance_imaging;
use App\Models\Medical_clinic;
use App\Models\medical_clinic_doctor;
use App\Models\Medical_examination;
use App\Models\Operation_section;
use App\Models\Operations;
use App\Models\Patient;
use App\Models\Radiation_section;
use App\Models\Sugar_blood;
use App\Models\Visit_details;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class Doctor extends Controller
{
    public function updateDoctor(Request $request,StoreUserRequest $requestuser){
        $doctor=Auth::guard('doctor')->user();
        ModelsDoctor::where('id','=',$doctor->id)->update($request->only(
            'name',
            'image',
            'birthdate',
            'about_him',
            'password',
            'specialization',
            'contact_info',
            'phoneNumber',
            'email',
            'address'
        ));
        if($request['userName']){
            ModelsDoctor::where('id','=',$doctor->id)->first()->update($requestuser->only('userName'));
        }
        $doctorinfo=ModelsDoctor::where('id','=',$doctor->id)->first();
        return response()->json(['message'=>'doctor updated successfully','doctor'=>$doctorinfo]);
    }
    public function update_clinic(Request $request,medical_clinic_doctor $clinic){
        $doctor=Auth::guard('doctor')->user();
       $update_clinic= medical_clinic_doctor::where('doctors_id','=',$doctor->id)->where('id','=',$clinic->id)->first()->update($request->only('price','start_time','end_time', 'days'));
       return response()->json(['message'=>'medical clinic updated successfully','clinic'=>$update_clinic]);
    }
    public function update_operation_section(Request $request,Doctor_operation_section $section){
        $doctor=Auth::guard('doctor')->user();
        $update_operation_section=Doctor_operation_section::where('doctors_id','=',$doctor->id)->where('id','=',$section->id)->first()->update($request->only('startWorkTime','endWorkTime','days'));
        return response()->json(['message'=>'operation section updated successfully','operation section'=>$update_operation_section]);
    }
    public function profile(){
        $doctor=Auth::guard('doctor')->user();
        return response()->json(['message'=>'doctor profile','profile'=>$doctor,'operation'=>$doctor->operation,'medical_clinic'=>$doctor->medical_clinic_doctor,'operation_section'=>$doctor->operation_section,'doctor_operation_section'=>$doctor->doctor_operation_section]);
    }//return array of section name related to doctor called after login
    public function routing_section(Request $request){
        $operation=array();
        $medical=array();
        $operation_management=array();
        $radiation_section=array();
        $imaging_section=array();
        $doctor=Auth::guard('doctor')->user();
        $doctor=ModelsDoctor::where('id','=',$doctor->id)->first();
        if($doctor->doctor_operation_section()->exists()){
          foreach($doctor->doctor_operation_section as $section){
            $sectionName=Operation_section::where('id','=',$section->operation_sections_id)->first();
            array_push($operation,[$sectionName->Section_name,'Operation Section']);
          }
        }
        if($doctor->medical_clinic_doctor()->exists()){
            foreach($doctor->medical_clinic_doctor as $medical){
            $medicalName=Medical_clinic::where('id','=',$medical->id)->first();
                array_push($medical,[$medicalName->name,'Medical clinic']);
            }
        }
        if($doctor->operation_section()->exists()){
            foreach($doctor->operation_section as $section){
                array_push($operation_management,[$section->Section_name,'Operation Managment']);
            }
        }
        if($doctor->radiology_section()->exists()){
            foreach($doctor->radiology_section as $section){
                array_push($radiation_section,[$section->name,'Radiology Section']);
            }
        }
        if($doctor->imaging_section()->exists()){
            foreach($doctor->imaging_section as $section){
                array_push($imaging_section,[$section->name,'Imaging Section']);
            }
        }
        return response()->json(['message'=>'choose any section you want to move it','operation section'=>$operation,
        'medical clinic'=> $medical,'managment operation section'=>$operation_management, 'radiation section'=>$radiation_section,'imaging section'=>$imaging_section]);
    }
    //get section and sectionName from array above
    public function choose_section(Request $request){
        $section=$request->section;
        $sectionName=$request->section_name;
        if($section=='Operation Section'){
            $operationSection=Operation_section::where('Section_name','=',$sectionName)->first();
            $operatioSectionDoctor=Doctor_operation_section::where('id','=',$operationSection->id)->first();
            $operatioSectionDoctor->available=true;
            return response()->json(['message'=>'doctor available in operation section','operation section'=>$sectionName]);
        }
        if($section=='Medical clinic'){
            $medical_clinic=Medical_clinic::where('name','=',$sectionName)->first();
            $medicalClinic_doctor=medical_clinic_doctor::where('id','=',$medical_clinic->id)->first();
            $medicalClinic_doctor->doctor_available=true;
            return response()->json(['message'=>'doctor available in medical clinic','medical clinic'=>$sectionName]);
        }
        if($section=='Operation Managment'){
            $operation=Operation_section::where('Section_name','=',$sectionName)->first();
            $operation->available=true;
            return response()->json(['message'=>'doctor available in operation section managment','operation section'=>$sectionName]);

        }
        if($section='Radiology Section'){
            $radiation=Radiation_section::where('name','=',$sectionName)->first();
            $radiation->available=true;
            return response()->json(['message'=>'doctor available in radiology section','dadiology section'=>$sectionName]);
        }
            if($section='Imaging Section'){
            $imaging=Magnetic_resonnance_imaging::where('name','=',$sectionName)->first();
            $imaging->available=true;
            return response()->json(['message'=>'doctor available in magnitic section','magnitic section'=>$sectionName]);
        }
    }
    //midecal queue
    public function extract_from_queue(){
        $doctor=Auth::guard('doctor')->user();
        $doctor=ModelsDoctor::where('id','=',$doctor->id)->first();
        if($doctor->medical_clinic_doctor()->exists()){
            $medical=medical_clinic_doctor::where('doctor_available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$medical->medical_clinic_id)->where('section_name','=','Medical clinic')->orderBy('position','asc')->first();
            $line_queue->delete();
            $patient=Patient::where('id','=',$line_queue->patient_id)->first();
            Line_queue::where('section_id','=',$medical->medical_clinic_id)->where('section_name','=','Medical clinic')->where('position','>',$line_queue->position)->decrement('position');
            $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
            $visit_details->enterTime=Carbon::now()->format('H:i');
            return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
        }
        if($doctor->operation_section()->exists()){
            $operation=Operation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->orderBy('position','asc')->first();
            $line_queue->delete();
            $patient=Patient::where('id','=',$line_queue->patient_id)->first();
            Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','>',$line_queue->position)->decrement('position');
            $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
            $visit_details->enterTime=Carbon::now()->format('H:i');
            return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
        }
        if($doctor->doctor_operation_section()->exists()){
            $operation=Doctor_operation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$operation->operation_sections_id)->where('section_name','=','Operations section')->orderBy('position','asc')->first();
            $line_queue->delete();
            $patient=Patient::where('id','=',$line_queue->patient_id)->first();
            Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','>',$line_queue->position)->decrement('position');
            $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
            $visit_details->enterTime=Carbon::now()->format('H:i');
            return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
        }
        if($doctor->radiology_section()->exists()){
            $radiation=Radiation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$radiation->id)->where('section_name','=','Radiation section')->orderBy('position','asc')->first();
            $line_queue->delete();
            $patient=Patient::where('id','=',$line_queue->patient_id)->first();
            Line_queue::where('section_id','=',$radiation->id)->where('section_name','=','Radiation section')->where('position','>',$line_queue->position)->decrement('position');
            $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
            $visit_details->enterTime=Carbon::now()->format('H:i');
            return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
        }
            if($doctor->imaging_section()->exists()){
            $magnitic=Magnetic_resonnance_imaging::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$magnitic->id)->where('section_name','=','Magnitic section')->orderBy('position','asc')->first();
            $line_queue->delete();
            $patient=Patient::where('id','=',$line_queue->patient_id)->first();
            Line_queue::where('section_id','=',$magnitic->id)->where('section_name','=','Magnitic section')->where('position','>',$line_queue->position)->decrement('position');
            $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
            $visit_details->enterTime=Carbon::now()->format('H:i');
            return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
        }
    }
public function doctor_examination(Request $request,Line_queue $line){
$validate=Validator::make($request->all(),[
    'Current_symptoms'=>'required',
    'Symptoms_appear'=>'required|date',
    'result_examination'=>'required',
]);
if($validate->fails()){
    return response()->json(['message'=>$validate->errors(),400]);
}
$doctor=Auth::guard('doctor')->user();
$patient=$line->patient_id;
$visit_id=$line->visit_id;
$visit=Visit_details::where('id','=',$visit_id)->first();
$visit->endTime=Carbon::now()->format('H:i');
    $examination=Doctor_examination::create([
        'patient_id'=>$patient,
        'doctors_id'=>$doctor->id,
        'medical_history'=>$request->medical_history,
        'previous_illnesses'=>$request->previous_illnesses,
        'Current_symptoms'=>$request->Current_symptoms,
        'Symptoms_appear'=>$request->Symptoms_appear,
        'Medications_taken'=>$request->Medications_taken,
        'id_medical_examination'=>$request->id_medical_examination,
        'laboratory_analysis'=>$request->laboratory_analysis,
        'ask_radiation_image'=>$request->ask_radiation_image,
        'placeRadiation'=>$request->placeRadiation,
        'ask_magnetic_image'=>$request->ask_magnetic_image,
        'place_magnetic'=>$request->place_magnetic,
        'ask_operationAction'=>$request->ask_operationAction,
        'NameActionOperation'=>$request->NameActionOperation,
        'drugs_id'=>$request->drugs_id,
        'result_examination'=>$request->result_examination,
        'medical_recomendation'=>$request->medical_recomendation
    ]);
    if(!$request->id_medical_examination->isEmpty()){
        foreach($request->id_medical_examination as $item){
            $exam_name=Medical_examination::where('id','=',$item)->first()->name_examination;
            if($exam_name=='Blood pressure'){
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
                    'id_doctor'=>$doctor->id,
                    'date'=>$request->date,
                    'systolic_blood_pressure'=>$request->systolic_blood_pressure,
                    'diastolic_blood_pressure'=>$request->diastolic_blood_pressure,
                    'number_pulses'=>$request->number_pulses,
                    'result'=>$request->result
                ]); }
            if($exam_name=='Sugar blood'){
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
                    'patient_id'=>$patient,
                    'id_doctor'=>$doctor->id,
                    'date'=>$request->date,
                    'blood_sugar'=>$request->blood_sugar,
                    'result'=>$request->result
                   ]); }
            if($exam_name=='Eco'){
             $medical=Medical_examination::where('name_examination','=','Eco')->first();
             $validate2=Validator::make($request->all(),[
                'date'=>'required|date',
                'image_eco'=>'required|array',
                'mitral_value'=>'required',
                'aortic_value'=>'required',
                'tricuspid_valve'=>'required',
                'pulmonary_valve'=>'required',
                'left_artial_valve'=>'required',
                'hijab_among_ears'=>'required',
                'interventricular_diaphragm'=>'required',
                'left_ventricle'=>'required',
                'right_ventricle'=>'required',
                'pericardium'=>'required',
                'result_eco'=>'required'
               ]);
               if($validate2->fails()){
                   return response()->json(['message'=>$validate2->errors()]);
               }
               $eco=Eco::create([
                'medical_examination_id'=>$medical->id,
                'patient_id'=>$patient,
                'doctors_id'=>$doctor->id,
                'date'=>$request->date,
                'image_eco'=>$this->uploadeImage($request),
                'mitral_value'=>$request->mitral_value,
                'aortic_value'=>$request->aortic_value,
                'tricuspid_valve'=>$request->tricuspid_valve,
                'pulmonary_valve'=>$request->pulmonary_valve,
                'left_artial_valve'=>$request->left_artial_valve,
                'hijab_among_ears'=>$request->hijab_among_ears,
                'interventricular_diaphragm'=>$request->interventricular_diaphragm,
                'left_ventricle'=>$request->left_ventricle,
                'right_ventricle'=>$request->right_ventricle,
                'pericardium'=>$request->pericardium,
                'result_eco'=>$request->result_eco
               ]); }
            if($exam_name=='Ecg'){
            $medical=Medical_examination::where('name_examination','=','Ecg')->first();
            $validate2=Validator::make($request->all(),[
                'date'=>'required|date',
                'image_Ecg'=>'required|array',
                'result_Ecg'=>'required'
               ]);
               if($validate2->fails()){
                   return response()->json(['message'=>$validate2->errors()]);
               }
               $Ecg=Ecg::create([
                'medical_examination_id'=>$medical->id,
                'patient_id'=>$patient,
                'doctors_id'=>$doctor->id,
                'date'=>$request->date,
                'image_Ecg'=>$this->uploadeImage($request),
                'result_Ecg'=>$request->result_Ecg
               ]);
            }}

        return response()->json(['message'=>'doctor examination done successfully','examination'=>$examination,'blood pressure'=>$blood,'sugar'=>$sugar,'Eco'=>$eco,'Ecg'=>$Ecg]);
        }
    return response()->json(['message'=>'doctor examination done successfully','examination'=>$examination]);
}
public function getLaboratoryId(){
    $laboratory_anylysis=Laboratory_anylysis::select('name','type','id')->get();
    $array=[];
    foreach($laboratory_anylysis as $lab ){
        $array=array('id'=>$lab->id,'name'=>$lab->name,'type'=>$lab->type);
    }
    return response()->json(['message'=>'all laboratory_anylysis','anylysis'=>$array]);
}
public function add_operation(Request $request){
    $doctor=Auth::guard('doctor')->user();
    $validate=Validator::make($request->all(),[
        'name_operation'=>'required',
        'type_operation'=>'required',
        'price_operation'=>'required',
    ]);
    if($validate->fails()){
     return response()->json(['message'=>$validate->errors()]);
    }
    $operation=Operations::create([
        'doctors_id'=>$doctor->id,
        'name_operation'=>$request->name_operation,
        'type_operation'=>$request->type_operation,
        'price_operation'=>$request->price_operation
    ]);
    return response()->json(['message'=>'operation added successfully','operation'=>$operation]);
}
public function uploadeImage(Request $request)
{
    $validator = Validator::make($request->all(), [
        'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048', // Adding a max size for the image
    ]);
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }
    $image = $request->file('image');
    $name = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
    $path = $image->storeAs('public/images', $name);
    return  Storage::url($path);
}
}

