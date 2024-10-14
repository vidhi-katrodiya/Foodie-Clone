<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Token;
use App\Models\Product;
use App\Models\Lang_core;
use App\Models\FileMeta;
Use Image;
use Hash;
class NotificationController extends Controller {
  

    public function index(){
      $lang = Lang_core::all();
      $getprdouct = Product::where("status",'1')->where("is_deleted",'0')->get();
      foreach ($getprdouct as $k) {
            $getlang =  FileMeta::where("model_id",$k->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get("locale"))->first();
             $k->name = isset($getlang->meta_value)?$getlang->meta_value:'';
      }
    	return view("admin.notification")->with("lang",$lang)->with("prdouct",$getprdouct);
    }

    public function notificationTable(){
    	 $notification =Notification::all();
            return DataTables::of($notification)
                ->editColumn('id', function ($notification) {
                   return $notification->id;
                })
                ->editColumn('msg', function ($notification) {
                   return $notification->msg;
                }) 
              
                           
            ->make(true);
    }

    public function addsendnotification(Request $request){
    	$setting=Setting::find(1);
      if($request->get("type")==0){
          if ($files = $request->file('image')) {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/notification/';
                $picture = "notification_".time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('image')->move($destinationPath, $picture);
                $img_url =$picture;
            }
            else{
                    $img_url="";
            }
      	$android=$this->send_notification_android($setting->android_api_key,$request->get("msg"),0,$img_url);
      	$ios=$this->send_notification_IOS($setting->iphone_api_key,$request->get("msg"),0,$img_url);
      }else{
            $android=$this->send_notification_android($setting->android_api_key,$request->get("product_id"),1,"");
        $ios=$this->send_notification_IOS($setting->iphone_api_key,$request->get("product_id"),1,"");        
      }
    //  exit;
    	if($android==1||$ios==1){
    		$store=new Notification();
        if($request->get("type")==0){
            $store->msg=$request->get("msg");
        }else{
            $store->msg=$request->get("product_id");
        }    		
        $store->type = $request->get("type");
    		$store->save();
    		Session::flash('message',__('messages.success_notification')); 
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
    	}
    	else{
    		Session::flash('message',__('messages.error_notification')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
    	}

    }
     public function send_notification_android($key,$msg,$type,$img_url){

        $getuser=Token::where("type",1)->get();
        if(count($getuser)!=0){               
               $reg_id = array();
               foreach($getuser as $gt){
                   $reg_id[]=$gt->token;
               }
               $regIdChunk=array_chunk($reg_id,1000);
               $response=array();
               foreach ($regIdChunk as $k) {
                       $registrationIds =  $k; 
                       if($type==1){ //product

                         $getprdouct = Product::find($msg);
                          if($getprdouct){
                              $getlang =  FileMeta::where("model_id",$getprdouct->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get("locale"))->first();
                              $getprdouct->name = isset($getlang->meta_value)?$getlang->meta_value:'';
                              $message = array(
                                'message' => $getprdouct->name,
                                'key'=>'normal',
                                'title' => __('messages.site_name'),
                                'image'=>asset('public/upload/product').'/'.$getprdouct->basic_image,
                                'itemId'=>$getprdouct->id
                              );
                          }
                          
                       }else{ //normal
                          $message = array(
                            'message' => $msg,
                            'key'=>'normal',
                            'title' => __('messages.order_status'),
                            'image' => asset('public/upload/notification').'/'.$img_url
                            );
                       }
                       
                      
                       $fields = array(
                          'registration_ids'  => $registrationIds,
                          'data'              => $message
                       );
        
                       $url = 'https://fcm.googleapis.com/fcm/send';
                       $headers = array(
                         'Authorization: key='.$key,// . $api_key,
                         'Content-Type: application/json'
                       );
                      $json =  json_encode($fields);   
                      try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
                            $result = curl_exec($ch);  
              
                            if ($result === FALSE){
                               die('Curl failed: ' . curl_error($ch));
                            }     
                           curl_close($ch);
//echo "<pre>";print_r($result);exit;
                           $response[]=json_decode($result,true);
                      } catch (\Exception $e) {
                       
                      }
                }
               $succ=0;
               foreach ($response as $k) {
                  $succ=$succ+$k['success'];
               }
              if($succ>0)
              {
                   return 1;
              }
              else
              {
                  return 0;
              }
        }
        return 0;
   }
   public function send_notification_IOS($key,$msg,$type,$img_url){
      $getuser=Token::where("type",2)->get();
         if(count($getuser)!=0){               
               $reg_id = array();
               $response=array();
               foreach($getuser as $gt){
                   $reg_id[]=$gt->token;
               }
               $regIdChunk=array_chunk($reg_id,1000);
               foreach ($regIdChunk as $k) {
                        $registrationIds =  $k;
                         if($type==1){ //product
                            $getprdouct = Product::find($msg);
                            if($getprdouct){
                                $getlang =  FileMeta::where("model_id",$getprdouct->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get("locale"))->first();
                                $getprdouct->name = isset($getlang->meta_value)?$getlang->meta_value:'';
                                $message = array(
                                  'body' => $getprdouct->name,
                                  'key'=>'normal',
                                  'title' => __('messages.site_name'),
                                  'image'=>asset('public/upload/product').'/'.$getprdouct->basic_image,
                                  'itemId'=>$getprdouct->id,
                                  'vibrate'   => 1,
                                  'sound'     => 1,
                                );
                            }
                         }else{ //normal
                            $message = array(
                               'body'  => $msg,
                               'title'     => __('messages.notification'),
                               'vibrate'   => 1,
                               'sound'     => 1,
                               'key'=>'normal',
                               'image' => asset('public/upload/notification').'/'.$img_url
                           );
                         }
                        
                       $fields = array(
                          'registration_ids'  => $registrationIds,
                          'data'              => $message
                       );
        
                       $url = 'https://fcm.googleapis.com/fcm/send';
                       $headers = array(
                         'Authorization: key='.$key,// . $api_key,
                         'Content-Type: application/json'
                       );
                      $json =  json_encode($fields);   
                       try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
                            $result = curl_exec($ch);  
              
                            if ($result === FALSE){
                               die('Curl failed: ' . curl_error($ch));
                            }     
                           curl_close($ch);
                           $response[]=json_decode($result,true);
                      } catch (\Exception $e) {
                       
                      }
               }
               $succ=0;
               foreach ($response as $k) {
                  $succ=$succ+$k['success'];
               }
              if($succ>0)
              {
                   return 1;
              }
              else
               {
                  return 0;
               }
        }
        return 0;
   }
}