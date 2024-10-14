<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\Banner;
use App\Models\Categories;
use App\Models\Newsletter;
use App\Models\Setting;
use App\Models\Lang_core;
use App\Models\FileMeta;
use Image;
use Hash;
use Mail;
use Sentinel;
use Session;
use DataTables;

class BannerController extends Controller {
       public function __construct() {
         parent::callschedule();
    }
     public function showbanner(){
         $banner=Banner::all();
         $subcategory=Categories::where("parent_category",'!=',0)->where('is_delete','0')->get();
         foreach ($subcategory as $k) {
            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
            $k->name = isset($getlang)?$getlang->meta_value:'';
         }
         $img1="demo.jpg";
         $img2="demo-1.jpg";
         $img3="demo-1.jpg";
         if(isset($banner[0]->Image)){
            $img1=$banner[0]->Image;
         }
         if(isset($banner[1]->Image)){
            $img2=$banner[1]->Image;
         }
          if(isset($banner[2]->Image)){
            $img3=$banner[2]->Image;
         }

         $lang = Lang_core::all();
         foreach ($banner as $b) {
            foreach ($lang as $k) {
                $title = "title_".$k->code;
                $getmeta = FileMeta::where("model_id",$b->id)->where("model_name","Banner")->where("meta_key","title")->where("lang",$k->code)->first();
                $b->$title = isset($getmeta->meta_value)?$getmeta->meta_value:'';
                 $title = "subtitle_".$k->code;
                $getmeta = FileMeta::where("model_id",$b->id)->where("model_name","Banner")->where("meta_key","subtitle")->where("lang",$k->code)->first();
                $b->$title = isset($getmeta->meta_value)?$getmeta->meta_value:'';
               
         }
         }

    
        return view("admin.banner.default")->with("img1",$img1)->with("img2",$img2)->with("img3",$img3)->with("subcategory",$subcategory)->with("bannerdata",$banner)->with("lang",$lang);
     }
    
   public function updatebanner(Request $request){
     // dd($request->all());
        if ($files = $request->file('photo1')) {
                $file = $request->file('photo1');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/banner/image/';
                $picture = "banner_".time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('photo1')->move($destinationPath, $picture);
                $img_url =$picture;
                $photo =Banner::find(1);
              if(empty($photo)){
                 $photo=new Banner();
              }
              $photo->Image = $img_url;
              $photo->subcategory=$request->get("subcategory1");
              $photo->position='1';
              $photo->save();
              $language =Lang_core::all();
              foreach ($language as $k) {
                  $this->file_meta_update_payment_key($photo->id,$k->code,"title",$request->get("title_".$k->code),"Banner");
                  $this->file_meta_update_payment_key($photo->id,$k->code,"subtitle",$request->get("subtitle_".$k->code),"Banner");

              }
       }
       else{
              $photo =Banner::find(1);
              if(empty($photo)){
                 $photo=new Banner();
              }
              $photo->title=$request->get("title1");
              $photo->position='1';
              $photo->save();
               $language =Lang_core::all();
              foreach ($language as $k) {
                  $this->file_meta_update_payment_key($photo->id,$k->code,"title",$request->get("title_".$k->code),"Banner");
                  $this->file_meta_update_payment_key($photo->id,$k->code,"subtitle",$request->get("subtitle_".$k->code),"Banner");

              }
       }
       if ($files = $request->file('photo2')) {
                $file = $request->file('photo2');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/banner/image/';
                $picture = "banner_".time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('photo2')->move($destinationPath, $picture);
                $img_url =$picture;
              $photo =Banner::find(2);
              if(empty($photo)){
                 $photo=new Banner();
              }
              $photo->Image = $img_url;
              $photo->subcategory=$request->get("subcategory2");
              $photo->position='2';
              $photo->save();
               $language =Lang_core::all();
              foreach ($language as $k) {
                  $this->file_meta_update_payment_key($photo->id,$k->code,"title",$request->get("title_".$k->code),"Banner");
                  $this->file_meta_update_payment_key($photo->id,$k->code,"subtitle",$request->get("subtitle_".$k->code),"Banner");

              }
       }
       else{
              $photo =Banner::find(2);
              if(empty($photo)){
                 $photo=new Banner();
              }
              $photo->title=$request->get("title2");
              $photo->position='2';
              $photo->save();
               $language =Lang_core::all();
              foreach ($language as $k) {
                  $this->file_meta_update_payment_key($photo->id,$k->code,"title",$request->get("title_".$k->code),"Banner");
                  $this->file_meta_update_payment_key($photo->id,$k->code,"subtitle",$request->get("subtitle_".$k->code),"Banner");

              }
       }
       if ($files = $request->file('photo3')) {
                $file = $request->file('photo3');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/banner/image/';
                $picture = "banner_".time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('photo3')->move($destinationPath, $picture);
                $img_url =$picture;
              $photo =Banner::find(3);
              if(empty($photo)){
                 $photo=new Banner();
              }
              $photo->Image = $img_url;
              $photo->subcategory=$request->get("subcategory3");
              $photo->position='3';
              $photo->save();
               $language =Lang_core::all();
              foreach ($language as $k) {
                  $this->file_meta_update_payment_key($photo->id,$k->code,"title",$request->get("title_".$k->code),"Banner");
                  $this->file_meta_update_payment_key($photo->id,$k->code,"subtitle",$request->get("subtitle_".$k->code),"Banner");

              }
       }
       else{
              $photo =Banner::find(3);
              if(empty($photo)){
                 $photo=new Banner();
              }
              $photo->subcategory=$request->get("subcategory3");
              $photo->position='3';
              $photo->save();
               $language =Lang_core::all();
              foreach ($language as $k) {
                  $this->file_meta_update_payment_key($photo->id,$k->code,"title",$request->get("title_".$k->code),"Banner");
                  $this->file_meta_update_payment_key($photo->id,$k->code,"subtitle",$request->get("subtitle_".$k->code),"Banner");

              }
       }
       $image = Banner::all();
         return redirect("admin/banner");
     }
     
     public function shownews(){
          $lang = Lang_core::all();
          return view("admin.news")->with("lang",$lang);
     }
     
     public function sendnews(Request $request){
          $msg=$request->get("news");
          $getall=Newsletter::all();
          $setting=Setting::find(1);
          foreach($getall as $g){
              $data=array();
              $data['email']=$g->email;
              $data['msg']=$msg;
              /* $to = $g->email;
              $subject = "news";
              $txt = $msg;
              $headers = "From:".Session::get("email")."";
              mail($to,$subject,$txt,$headers);*/
                try {
                      $result=Mail::send('email.news', ['user' => $data], function($message) use ($data){
                         $message->to($data['email'],'customer')->subject(__('messages.site_name'));
                      });
            
               } catch (\Exception $e) {
               }
        
          }
       Session::flash('message',__('messages.News Send Successfully'));
       Session::flash('alert-class', 'alert-success');
       return redirect()->back();
     }
  
  
}
