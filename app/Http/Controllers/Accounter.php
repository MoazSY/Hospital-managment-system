<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Accounter as ModelsAccounter;
use App\Models\Consumers;
use App\Models\Drugs_supplies;
use App\Models\Laboratory_anylysis;
use App\Models\Medical_clinic;
use App\Models\medical_clinic_doctor;
use App\Models\Operation_rooms;
use App\Models\Operations;
use App\Models\Patient;
use App\Models\Patient_bill;
use App\Models\Patient_ready_accounting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Accounter extends Controller
{
    public function profile(){
        $Accounter=Auth::guard('accounter')->user();
        $profile=ModelsAccounter::where('id','=',$Accounter->id)->first();
        return response()->json(['message'=>'Accounter profile','profile'=>$profile]);
    }

    public function update_profile(Request $request, StoreUserRequest $requestuser){
        $Accounter=Auth::guard('accounter')->user();
        ModelsAccounter::where('id','=',$Accounter->id)->update($request->only(
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
        $Accounter=ModelsAccounter::where('id','=',$Accounter->id)->first();
        $Accounter->image=$this->uploadeImage($request);
        $Accounter->save();
        }
        if($request['userName']){
            ModelsAccounter::where('id','=',$Accounter->id)->update($requestuser->only('userName'));
        }
        if($request->password){
            $validate=Validator::make($request->all(),['password'=>'required|min:8']);
            if($validate->fails()){
                return response()->json(['message'=>$validate->errors()]);
            }
            $Accounter=ModelsAccounter::where('id','=',$Accounter->id)->first();
            $Accounter->password=Hash::make($request->password);
            $Accounter->save();
        }
        $Accounter=ModelsAccounter::where('id','=',$Accounter->id)->first();
        return response()->json(['message'=>'profile updated successfully','updeted'=>$Accounter]);
    }

    public function get_price_drug(Drugs_supplies $drug){
        return response()->json(['message'=>'price drug','price'=>$drug->price]);
    }
    public function get_patient_ready_consumer(){
        $Patient_consumers=Patient_ready_accounting::where('accounter_id','=',Auth::guard('accounter')->id)->get();
        if($Patient_consumers->isEmpty()){
            // drugs
            return response()->json(['message'=>'all patient ready for accounting','patients'=>$Patient_consumers]);
        }
        return response()->json(['message'=>'not found any patient for accounting',404]);
    }
    public function calc_consumers_cost(Patient_ready_accounting $patient_consumers){
        $cost=0;
        $consumers=$patient_consumers->consumers_id;
        foreach($consumers as $id){
            $Consumers=Consumers::where('id','=',$id)->first();
            $supplies=Drugs_supplies::where('id','=',$Consumers->drugs_supplies_id)->first();
            $cost+=$supplies->price;
        }
        $patient_consumers->accounting=true;
        $patient_consumers->save();
        return response()->json(['message'=>'all cost from consumers','consumers costs'=>$cost]);
    }
    // stay price on operation room يوجد ثغرة هي انه يتم حساب كافة الفواتير في كل مرة اي لم يتم تعيين ان فاتورة معينة قد تم دفعها
    public function calc_other_cost(Patient $patient){
        $patient_cost=Patient::with([
            'radiology_report',
            'imaging_report',
            'result_laboratory',
            'doctor_examination',
            'medical_operation',
            'consumers',
            'room_stay'
        ])->find($patient->id);
        $price_radiology_report=0;
        $price_magnitic_report=0;
        $price_laboratory_analysis=0;
        $price_examination=0;
        $price_operation=0;
        $stay_room_price=0;
        $stay_room=[];
        $Allconsumers=[];
        $operations=[];
            $Allexamination=[];
        $laboratory_analysis=[];
        $radiology_report=[];
        $magnitic_report=[];

        if($patient_cost->radiology_report()->exists()){
          foreach($patient_cost->radiology_report as $report){
            $price_radiology_report+=$report->price;
            $radiology_report=array(
                'radiology_report'=>$report
            );
          }
        }
        if($patient_cost->imaging_report()->exists()){
            foreach($patient_cost->imaging_report as $report){
               $price_magnitic_report+=$report->price;
               $magnitic_report=array('magnitic_report'=>$report);
            }
        }
        if($patient_cost->result_laboratory()->exists()){
            foreach($patient_cost->result_laboratory as $result){
                $analysis=Laboratory_anylysis::where('id','=',$result->laboratory_anylysis_id)->first();
                $price_laboratory_analysis+=$analysis->price;
                $laboratory_analysis=array('laboratory_analysis'=>$result);
            }
        }
        if($patient_cost->doctor_examination()->exists()){
            foreach($patient_cost->doctor_examination as $examination){
                $medical_clinic=Medical_clinic::where('id','=',$examination->section_id)->first();
                $medical_clinic_doctor=medical_clinic_doctor::where('medical_clinic_id','=',$medical_clinic->id)->first();
                $price_examination+=$medical_clinic_doctor->price;
                $Allexamination=array('examination'=>$examination);
            }
        }
        if($patient_cost->medical_operation()->exists()){
            foreach($patient_cost->medical_operation as $medicaloperation){
                $operation=Operations::where('id','=',$medicaloperation->operations_id)->first();
                $price_operation+=$operation->price;
                $operations=array('operations'=>$medicaloperation);
            }
        }
        //// هنا يوجد مشكلة في الحساب
                if($patient_cost->room_stay()->exists()){
                    foreach($patient_cost->room_stay as $room_stay){
                $date1 = Carbon::createFromFormat('Y-m-d H:i', '{$room_stay->enter_date} {$room_stay->enter_time}');
                $date2 = Carbon::createFromFormat('Y-m-d H:i', '{$room_stay->out_date} {$room_stay->out_time}');
                $diffInHours = $date1->diffInHours($date2);
                $operation_room=Operation_rooms::where('id','=',$room_stay->operation_rooms_id)->first();
                $stay_room_price+=$operation_room->hour_price_stay*$diffInHours;
                $stay_room=array('room_stay'=>$room_stay);
                    }
                }

                return response()->json(['message'=>'all medical patient costs in hospital',
                'radiology report'=>['radiology report'=>$radiology_report,'cost'=>$price_radiology_report],
                'magnitic report'=>['magnitic report'=>$magnitic_report,'cost'=>$price_magnitic_report],
                'result_laboratory'=>['result_laboratory'=>$laboratory_analysis,'cost'=>$price_laboratory_analysis],
                'doctor_examination'=>['doctor_examination'=>$Allexamination,'cost'=>$price_examination],
                'medical_operation'=>['medical_operation'=>$operations,'cost'=>$price_operation],
                'room_stay'=>['room_stay'=>$stay_room ,'cost'=>$stay_room_price]
                ]);

    }

    public function show_patient_consumers(Patient $patient){
        $supplies=[];
        $consumers=Patient_ready_accounting::where('patient_id','=',$patient->id)->where('accounting','=',true)->first();
        $drugs=$consumers->consumers_id;
        foreach($drugs as $id){
            $Consumers=Consumers::where('id','=',$id)->first();
            $supplies=Drugs_supplies::where('id','=',$Consumers->drugs_supplies_id)->first();
            $supplies=array('supplies'=>$supplies);
        }
        return response()->json(['message'=>'all patient consumers','supplies'=>$supplies]);
    }

    public function create_bill(Request $request ,Patient $patient){
        $validate=Validator::make($request->all(),[
            // 'accounter_id',
            'consumers_price'=>'required',
            'stay_price'=>'required',
            'operation_price'=>'required',
            'radiology_report_price'=>'required',
            'magnitic_report_price'=>'required',
            'laboratory_analysis_price'=>'required',
            'doctor_examination_price'=>'required',
            // 'patient_id',
            'total_bill'=>'required'
        ]);
        $patient_bill=Patient_bill::create([
                        'accounter_id'=>Auth::guard('accounter')->user()->id,
                        'consumers_price'=>$request->consumers_price,
                        'stay_price'=>$request->stay_price,
                        'operation_price'=>$request->operation_price,
                        'radiology_report_price'=>$request->radiology_report_price,
                        'magnitic_report_price'=>$request->magnitic_report_price,
                        'laboratory_analysis_price'=>$request->laboratory_analysis_price,
                        'doctor_examination_price'=>$request->doctor_examination_price,
                        'patient_id'=>$patient->id,
                        'total_bill'=>$request->total_bill
        ]);
        return response()->json(['message'=>'patient bill for all consumers and other costs','bill'=>$patient_bill]);
    }
    public function update_patient_bill(Request $request ,Patient $patient){
        $update=Patient_bill::where('patient_id','=',$patient->id)->update($request->all());
        return response()->json(['message'=>' patient bill is updated successfully','bill'=>$update]);
    }
    public function search_patient(Request $request){
        $query=Patient::query();
        if($request->id_file){
            $query->where('id_file','=',$request->id_file);
        }
        if($request->name){
            $query->where('name','=',$request->name);
        }
     $result=$query->get();
     if($result->isEmpty()){
        return response()->json(['message'=>'not found patient',404]);
    }
    return response()->json(['message'=>'patient files','files'=>$result]);
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
