<?php

namespace App\Http\Controllers;
ini_set('max_execution_time', 500);
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Models\Lang_core;
use App\Models\Translation;
use Hash;
use Artisan;
class LanguageController extends Controller {
  
    public function show_language(){
        $lang = Lang_core::all();
        return view("admin.language.default")->with("lang",$lang);
    }

    public function language_datatable(){
        $user =Lang_core::all();
      //  echo "<pre>";print_r($user);exit;
        return DataTables::of($user)
            ->editColumn('id', function ($user) {
                return $user->id;
            })
            ->editColumn('name', function ($user) {
                return $user->name;
            })
            ->editColumn('code', function ($user) {
                return $user->code;
            })
             ->editColumn('image', function ($user) {
                return asset('public/upload/language_image').'/'.$user->image;
            })
            ->editColumn('is_rtl', function ($user) {
                if($user->is_rtl=='1'){
                    return __("messages.Yes");
                }else{
                    return __("messages.No");
                }
            }) 
            ->editColumn('action', function ($user) {  
               $accept=url("admin/translation",array('code'=>$user->code));  
               $delete=url('admin/deletelang',array('id'=>$user->id));
                $return = '<a href="'.$accept.'" rel="tooltip" title="" class="btn btn-sm btn-success btnorder" data-original-title="Remove"  style="color:white !important;margin-right:10px">'.__('messages.View').'</a>
                <a onclick="delete_record(' . "'" . $delete. "'" . ')" rel="tooltip" title="Delete Category" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';  
                return $return;       
            })
           
            ->make(true);
    }
    
    public function deletelang($id){
          $get=Lang_core::find($id);
          $get->delete();
          Session::flash('message',__('messages_error_success.language_delete_successfully')); 
          Session::flash('alert-class', 'alert-success');
          return redirect()->back();
    }


   public function add_language(Request $request){
        $savelanguage = new Lang_core();
        $savelanguage->name = $request->get("name");
        $savelanguage->code = $request->get("code");
        $savelanguage->is_rtl = $request->get("is_rtl");
         if ($files = $request->file('file')) {
                $file = $request->file('file');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/language_image/';
                $picture = "language_".time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('file')->move($destinationPath, $picture);
                $savelanguage->image =$picture;
            }
        $savelanguage->save();
        $getdatatranslation =  Translation::where("locale","en")->get();
        foreach ($getdatatranslation as $k) {
             $store = new Translation();
             $store->status = $k->status;
             $store->locale = $request->get("code");
             $store->group  = $k->group;
             $store->key = $k->key;
             $store->value = $k->value;
             $store->save();
              Artisan::call('translations:export {group}', ['group'=>$k->group]);      
        }
//Artisan::call('translations:export');
        Session::flash('message',__('messages.Language Add Successfully')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
   }


   public function show_translation($code){
       Artisan::call('translations:import');
        $totalrow=Translation::where("locale",$code)->get();
        $lang = Lang_core::all();
       return view("admin.language.translation")->with("totalrow",count($totalrow))->with("code",$code)->with("lang",$lang);
   }


      public function updatetranslation(Request $request){
       $store=Translation::find($request->get("id"));
       $store->value=$request->get("value");
       $store->save();
       Artisan::call('translations:export {group}', ['group'=>$store->group]);       
       return "done";
   }

   public function translationdatatable($code){
        $lang =Translation::where("locale",$code)->get();
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


