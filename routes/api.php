<?php

use App\Http\Controllers\HospitalManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register_Hospital_manager',[HospitalManager::class,'Register']);
Route::post('login',[HospitalManager::class,'login']);
Route::post('add_doctor',[HospitalManager::class,'add_doctor'])->middleware('Hospital_manager');
Route::post('add_section_operation',[HospitalManager::class,'add_section_operation'])->middleware('Hospital_manager');
Route::post('add_nurse',[HospitalManager::class,'add_nurse'])->middleware('Hospital_manager');
Route::post('add_laboratory',[HospitalManager::class,'add_laboratory'])->middleware('Hospital_manager');
Route::post('add_accounter',[HospitalManager::class,'add_accounter'])->middleware('Hospital_manager');
Route::post('add_operation_rooms',[HospitalManager::class,'add_operation_rooms'])->middleware('Hospital_manager');
Route::post('add_reseption_employee',[HospitalManager::class,'add_reseption_employee'])->middleware('Hospital_manager');
Route::post('add_radiation_section',[HospitalManager::class,'add_radiation_section'])->middleware('Hospital_manager');
Route::post('add_magnitic_section',[HospitalManager::class,'add_magnitic_section'])->middleware('Hospital_manager');
Route::post('add_consumer_employee',[HospitalManager::class,'add_consumer_employee'])->middleware('Hospital_manager');
Route::get('show_operation_secion',[HospitalManager::class,'operation_secion'])->middleware('Hospital_manager');
Route::get('getDoctor',[HospitalManager::class,'getDoctor'])->middleware('Hospital_manager');
Route::get('getLaboratory',[HospitalManager::class,'getLaboratory'])->middleware('Hospital_manager');



