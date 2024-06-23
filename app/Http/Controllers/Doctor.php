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
use App\Models\Imaging_report;
use App\Models\Radiation_section;
use App\Models\Sugar_blood;
use App\Models\Visit_details;
use App\Models\Radiology_report;
use App\Models\Medical_operation;
use App\Models\Drugs_supplies;
use App\Models\Request_medical_supplies;
use App\Models\Patient_graduation;
use App\Models\Operation_rooms;
use App\Models\Stay_operation_rooms;
use App\Models\Pharmatical_warehouse;
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
        if($request['image']){
          $doctorImage=ModelsDoctor::where('id','=',$doctor->id)->first();
            $doctorImage->image=$this->uploadeImageDoctor($request);
            $doctorImage->save();
        }
        $doctorinfo=ModelsDoctor::where('id','=',$doctor->id)->first();
        return response()->json(['message'=>'doctor updated successfully','doctor'=>$doctorinfo]);
    }
    public function update_clinic(Request $request,medical_clinic_doctor $clinic){
        $doctor=Auth::guard('doctor')->user();
       $update_clinic= medical_clinic_doctor::where('doctors_id','=',$doctor->id)->where('id','=',$clinic->id)->first();
      $update_clinic ->update($request->only('price','start_time','end_time', 'days'));
       return response()->json(['message'=>'medical clinic updated successfully','clinic'=>$update_clinic]);
    }
    public function update_operation_section(Request $request,Doctor_operation_section $section){
        $doctor=Auth::guard('doctor')->user();
        $update_operation_section=Doctor_operation_section::where('doctors_id','=',$doctor->id)->where('id','=',$section->id)->first();
        $update_operation_section->update($request->only('startWorkTime','endWorkTime','days'));
        return response()->json(['message'=>'operation section updated successfully','operation section'=>$update_operation_section]);
    }
    public function profile(){
        $doctorID=Auth::guard('doctor')->user()->id;
        $doctor=ModelsDoctor::where('id','=',$doctorID)->first();

        $doctor = ModelsDoctor::with([
            'medical_clinic_doctor',
            'operation',
            'operation_section',
            'doctor_operation_section'
        ])->find($doctorID);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }
        return response()->json([
            'message' => 'Doctor profile retrieved successfully',
            'profile' => $doctor,
            'medical_clinic' => $doctor->medical_clinic_doctor,
            'operation' => $doctor->operation,
            'operation_section' => $doctor->operation_section,
            'doctor_operation_section' => $doctor->doctor_operation_section
        ]);
    }
    public function routing_section(){
    // Initialize arrays to hold the sections
    $operationSections = [];
    $medicalClinics = [];
    $operationManagementSections = [];
    $radiationSections = [];
    $imagingSections = [];
    // Get the authenticated doctor
    $doctorID = Auth::guard('doctor')->user()->id;
    // Eager load relationships
    $doctor = ModelsDoctor::with([
        'doctor_operation_section',
        'medical_clinic_doctor',
        'operation_section',
        'radiology_section',
        'imaging_section'
    ])->find($doctorID);

    // Check if the doctor was found
    if (!$doctor) {
        return response()->json(['message' => 'Doctor not found'], 404);
    }
        if($doctor->doctor_operation_section()->exists()){
        foreach($doctor->doctor_operation_section as $section){
        $sectionName=Operation_section::where('id','=',$section->operation_sections_id)->first();
        $operationSections[]=[$sectionName->Section_name,'Operation Section'];
        }
        }
        if($doctor->medical_clinic_doctor()->exists()){
            foreach($doctor->medical_clinic_doctor as $medical){
            $medicalName=Medical_clinic::where('id','=',$medical->medical_clinic_id)->first();
                $medicalClinics[]=[$medicalName->name,'Medical clinic'];
            }
        }
        if($doctor->operation_section()->exists()){
    foreach($doctor->operation_section as $section){
       $operationManagementSections[]=[$section->Section_name,'Operation Managment'];
    }
}
    if($doctor->radiology_section()->exists()){
        foreach($doctor->radiology_section as $section){
            $radiationSections[]=[$section->name,'Radiology Section'];
        }
    }
    if($doctor->imaging_section()->exists()){
    foreach($doctor->imaging_section as $section){
        $imagingSections[]=[$section->name,'Imaging Section'];
    }
}
 return response()->json([
        'message' => 'Choose any section you want to move to',
        'operation_section' => $operationSections,
        'medical_clinic' => $medicalClinics,
        'operation_management_section' => $operationManagementSections,
        'radiation_section' => $radiationSections,
        'imaging_section' => $imagingSections
    ]);
    }
    //get section and sectionName from array above
    public function choose_section(Request $request){
        $section=$request->section;
        $sectionName=$request->section_name;
           if($section=='Operation Section'){
            $operationSection=Operation_section::where('Section_name','=',$sectionName)->first();
            $operatioSectionDoctor=Doctor_operation_section::where('operation_sections_id','=',$operationSection->id)->first();
            $operatioSectionDoctor->available=true;
            $operatioSectionDoctor->save();
            return response()->json(['message'=>'doctor available in operation section','operation section'=>$operatioSectionDoctor,'section name'=>$sectionName]);
        }
        if($section=='Medical clinic'){
            $medical_clinic=Medical_clinic::where('name','=',$sectionName)->first();
            $medicalClinic_doctor=medical_clinic_doctor::where('medical_clinic_id','=',$medical_clinic->id)->first();
            $medicalClinic_doctor->doctor_available=true;
            $medicalClinic_doctor->save();
            return response()->json(['message'=>'doctor available in medical clinic','medical clinic'=>$medicalClinic_doctor,'section name'=>$sectionName]);
        }
        if($section=='Operation Managment'){
            $operation=Operation_section::where('Section_name','=',$sectionName)->first();
            $operation->available=true;
            $operation->save();
            return response()->json(['message'=>'doctor available in operation section managment','operation section'=>$operation,'section name'=>$sectionName]);
        }
        if($section=='Radiology Section'){
            $radiation=Radiation_section::where('name','=',$sectionName)->first();
            $radiation->available=true;
            $radiation->save();
            return response()->json(['message'=>'doctor available in radiology section','dadiology section'=>$radiation,'section name'=>$sectionName]);
        }
            if($section=='Imaging Section'){
            $imaging=Magnetic_resonnance_imaging::where('name','=',$sectionName)->first();
            $imaging->available=true;
            $imaging->save();
            return response()->json(['message'=>'doctor available in magnitic section','magnitic section'=>$imaging,'section name'=>$sectionName]);
        }
    }
    public function Pass_Patient(Line_queue $queue){
        $doctorId=Auth::guard('doctor')->user()->id;
        $doctor = ModelsDoctor::with([
            'doctor_operation_section',
            'medical_clinic_doctor',
            'operation_section',
            'radiology_section',
            'imaging_section'
        ])->find($doctorId);
        // Check if the doctor was found
        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }
        if($doctor->medical_clinic_doctor()->exists()){
            $medical=medical_clinic_doctor::where('doctor_available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$medical->medical_clinic_id)->where('section_name','=','Medical clinic')->where('position','=',$queue->position+1)->first();
            if($line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$medical->medical_clinic_id)->where('section_name','=','Medical clinic')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now();
                $visit_details->save();
                Line_queue::create([
                    'patient_id'=>$queue->patient_id,
                    'num_char'=>$queue->num_char,
                    'position'=>$queue->position,
                    'section_name'=>$queue->section_name,
                    'section_id'=>$queue->section_id,
                    'visit_id'=>$queue->visit_id,
                    'wating'=>true
                ]);
                Line_queue::where('section_id','=',$medical->medical_clinic_id)->where('section_name','=','Medical clinic')->where('position','>',$queue->position)->increment('position');
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            Line_queue::create([
                'patient_id'=>$queue->patient_id,
                'num_char'=>$queue->num_char,
                'position'=>$queue->position,
                'section_name'=>$queue->section_name,
                'section_id'=>$queue->section_id,
                'visit_id'=>$queue->visit_id,
                'wating'=>true
            ]);
            Line_queue::where('section_id','=',$medical->medical_clinic_id)->where('section_name','=','Medical clinic')->where('position','>',$queue->position)->increment('position');
            return response()->json(['message'=>'dont found other patients']);
        }
        if($doctor->operation_section()->exists()){
            $operation=Operation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','=',$queue->position+1)->first();
            if($line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now()->format('H:i');
                Line_queue::create([
                    'patient_id'=>$queue->patient_id,
                    'num_char'=>$queue->num_char,
                    'position'=>$queue->position,
                    'section_name'=>$queue->section_name,
                    'section_id'=>$queue->section_id,
                    'visit_id'=>$queue->visit_id,
                    'wating'=>true
                ]);
                Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','>',$queue->position)->increment('position');
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            Line_queue::create([
                'patient_id'=>$queue->patient_id,
                'num_char'=>$queue->num_char,
                'position'=>$queue->position,
                'section_name'=>$queue->section_name,
                'section_id'=>$queue->section_id,
                'visit_id'=>$queue->visit_id,
                'wating'=>true
            ]);
            Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','>',$queue->position)->increment('position');
            return response()->json(['message'=>'dont found other patients']);
        }
        if($doctor->doctor_operation_section()->exists()){
            $operation=Doctor_operation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$operation->operation_sections_id)->where('section_name','=','Operations section')->where('position','=',$queue->position+1)->first();
            if($line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now()->format('H:i');
                Line_queue::create([
                    'patient_id'=>$queue->patient_id,
                    'num_char'=>$queue->num_char,
                    'position'=>$queue->position,
                    'section_name'=>$queue->section_name,
                    'section_id'=>$queue->section_id,
                    'visit_id'=>$queue->visit_id,
                    'wating'=>true
                ]);
                Line_queue::where('section_id','=',$operation->operation_sections_id)->where('section_name','=','Operations section')->where('position','>',$queue->position)->increment('position');
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            Line_queue::create([
                'patient_id'=>$queue->patient_id,
                'num_char'=>$queue->num_char,
                'position'=>$queue->position,
                'section_name'=>$queue->section_name,
                'section_id'=>$queue->section_id,
                'visit_id'=>$queue->visit_id,
                'wating'=>true
            ]);
            Line_queue::where('section_id','=',$operation->operation_sections_id)->where('section_name','=','Operations section')->where('position','>',$queue->position)->increment('position');
            return response()->json(['message'=>'dont found other patients']);
        }
        if($doctor->radiology_section()->exists()){
            $radiation=Radiation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$radiation->id)->where('section_name','=','Radiation section')->where('position','=',$queue->position+1)->first();
            if($line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$radiation->id)->where('section_name','=','Radiation section')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now()->format('H:i');
                Line_queue::create([
                    'patient_id'=>$queue->patient_id,
                    'num_char'=>$queue->num_char,
                    'position'=>$queue->position,
                    'section_name'=>$queue->section_name,
                    'section_id'=>$queue->section_id,
                    'visit_id'=>$queue->visit_id,
                    'wating'=>true
                ]);
                Line_queue::where('section_id','=',$radiation->id)->where('section_name','=','Radiation section')->where('position','>',$queue->position)->increment('position');
            return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            Line_queue::create([
                'patient_id'=>$queue->patient_id,
                'num_char'=>$queue->num_char,
                'position'=>$queue->position,
                'section_name'=>$queue->section_name,
                'section_id'=>$queue->section_id,
                'visit_id'=>$queue->visit_id,
                'wating'=>true
            ]);
            Line_queue::where('section_id','=',$radiation->id)->where('section_name','=','Radiation section')->where('position','>',$queue->position)->increment('position');
            return response()->json(['message'=>'dont found other patients']);

        }
            if($doctor->imaging_section()->exists()){
            $magnitic=Magnetic_resonnance_imaging::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$magnitic->id)->where('section_name','=','Magnitic section')->where('position','=',$queue->position+1)->first();
            if($line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$magnitic->id)->where('section_name','=','Magnitic section')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now()->format('H:i');
                Line_queue::create([
                    'patient_id'=>$queue->patient_id,
                    'num_char'=>$queue->num_char,
                    'position'=>$queue->position,
                    'section_name'=>$queue->section_name,
                    'section_id'=>$queue->section_id,
                    'visit_id'=>$queue->visit_id,
                    'wating'=>true
                ]);
                Line_queue::where('section_id','=',$magnitic->id)->where('section_name','=','Magnitic section')->where('position','>',$queue->position)->increment('position');
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            Line_queue::create([
                'patient_id'=>$queue->patient_id,
                'num_char'=>$queue->num_char,
                'position'=>$queue->position,
                'section_name'=>$queue->section_name,
                'section_id'=>$queue->section_id,
                'visit_id'=>$queue->visit_id,
                'wating'=>true
            ]);
            Line_queue::where('section_id','=',$magnitic->id)->where('section_name','=','Magnitic section')->where('position','>',$queue->position)->increment('position');
            return response()->json(['message'=>'dont found other patients']);
        }
    }
    public function extract_from_queue(){
        $doctor=Auth::guard('doctor')->user();
        $doctor=ModelsDoctor::where('id','=',$doctor->id)->first();
        if($doctor->medical_clinic_doctor()->exists()){
            $medical=medical_clinic_doctor::where('doctor_available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$medical->medical_clinic_id)->where('section_name','=','Medical clinic')->orderBy('position','asc')->first();
            if($line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$medical->medical_clinic_id)->where('section_name','=','Medical clinic')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now();
                $visit_details->save();
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            return response()->json(['message'=>'line queue is empty']);
        }
        if($doctor->operation_section()->exists()){
            $operation=Operation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->orderBy('position','asc')->first();
            if( $line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now();
                $visit_details->save();
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            return response()->json(['nessage'=>'line queue is empty']);
        }
        if($doctor->doctor_operation_section()->exists()){
            $operation=Doctor_operation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$operation->operation_sections_id)->where('section_name','=','Operations section')->orderBy('position','asc')->first();
            if( $line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$operation->id)->where('section_name','=','Operations section')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now();
                $visit_details->save();
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            return response()->json(['message'=>'line queue is empty']);
        }
        if($doctor->radiology_section()->exists()){
            $radiation=Radiation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$radiation->id)->where('section_name','=','Radiation section')->orderBy('position','asc')->first();
            if($line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$radiation->id)->where('section_name','=','Radiation section')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now();
                $visit_details->save();
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            return response()->json(['message'=>'line queue is empty']);
        }
            if($doctor->imaging_section()->exists()){
            $magnitic=Magnetic_resonnance_imaging::where('available','=',true)->where('doctors_id','=',$doctor->id)->first();
            $line_queue=Line_queue::where('section_id','=',$magnitic->id)->where('section_name','=','Magnitic section')->orderBy('position','asc')->first();
            if($line_queue){
                $line_queue->delete();
                $patient=Patient::where('id','=',$line_queue->patient_id)->first();
                Line_queue::where('section_id','=',$magnitic->id)->where('section_name','=','Magnitic section')->where('position','>',$line_queue->position)->decrement('position');
                $visit_details=Visit_details::where('id','=',$line_queue->visit_id)->first();
                $visit_details->enterTime=Carbon::now();
                $visit_details->save();
                return response()->json(['message'=>'get patient from queue','patient'=>$patient,'queue'=>$line_queue]);
            }
            return response()->json(['message'=>'line queue is empty']);
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
$visit->save();
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
    if(!is_null($request->id_medical_examination)){
        $sugar = null;
        $eco = null;
        $Ecg = null;
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
                'image_Ecg'=>'required',
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
public function imaging_report(Request $request,Line_queue $line){
    $validate=Validator::make($request->all(),[
        'name_image'=>'required',
        'image'=>'required|array',
        'price'=>'required',
        'medical_diagnosis'=>'required'
    ]);
if($validate->fails()){
    return response()->json(['message'=>$validate->errors()]);
}
$doctor=Auth::guard('doctor')->user();
$magniticId=Magnetic_resonnance_imaging::where('available','=',true)->where('doctors_id','=',$doctor->id)->first()->id;
$magnitic=Imaging_report::create([
    'name_image'=>$request->name_image,
    'magnetic_resonnance_imaging_id'=>$magniticId,
    'doctors_id'=>$doctor->id,
    'patient_id'=>$line->patient_id,
    'image'=>$this->uploadeImage($request),
    'price'=>$request->price,
    'medical_diagnosis'=>$request->medical_diagnosis
]);
return response()->json(['message'=>$magnitic]);
}
public function Radiology_report(Request $request,Line_queue $line){
    $validate=Validator::make($request->all(),[
        'name_radiology'=>'required',
        // 'radiation_section_id'
        // 'doctors_id',
        // 'patient_id'
        'price'=>'required',
        'image'=>'required|array',
        'medical_diagnosis'=>'required',
        'name_radiology'=>'required'
    ]);
if($validate->fails()){
    return response()->json(['message'=>$validate->errors()]);
}
$doctor=Auth::guard('doctor')->user();
$radiationId=Radiation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first()->id;
$radiation=Radiology_report::create([
    'name_radiology'=>$request->name_radiology,
    'radiation_section_id'=>$radiationId,
    'doctors_id'=>$doctor->id,
    'price'=>$request->price,
    'image'=>$this->uploadeImage($request),
    'medical_diagnosis'=>$request->medical_diagnosis,
    'patient_id'=>$line->patient_id
]);
return response()->json(['message'=>$radiation]);
}
 public function make_operartion(Request $request ,Patient $patient){
    $validate=Validator::make($request->all(),[
        'start_date'=>'required|date',
        'end_date'=>'required|date',
        'start_time'=>'required|date_format:H:i',
        'end_time'=>'required|date_format:H:i',
        'status_operation'=>'required',
        'recomendation'=>'required',
        'id_drugs'=>'required|array'//json convert
    ]);
    if($validate->fails()){
        return response()->json(['message'=>$validate->errors()]);
    }
    $doctor=Auth::guard('doctor')->user();
        $operationId=Doctor_operation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first()->id;
        $medical_operation=Medical_operation::create([
        'operations_id'=>$operationId,
        'patient_id'=>$patient->id,
        'start_date'=>$request->start_date,
        'end_date'=>$request->end_date,
        'start_time'=>$request->start_time,
        'end_time'=>$request->end_time,
        'status_operation'=>$request->status_operation,
        'recomendation'=>$request->recomendation,
        'id_drugs'=>$request->id_drugs,
        ]);
        return response()->json(['message'=>$medical_operation]);
    }
        public function Request_medical_supplies(Request $request , Patient $patient){
        $validate=Validator::make($request->all(),[
        'drugs_supplies_id'=>'required',
        'quentity'=>'required',
        'date'=>'required|date'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $doctor=Auth::guard('doctor')->user();
        $operationId=Doctor_operation_section::where('available','=',true)->where('doctors_id','=',$doctor->id)->first()->id;
        $drugs=Drugs_supplies::where('id','=',$request->drugs_supplies_id)->first();
        if($drugs){
            if($drugs->quentity>=$request->quentity){
                $medical_supplies=Request_medical_supplies::create([
                    'doctor_id'=>$doctor->id,
                    'drugs_supplies_id'=>$request->drugs_supplies_id,
                    'quentity'=>$request->quentity,
                    'operation_sections_id'=>$operationId,
                    'patient_id'=>$patient->id,
                    'date'=>$request->date
                    ]);
                    return response()->json(['message'=>'medical supplies that requested','medical supplies'=>$medical_supplies]);
            }
            else{
                return response()->json(['message'=>'quentity not enough']);
            }
        }else{
            return response()->json(['message'=>'drugs not found']);
        }
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
    public function patient_graduation(Request $request, Patient $patient){
$validate=Validator::make($request->all(),[
    'out_date'=>'required|date',
    'out_time'=>'required|date_format:H:i',
    'recomendation'=>'required',
]);
if($validate->fails()){
    return response()->json(['message'=>$validate->errors()]);
}
 $PatientS=Patient::with([
    'room_stay'
 ])->find($patient->id);

 if(!$PatientS){
    return response()->json(['message'=>'not found',404]);
 }
 $operation=Doctor_operation_section::where('available','=',true)->
 where('doctors_id','=',Auth::guard('doctor')->user()->id)->first();
if($PatientS->room_stay()->exists()){
    foreach($PatientS->room_stay as $room){
        $operation_room=Operation_rooms::where('operation_sections_id','=',$operation->operation_sections_id)
        ->where('id','=',$room->operation_rooms_id)->where('available','=',false)->first();
        if($operation_room){
        $RoomNumber=$operation_room->numberRoom;
        $operation_room=Operation_rooms::where('operation_sections_id','=',$operation->operation_sections_id)
        ->where('id','=',$room->operation_rooms_id)->where('available','=',false)->where('numberRoom','=',$RoomNumber)->first();
        $room->out_date=$request->out_date;
        $room->out_time=$request->out_time;
        $room->save();
        $operation_room->available=true;
        $operation_room->save();
        $graduation=Patient_graduation::create([
            'doctors_id'=>Auth::guard('doctor')->user()->id,
            'patient_id'=>$patient->id,
            'out_date'=>$request->out_date,
            'out_time'=>$request->out_time,
            'recomendation'=>$request->recomendation,
         ]);
            return response()->json(['message'=>'patient qraduation','graduation'=>$graduation,'room stay'=>$PatientS->room_stay]);
        }
    }
}
return response()->json(['message'=>'not found',404]);
    }
    public function get_patient_file(Patient $patient){
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
    public function uploadeImageDoctor(Request $request)
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
public function uploadeImage(Request $request){
    //image image_Ecg,image_eco,
    $uploadedImages = [];
    if ($request->hasFile('image_Ecg')) {
        $images = $request->file('image_Ecg');
        foreach ($images as $image) {
            $validator = Validator::make(['image_Ecg' => $image], [
                'image_Ecg' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            $name = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/images', $name);
            array_push($uploadedImages, Storage::url($path));
        }
    }
    elseif($request->hasFile('image_eco')){
        $images = $request->file('image_eco');
        foreach ($images as $image) {
            $validator = Validator::make(['image_eco' => $image], [
                'image_eco' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            $name = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/images', $name);
            array_push($uploadedImages, Storage::url($path));
        }
    }
    elseif($request->hasFile('image')){
        $images = $request->file('image');
        foreach ($images as $image) {
            $validator = Validator::make(['image' => $image], [
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            $name = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/images', $name);
            array_push($uploadedImages, Storage::url($path));
        }
    }
     else{
        return response()->json(['error' => 'No image uploaded.'], 400);
    }
    return $uploadedImages;
}
}

