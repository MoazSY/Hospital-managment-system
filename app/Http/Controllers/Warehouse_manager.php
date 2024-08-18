<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Drugs_supplies;
use App\Models\Warehouse_manager as ModelsWarehouse_manager;
use App\Models\Pharmatical_warehouse;
use App\Models\Request_medical_supplies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Warehouse_manager extends Controller
{
    public function view_profile(){
    $warehouse=Auth::guard('warehouse_manager')->user();
    $profile=ModelsWarehouse_manager::with(['pharmatical_warehouse'])->find($warehouse->id);
    return response()->json(['message'=>'warehouse manager profile','profile'=>$profile]);
    }
    public function update_profile(Request $request, StoreUserRequest $requestuser){
        $profile=Auth::guard('warehouse_manager')->user();
        ModelsWarehouse_manager::where('id','=',$profile->id)->update($request->only(
            'name',
            // 'image',
            'birthdate',
            'about_him',
            'phoneNumber',
            // 'userName',
            // 'password',
            'email'
        ));
if($request->hasFile('image')){
    $update=ModelsWarehouse_manager::where('id','=',$profile->id)->first();
    $update->image=$this->uploadeImage($request);
    $update->save();
}
if($request->userName){
ModelsWarehouse_manager::where('id','=',$profile->id)->update($requestuser->only('userName'));
}
if($request->password){
    $validate=Validator::make($request->all(),['password'=>'required|min:8']);
    if($validate->fails()){
        return response()->json(['message'=>$validate->errors()]);
    }
    $update=ModelsWarehouse_manager::where('id','=',$profile->id)->first();
    $update->password=Hash::make($request->password);
    $update->save();
}
$update=ModelsWarehouse_manager::where('id','=',$profile->id)->first();
return response()->json(['message'=>'profile updated successfully','updeted'=>$update]);
    }
    public function import_drugs(Request $request){
        $warehouse_manager=Auth::guard('warehouse_manager')->user();
        $id_pharmatical_warehouse=Pharmatical_warehouse::where('warehouse_manager_id','=',$warehouse_manager->id)->first()->id;
        $validate=Validator::make($request->all([
            // 'pharmatical_warehouse_id',
            'name'=>'required',
            'quentity'=>'required',
            'category'=>'required',
            'price'=>'required',
            'manufacture_company'=>'required'
        ]));
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors(),404]);
        }
        $drugsExist=Drugs_supplies::where('pharmatical_warehouse_id','=',$id_pharmatical_warehouse)->where('name','=',$request->name)
        ->where('category','=',$request->category)->where('manufacture_company','=',$request->manufacture_company)->first();
        if($drugsExist){
            if(!$drugsExist->price==$request->price){
              $drugsExist->update($request->only('price'));
              $drugsExist->save();
            }
            if(!$drugsExist->quentity==$request->quentity){
                $drugsExist->quentity+=$request->quentity;
                $drugsExist->save();
            }
            return response()->json(['message'=>'drugs exist on system','drugs'=>$drugsExist]);
        }else{
            $drugs=Drugs_supplies::create([
                'pharmatical_warehouse_id'=>$id_pharmatical_warehouse,
                'name'=>$request->name,
                'quentity'=>$request->quentity,
                'category'=>$request->category,
                'price'=>$request->price,
                'manufacture_company'=>$request->manufacture_company
                ]);
                return response()->json(['message'=>'supplies are imported successfully','Drugs'=>$drugs]);
        }}
    public function export_drugs(Request_medical_supplies $requests){
    $request_supplies=Request_medical_supplies::where('id','=',$requests->id)->first();
        $drugs=Drugs_supplies::where('id','=',$requests->drugs_supplies_id)->first();
        if($drugs){
            $drugs->quentity-=$requests->quentity;
            $drugs->status_request=true;
            $drugs->save();
            return response()->json(['message'=>'supplies are exported','drugs in warehouse'=>$drugs]);
        }
        return response()->json(['message'=>'drugs not found']);
    }
    public function request_supplies(){
        $request_supplies=Request_medical_supplies::where('status_request','=',false)->get();
        return response()->json(['message'=>'all supplies requested','request_supplies'=>$request_supplies]);
    }
    public function search_drugs(Request $request){
        $manager=Auth::guard('warehouse_manager')->user();
        $Pharmatical_warehouse=Pharmatical_warehouse::where('warehouse_manager_id','=',$manager->id)->first();
        $query=Drugs_supplies::query();
        if($request->name){
            $query->where('pharmatical_warehouse_id','=',$Pharmatical_warehouse->id)->where('name','=',$request->name)->first();
        }
        if($request->category){
            $query->where('pharmatical_warehouse_id','=',$Pharmatical_warehouse->id)->where('category','=',$request->category)->first();
        }
        if($request->manufacture_company){
         $query->where('pharmatical_warehouse_id','=',$Pharmatical_warehouse->id)->where('manufacture_company','=',$request->manufacture_company)->first();
        }
        if($query){
        return response()->json(['message'=>'result drugs','drugs'=>$query]);
        }
        return response()->json(['message'=>'drugs not found']);
    }
    public function get_all_drugs(){
        $manager=Auth::guard('warehouse_manager')->user();
        $Pharmatical_warehouse=Pharmatical_warehouse::where('warehouse_manager_id','=',$manager->id)->first();
        $drugs=Drugs_supplies::where('pharmatical_warehouse_id','=',$Pharmatical_warehouse->id)->get();
        return response()->json(['message'=>'all drugs in warehouse','drugs'=>$drugs]);
    }
    public function update_drug(Request $request,Drugs_supplies $drug){
        $manager=Auth::guard('warehouse_manager')->user();
        $Pharmatical_warehouse=Pharmatical_warehouse::where('warehouse_manager_id','=',$manager->id)->first();
       $drug=Drugs_supplies::where('id','=',$drug->id)->where('pharmatical_warehouse_id','=',$Pharmatical_warehouse->id)->update($request->all(),[
                        'name',
                        'quentity',
                        'category',
                        'price',
                        'manufacture_company'
        ]);
        return response()->json(['message'=>'updated drug','drug'=>$drug]);
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
