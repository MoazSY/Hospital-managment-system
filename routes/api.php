<?php

use App\Http\Controllers\HospitalManager;
use App\Http\Controllers\Reseption_employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register_Hospital_manager',[HospitalManager::class,'Register']);
Route::post('login1',[HospitalManager::class,'login']);
Route::middleware('Hospital_manager')->group(function(){

Route::post('add_doctor',[HospitalManager::class,'add_doctor']);
Route::post('delete_doctor/{doctor}',[HospitalManager::class,'delete_doctor']);//
Route::post('add_section_operation',[HospitalManager::class,'add_section_operation']);
Route::post('delete_section_operation/{section}',[HospitalManager::class,'delete_section_operation']);//
Route::post('update_section_operation/{section}',[HospitalManager::class,'update_section_operation']);//
Route::post('add_nurse',[HospitalManager::class,'add_nurse']);
Route::post('deleteNurse/{nurse}',[HospitalManager::class,'deleteNurse']);//
Route::post('add_laboratory',[HospitalManager::class,'add_laboratory']);
Route::post('deleteLaboratory/{lab}',[HospitalManager::class,'deleteLaboratory']);//
Route::post('add_accounter',[HospitalManager::class,'add_accounter']);
Route::post('deleteAccounter/{acc}',[HospitalManager::class,'deleteAccounter']);//
Route::post('add_operation_rooms',[HospitalManager::class,'add_operation_rooms']);
Route::post('delete_operation_room/{Oproom}',[HospitalManager::class,'delete_operation_room']);//
Route::post('update_operation_room/{Oproom}',[HospitalManager::class,'update_operation_room']);//
Route::post('add_reseption_employee',[HospitalManager::class,'add_reseption_employee']);
Route::post('delete_employee_reseption/{employee}',[HospitalManager::class,'delete_employee_reseption']);//
Route::post('add_radiation_section',[HospitalManager::class,'add_radiation_section']);
Route::post('delete_radiation_section/{section}',[HospitalManager::class,'delete_radiation_section']);//
Route::post('update_radiation_section/{section}',[HospitalManager::class,'update_radiation_section']);//
Route::post('add_magnitic_section',[HospitalManager::class,'add_magnitic_section']);
Route::post('delete_magnitic_section/{section}',[HospitalManager::class,'delete_magnitic_section']);//
Route::post('update_magnitic_section/{section}',[HospitalManager::class,'update_magnitic_section']);//
Route::post('add_laboratory_section',[HospitalManager::class,'add_laboratory_section']);
Route::post('update_laboratory_section/{section}',[HospitalManager::class,'update_laboratory_section']);//
Route::post('delete_laboratory_section/{section}',[HospitalManager::class,'delete_laboratory_section']);//
Route::post('add_consumer_employee',[HospitalManager::class,'add_consumer_employee']);
Route::post('delete_consumer_employee/{employee}',[HospitalManager::class,'delete_consumer_employee']);//
Route::get('show_operation_secion',[HospitalManager::class,'operation_secion']);//
Route::get('getDoctors',[HospitalManager::class,'getDoctor']);//
Route::get('getLaboratory',[HospitalManager::class,'getLaboratory']);//
Route::post('add_medical_clinic',[HospitalManager::class,'add_medical_clinic']);//
Route::get('get_medical_clinic',[HospitalManager::class,'get_medical_clinic']);//
Route::post('add_doctor_clinic',[HospitalManager::class,'add_doctor_clinic']);//
Route::post('delete_medical_clinic/{medical}',[HospitalManager::class,'delete_medical_clinic']);//
Route::post('update_medical_clinic/{medical}',[HospitalManager::class,'update_medical_clinic']);//
Route::post('update_medical_clinic_doctor/{medical_doctor}',[HospitalManager::class,'update_medical_clinic_doctor']);//
Route::post('add_operation_section_doctor',[HospitalManager::class,'add_operation_section_doctor']);//
Route::post('update_operation_section_doctor/{doctorOperation}',[HospitalManager::class,'update_operation_section_doctor']);//
Route::post('add_nurse_section',[HospitalManager::class,'add_nurse_section']);//
Route::post('update_nurse_section/{nurseSection}',[HospitalManager::class,'update_nurse_section']);//
});

Route::middleware('Employee_reseption')->group(function () {
Route::post('update_employee/{employee}',[Reseption_employee::class,'update_employee_reseption']);
Route::post('Register_patients_visit',[Reseption_employee::class,'Register_patients_visit']);
Route::post('search_file',[Reseption_employee::class,'search_file']);
Route::post('show_patient_file/{patient}',[Reseption_employee::class,'show_patient_file']);
Route::post('add_patient_visit/{patient}',[Reseption_employee::class,'add_patient_visit']);
Route::get('getDoctor',[Reseption_employee::class,'getDoctor']);
Route::get('get_section_id',[Reseption_employee::class,'get_section_id']);
Route::get('show_queue',[Reseption_employee::class,'show_queue']);
Route::post('delete_from_queue/{lineQ}',[Reseption_employee::class,'delete_from_queue']);
Route::post('insert_to_queue/{patient}',[Reseption_employee::class,'insert_to_queue']);
Route::get('show_available_rooms/{section}',[Reseption_employee::class,'show_available_rooms']);
Route::post('input_patient_Room/{patient}/{room}',[Reseption_employee::class,'input_patient_Room']);
Route::get('getLaboratory',[Reseption_employee::class,'getLaboratory']);

});


