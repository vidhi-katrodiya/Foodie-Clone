<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use App\Models\Taxes;
use App\Models\Country;
use App\Models\Setting;
use App\Models\Translation;
use App\Models\Lang_core;
use App\Models\FileMeta;
use Artisan;
use DataTables;
Use Image;
use DateTimeZone;
use DateTime;
use Hash;
class TaxesController extends Controller {
      public function __construct() {
         parent::callschedule();
    }
   public function showtaxes(){
      $lang = Lang_core::all();
      return view("admin.localization.taxes_default")->with("lang",$lang);
   }

   public function taxesdatatable(){
      $taxes =Taxes::orderBy('id','DESC')->get();
            return DataTables::of($taxes)
                ->editColumn('id', function ($taxes) {
                   return $taxes->id;
                })
                ->editColumn('tax_class', function ($taxes) {
                    $getlang = FileMeta::where("model_id",$taxes->id)->where("lang",Session::get('locale'))->where("model_name","Taxes")->where("meta_key","tax_name")->first();
                     return isset($getlang)?$getlang->meta_value:'';
                }) 
                 ->editColumn('billing_no', function ($taxes) {
                    if($taxes->base_on==1){
                        return __('messages.billing_address');
                    }
                    else{
                        return __('messages.shipping_address');
                    }
                }) 
                 ->editColumn('rate', function ($taxes) {
                   return $taxes->rate."%";
                })        
                ->editColumn('action', function ($taxes) {
                     $edit=url('admin/edittaxes',array('id'=>$taxes->id));
                     return '<a href="'.$edit.'" rel="tooltip" title="active" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-edit f-s-25" style="font-size: x-large;"></i></a>';      
                })           
            ->make(true);
   }

   public function addtaxes(){
      $lang = Lang_core::all();
      return view("admin.localization.addtaxes")->with("lang",$lang);
   }
    
   public function storetaxes(Request $request){
         $store=new Taxes();
         $store->base_on=$request->get("based_on");
         $store->rate=$request->get("rate");
         $store->save();
          $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"tax_name",$request->get("tax_name_".$k->code),"Taxes");
        }
         Session::flash('message',__('messages_error_success.tax_add_success')); 
         Session::flash('alert-class', 'alert-success');
         return redirect("admin/taxes");
   } 

   public function edittaxes($id){
      $tax=Taxes::find($id);  
      $lang = Lang_core::all();   
      foreach ($lang as $k) {
          $getmeta = FileMeta::where("model_id",$id)->where("model_name","Taxes")->where("meta_key","tax_name")->where("lang",$k->code)->first();
            $name = "tax_name_".$k->code;
                if($getmeta){
                   $tax->$name = $getmeta->meta_value;
                }else{
                     $tax->$name = "";
                }
       } 

       

      return view("admin.localization.edittax")->with("taxes_data",$tax)->with("lang",$lang);
   }

   public function updatetaxdata(Request $request){
        $store=Taxes::find($request->get("id"));
        $store->base_on=$request->get("based_on");
        $store->rate=$request->get("rate");
         $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"tax_name",$request->get("tax_name_".$k->code),"Taxes");
        }
        $store->save();
        Session::flash('message',__('messages_error_success.tax_update_sucess')); 
        Session::flash('alert-class', 'alert-success');
        return redirect("admin/taxes");
   }

   public function showtranslations(){
        Artisan::call('translations:import');
        $totalrow=Translation::all();
        $lang = Lang_core::all();
       return view("admin.localization.translation")->with("totalrow",count($totalrow))->with("lang",$lang);
   }

   public function updatetranslation(Request $request){
       $store=Translation::find($request->get("id"));
       $store->value=$request->get("value");
       $store->save();
       Artisan::call('translations:export {group}', ['group'=>$store->group]);       
       return "done";
   }

   public function translationdatatable(){
        $lang =Translation::orderBy('id','DESC')->get();
            return DataTables::of($lang)
                ->editColumn('id', function ($lang) {
                   return $lang->id;
                })
                ->editColumn('key', function ($lang) {
                   return $lang->key;
                }) 
                 ->editColumn('value', function ($lang) {
                   return $lang->value.",".$lang->id;
                })  
            ->make(true);
   }


   public function getdatatranslation($id){
     $data=Translation::find($id);
     return json_encode($data);
   }
}