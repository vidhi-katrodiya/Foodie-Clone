<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Lang_core;
use App\Models\FileMeta;
use App\Models\City;
use Session;
use DataTables;
use Hash;
use Auth;
class CityController extends Controller {
  
     public function showcity(){
          $lang = Lang_core::all();
          return view("admin.city.default")->with("lang",$lang);
     }
     
     public function Citydatatable(){
          $citydata =City::orderBy('id','DESC')->where("is_delete",'0')->get();
         return DataTables::of($citydata)
            ->editColumn('id', function ($citydata) {
                return $citydata->id;
            })
            ->editColumn('name', function ($citydata) {
                $getlang = FileMeta::where("model_id",$citydata->id)->where("lang",Session::get('locale'))->where("model_name","City")->where("meta_key","name")->first();
                return isset($getlang)?$getlang->meta_value:'';
            })           
            ->editColumn('action', function ($citydata) {
                 
                 $deleteuser=url('admin/deletecity',array('id'=>$citydata->id));
                 $return = '<a onclick="editcity('.$citydata->id.')"   rel="tooltip" class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editcategory"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';
                 return $return;              
            })           
            ->make(true);
     }
     
      public function addcity(Request $request){
       $store=new City();
       $store->save();
        $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"City");
        }
       Session::flash('message',__('messages.City Add Successfully')); 
       Session::flash('alert-class', 'alert-success');
       return redirect()->back();
    }
    
    public function getcitybyid($id){
        $arr = array();
      $arr['id'] = $id;
     $language =Lang_core::all();  
          foreach ($language as $k) {         
                $getmeta = Filemeta::where("model_id",$id)->where("model_name","City")->where("meta_key","name")->where("lang",$k->code)->first();
                if($getmeta){
                   $arr["name_".$k->code] = $getmeta->meta_value;
                }
          }    
        $data = array("data"=>$arr,"language"=>$language);
        return json_encode($data);
       return $data->name;
    }
    
    public function deletecity($id){
        $data=City::find($id);
        if($data){
            $data->is_delete='1';
            $data->save();
        }
       Session::flash('message',__('messages.City Delete Successfully')); 
       Session::flash('alert-class', 'alert-success');
       return redirect()->back();
    }
    
      public function updatecity(Request $request){
       $store=City::find($request->get("id"));       
       $store->save();
        $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"City");
        }
       Session::flash('message',__('messages.City Update Successfully')); 
       Session::flash('alert-class', 'alert-success');
       return redirect()->back();
    }

}