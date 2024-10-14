<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Sentinel;
use Validator;
use App\Models\User;
use App\Models\Delivery;
use App\Models\Token;
use App\Models\Deliveryboy_Document;
use App\Models\OrderData;
use App\Models\Order;
use App\Models\Addresses;
use App\Models\Setting;
use App\Models\DeliveryboyReject;
use DateTimeZone;
use DateTime;
use Session;
use Image;
use Mail;
use App;
use DB;
class DeliveryBoyController extends Controller {
  
    public function postregister(Request $request){ 
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'name' => 'required',
                      'email' => 'required|unique:delivery_boy',
                      'password' => 'required',
                      'phone'=>'required',
                      "token"=>"required",
                      "lang"=>"required"
                    ]; 
                    
                    
            $messages = array(
                      'name.required' => "name is required",
                      'email.unique' => 'Email Already exist',
                      'email.required' => "email are required",
                      'password.required' => "password is required",
                      'phone.required'=>"phone is required",
                      'token.required'=>"Token is required",
                      "lang.required"=>"lang is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $user =Delivery::where("email",$request->get("email"))->first();
                 if(empty($user)){
                           $otp = random_int(100000, 999999);
                           $user=new Delivery();
                            $user->name=$request->get("name");
                            $user->email=$request->get("email");
                            $user->password=$request->get("password");
                            $user->mobile_no=$request->get("phone");
                            $user->otp = $otp;
                            $user->save();
                            $gettoken=Token::where("token",$request->get("token"))->update(["delivery_boyid"=>$user->id]);
                            $msg = __("messages.Your Otp")." :".$otp;
                            $result = $this->sendotpmsg($request->get("phone"),$msg);
                            if($result==1){
                                $response = array("status" =>1, "msg" => __("messages.Register Successfully"),"data"=>$user);
                            }                          
                            else{
                              $response = array("status" =>1, "msg" => __("messages.Register Successfully"),"data"=>$user);
                            } 
                            
                 }
                 else{
                  $response = array("status" =>0, "msg" => __("messages.Something wrong"));
                 }                
           }
           return $response;
    }
    
     public function delete_delivery_boy(Request $request){
           $response = array("status" => "0", "register" => "Validation error");
           $rules = [
                      'id' => 'required'        
                    ];                    
            $messages = array(
                      'id.required' => "user_id is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['msg'] = $message;
            } else {
                      App::setlocale($request->get("lang"));
                      session()->put('locale', $request->get("lang"));
                      $id = $request->get("id");
                      $user=Delivery::find($id);
                      if($user){
                          $user->is_deleted = '1';
                          $user->save();
                      }
                      
                      $response = array("status" =>1, "msg" => __('messages_error_success.delivery_del'));
           }
           return Response::json(array("data"=>$response));
    }


    public function show_editprofile(Request $request){
      //  dd($request->all());
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'name' => 'required',
                      'email' => 'required',
                      'password' => 'required',
                      'phone'=>'required',
                      "lang"=>"required",
                      "vehicle_no"=>"required",
                      "vehicle_type"=>"required",
                      "id"=>"required"
                    ]; 
                    
                    
            $messages = array(
                      'name.required' => "name is required",
                      'email.required' => "email are required",
                      'password.required' => "password is required",
                      'phone.required'=>"phone is required",
                      "lang.required"=>"lang is required",
                      "vehicle_no.required"=>"vehicle_no is required",
                      "vehicle_type.required"=>"vehicle_type is required",
                      "lat.required"=>"lat is required",
                      "long.required"=>"long is required",
                      "id.required"=>"id is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $user =Delivery::find($request->get("id"));
                 if($user){
                              if ($request->hasFile('file')) 
                              {
                                 $file = $request->file('file');
                                 $filename = $file->getClientOriginalName();
                                 $extension = $file->getClientOriginalExtension() ?: 'png';
                                 $folderName = '/upload/images/Delivery';
                                 $picture = "delivery_".time() . '.' . $extension;
                                 $destinationPath = public_path() . $folderName;
                                 $request->file('file')->move($destinationPath, $picture);
                                  $image_path = public_path() ."/upload/images/Delivery/".$user->profile;
                                        if(file_exists($image_path)&&$user->profile!="") {
                                            try {
                                                 unlink($image_path);
                                            }
                                            catch(Exception $e) {
                                              
                                            }                        
                                        }
                                 $user->profile=$picture;
                             }
                            $user->name=$request->get("name");
                            $user->email=$request->get("email");
                            $user->password=$request->get("password");
                            $user->mobile_no=$request->get("phone");
                            $user->vehicle_no = $request->get("vehicle_no");
                            $user->vehicle_type = $request->get("vehicle_type");
                           // $user->lat = $request->get("lat");
                           // $user->long = $request->get("long");
                            $user->save();
                           
                                $response = array("status" =>1, "msg" => __("messages.Profile Update Successfully"),"data"=>$user);
                            
                 }
                 else{
                  $response = array("status" =>0, "msg" => __("messages.Something wrong"));
                 }                
           }
           return $response;
    }
    
    public function today_orders(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required'
                    ]; 
                    
                    
            $messages = array(
                      'id.required' => "id is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $data = OrderData::select('id','order_id','orderplace_datetime','total_amount','delivery_charges','assign_datetime')->where("assign_id",$request->get("id"))->Where('assign_datetime', 'like', '%' . date('d-m-Y') . '%')->paginate(15);  
                
                $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                if($data){
                    $total_charges = 0;
                    $total_order = 0;
                    $arr = array();
                    foreach($data as $d){
                        $getorder = Order::find($d->order_id);
                        $getuser = User::find($getorder->user_id);
                        $getaddress = Addresses::where("user_id",$getorder->user_id)->first();
                        $d->username = $getuser->first_name;
                        $d->address = $getaddress;
                        $d->status = "7";
                        $total_charges = $total_charges + $d->delivery_charges;
                        $total_order = $total_order+1;
                        
                        $d->payment_method = $getorder->payment_method;
                        unset($getuser->delivery_charges);
                    }
                     $setting = Setting::find(1);
                    $currency = explode("-",$setting->default_currency);
                    $arr = array("data"=>$data,"currency"=>isset($currency[1])?trim($currency[1]):'');
                    
                    
                    $response = array("status" =>1, "msg" => __("messages.Order Histroy"),"data"=>$arr);  
                }else{
                     $response = array("status" =>0, "msg" => __("messages.No Order Histroy"));    
                }
                
                
           }
           return $response;
    }
    
    public function total_amount_order_list(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required'
                    ]; 
                    
                    
            $messages = array(
                      'id.required' => "id is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $data = Order::select('id','order_no','orderplace_datetime','total','delivery_charge')->where("assign_id",$request->get("id"))->whereMonth('created_at',date('m'))->orderby('total','DESC')->paginate(15);  
                $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                if($data){
                    $total_charges = 0;
                    $total_order = 0;
                    $arr = array();
                    foreach($data as $d){
                        $getorder = Order::find($d->id);
                        $getuser = User::find($getorder->user_id);
                        $getaddress = Addresses::where("user_id",$getorder->user_id)->first();
                        $d->username = $getuser->first_name;
                        $d->address = $getaddress;
                        $d->status =$getorder->status;
                        $total_charges = $total_charges + $d->delivery_charges;
                        $total_order = $total_order+1;
                        
                        $d->payment_method = $getorder->payment_method;
                        unset($getuser->delivery_charges);
                    }
                    $setting = Setting::find(1);
                    $currency = explode("-",$setting->default_currency);
                    $arr = array("data"=>$data,"currency"=>isset($currency[1])?trim($currency[1]):'');
                
                    
                    $response = array("status" =>1, "msg" => __("messages.Order Histroy"),"data"=>$arr);  
                }else{
                     $response = array("status" =>0, "msg" => __("messages.No Order Histroy"));    
                }
                
                
           }
           return $response;
    }
    
    public function order_history(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required'
                    ]; 
                    
                    
            $messages = array(
                      'id.required' => "id is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $data = Order::select('id','order_no','orderplace_datetime','total','delivery_charge','status')->where("assign_id",$request->get("id"))->orderby('id','DESC')->paginate(15);  
                $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                if($data){
                    $total_charges = 0;
                    $total_order = 0;
                    $arr = array();
                    foreach($data as $d){
                        $getorder = Order::find($d->id);
                        $getuser = User::find($getorder->user_id);
                        $getaddress = Addresses::where("id",$getorder->user_address_id)->first();
                        $d->username = $getuser->first_name;
                        $d->address = $getaddress;
                      //  $d->status = "7";
                        $total_charges = $total_charges + $d->delivery_charges;
                        $total_order = $total_order+1;
                        
                        $d->payment_method = $getorder->payment_method;
                        unset($getuser->delivery_charges);
                    }
                    $complete_amount = Order::where("assign_id",$request->get("id"))->whereMonth('created_at',date('m'))->sum('delivery_charge');
                    
                    $getlastorder = Order::where("assign_id",$request->get("id"))->where("status",'7')->orderby('id','DESC')->first();
                    $response = array("status" =>1, "msg" => __("messages.Order Histroy"),"data"=>array("order"=>$data,"total_earning"=>$complete_amount,"complete_order"=>$total_order,"currency"=>isset($currency[1])?trim($currency[1]):'',"last_order_complete_time"=>isset($getlastorder->complete_datetime)?$getlastorder->complete_datetime:''));  
                }else{
                     $response = array("status" =>0, "msg" => __("messages.No Order Histroy"));    
                }
                
                
           }
           return $response;
    }
    
    public function show_order_list(Request $request){
      // die("AA");
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required',
                      'status'=>'required'
                    ]; 
                    
                    
            $messages = array(
                      'id.required' => "id is required",
                      'status.required'=>"status is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                if($request->get("status")==1){ // pending
                    $data = Order::select('id','order_no','orderplace_datetime','total','delivery_charge','status','assigned')->where("assign_id",$request->get("id"))->where("status","!=",'7')->orderby('id','DESC')->paginate(15); 
                }
                if($request->get("status")==2){ // complete
                    $data = Order::select('id','order_no','orderplace_datetime','total','delivery_charge','status','assigned')->where("assign_id",$request->get("id"))->where("status","7")->orderby('id','DESC')->paginate(15); 
                }
                if($request->get("status")==3){ // picked
                    $data = Order::select('id','order_no','orderplace_datetime','total','delivery_charge','status','assigned')->where("assign_id",$request->get("id"))->where("status","5")->orderby('id','DESC')->paginate(15);  
                }
                 
                        $user =Delivery::find($request->get("id"));   
                           
                $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                if(count($data)>0){
                    $total_charges = 0;
                    $total_order = 0;
                    $arr = array();
                    foreach($data as $d){
                        $getorder = Order::find($d->id);
                        
                        $getuser = User::find($getorder->user_id);
                       
                        // $getaddress = Addresses::where("user_id",$getorder->user_id)->first();
                        $getaddress = Addresses::where("id",$getorder->user_address_id)->first();
                        $d->username = $getuser->first_name;
                        $d->address = $getaddress;
                      //  $d->status = "7";
                        $total_charges = $total_charges + $d->delivery_charge;
                        $total_order = $total_order+1;
                        
                        $d->payment_method = $getorder->payment_method;
                        unset($getuser->delivery_charges);
                    }
                    
                    $today_order = count(Order::where("assign_id",$request->get("id"))->whereDate('created_at',date('Y-m-d'))->get());
                    $complete_amount = Order::where("assign_id",$request->get("id"))->whereMonth('created_at',date('m'))->sum('delivery_charge');
                    
                    $response = array("status" =>1, "msg" => __("messages.order List"),"data"=>array("order"=>$data,"today_order"=>$today_order,"total_earning"=>$complete_amount,"currency"=>isset($currency[1])?trim($currency[1]):''),"presence"=>$user->attendance);  
                }else{
                     $response = array("status" =>0, "msg" => __("messages.No Order List Found"),"presence"=>$user->attendance);    
                }
                
                
           }
           return $response;
    }
    
    public function show_order_action(Request $request){
          $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required',
                      'status'=>'required'
                    ]; 
                    
                    
            $messages = array(
                      'id.required' => "id is required",
                      'status.required'=>"status is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $orderdata = Order::find($request->get("id"));
                $setting=Setting::find(1);
                if($orderdata){ // sucess
                    $getorder = Order::find($orderdata->id);
                    $msg = "";
                    if($request->get("status")=='1'){ // accept
                        $msg=__('messages.the order has been accepted by the delivery boy');
                        $android=$this->send_notification_android($setting->android_api_key,$getorder->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$getorder->user_id,$msg,$orderdata->id);
                         $android=$this->send_notification_android($setting->android_api_key,$orderdata->seller_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->seller_id,$msg,$orderdata->id);
                        $orderdata->assigned = '1';
                        $orderdata->save();
                    }
                    if($request->get("status")=='2'){
                        $orderdata->assigned = '0';
                        $orderdata->assign_datetime = '';
                        $orderdata->assign_id = '';
                        $orderdata->save();
                        $store = new DeliveryboyReject();
                        $store->assign_id = $request->get("assign_id");
                        $store->order_id = $request->get("id");
                        $store->save();
                        $msg=__('messages.Reject Order');
                    }
                    if($request->get("status")=='5'){
                        $msg=__('messages.the order has been picked by the delivery boy');
                        $android=$this->send_notification_android($setting->android_api_key,$getorder->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$getorder->user_id,$msg,$orderdata->id);
                         $android=$this->send_notification_android($setting->android_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $orderdata->status = '5';
                        $orderdata->pickup_datetime = $this->getsitedate();
                        $orderdata->save();
                    }
                    if($request->get("status")=='6'){
                        $msg="The order has been out for delivery by the delivery boy";
                        $android=$this->send_notification_android($setting->android_api_key,$getorder->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$getorder->user_id,$msg,$orderdata->id);
                         $android=$this->send_notification_android($setting->android_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $orderdata->status = '6';
                        $orderdata->out_for_delivery_datetime = $this->getsitedate();
                        $orderdata->save();
                    }
                    if($request->get("status")=='7'){
                        $msg=__('messages.the order has been delivered');
                        $android=$this->send_notification_android($setting->android_api_key,$getorder->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$getorder->user_id,$msg,$orderdata->id);
                         $android=$this->send_notification_android($setting->android_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $orderdata->complete_datetime = $this->getsitedate();
                        $orderdata->status = '7';
                        $orderdata->save();
                    }
                    $response = array("status" =>1, "msg" => $msg); 
                    
                }else{ // no
                    $response = array("status" =>0, "msg" => __("messages.No Data Found"));  
                }
                
           }
           return $response;
    }
    
    public function getsitedate(){
            $setting=Setting::find(1);
            $date_zone=array();
            $timezone=$this->generate_timezone_list();
                foreach($timezone as $key=>$value){
                      if($setting->default_timezone==$key){
                              $date_zone=$value;
                      }
                }
            date_default_timezone_set($date_zone);   
            return date('d-m-Y h:i:s');                    
     }
      static public function generate_timezone_list(){
          static $regions = array(
                     DateTimeZone::AFRICA,
                     DateTimeZone::AMERICA,
                     DateTimeZone::ANTARCTICA,
                     DateTimeZone::ASIA,
                     DateTimeZone::ATLANTIC,
                     DateTimeZone::AUSTRALIA,
                     DateTimeZone::EUROPE,
                     DateTimeZone::INDIAN,
                     DateTimeZone::PACIFIC,
                 );
                  $timezones = array();
                  foreach($regions as $region) {
                            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
                  }

                  $timezone_offsets = array();
                  foreach($timezones as $timezone) {
                       $tz = new DateTimeZone($timezone);
                       $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
                  }
                 asort($timezone_offsets);
                 $timezone_list = array();
    
                 foreach($timezone_offsets as $timezone=>$offset){
                          $offset_prefix = $offset < 0 ? '-' : '+';
                          $offset_formatted = gmdate('H:i', abs($offset));
                          $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
                          $timezone_list[] = "$timezone";
                 }

                 return $timezone_list;
                ob_end_flush();
       }

       public function gettimezonename($timezone_id){
              $getall=$this->generate_timezone_list();
              foreach ($getall as $k=>$val) {
                 if($k==$timezone_id){
                     return $val;
                 }
              }
       }
       
        public function send_notification_android($key,$user_id,$msg,$id){
          $getuser=Token::where("type",1)->where("user_id",$user_id)->get();
          if(count($getuser)!=0){               
               $reg_id = array();
               foreach($getuser as $gt){
                   $reg_id[]=$gt->token;
               }
               $registrationIds =  $reg_id;    
               $message = array(
                    'message' => $msg,
                    'key'=>'order',
                    'title' => __('messages.order_status'),
                    'order_id'=>$id
                );
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
                   $response=json_decode($result,true); 
                  } catch (\Exception $e) {
                    return 0;
                 }
             if(isset($response)&&$response['success']>0)
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
   public function send_notification_IOS($key,$user_id,$msg,$id){
      $getuser=Token::where("type",2)->where("user_id",$user_id)->get();
         if(count($getuser)!=0){               
               $reg_id = array();
               foreach($getuser as $gt){
                   $reg_id[]=$gt->token;
               }
                $registrationIds =  $reg_id;    
                $message = array(
                   'body'  => $msg,
                   'title'     => __('messages.notification'),
                   'vibrate'   => 1,
                   'sound'     => 1,
                   'key'=>'order',
                   'order_id'=>$id
               );
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
                   $response=json_decode($result,true); 
                  } catch (\Exception $e) {
                    return 0;
                 }
             if(isset($response)&&$response['success']>0)
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
    
    public function saveDocument(Request $request){
     //   dd($request->all());
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'deliveryboy_id' => 'required',
                      'document' => 'required',
                      'name' => 'required',
                      'id'=>'required',
                      'doc_id'=>'required'
                    ]; 
                    
                    
            $messages = array(
                      'deliveryboy_id.required' => "deliveryboy_id is required",
                      'document.required' => "document are required",
                      'name.required' => "name is required",
                      'id.required'=>"id is required",
                      "doc_id.required"=>"doc_id is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                if($request->get("id")==0){
                    $store = new Deliveryboy_Document();
                }else{
                    $store = Deliveryboy_Document::find($request->get("id"));
                    if(empty($store)){
                        $response = array("status" =>0, "msg" => __("messages.Something wrong"));
                        return $response;
                    }
                }
                $store->deliveryboy_id = $request->get("deliveryboy_id");
                $store->name = $request->get("name");
                $store->doc_id = $request->get("doc_id");
               // $store->status = 0;
                if ($request->hasFile('document')) 
                {
                    $arr = array();
                    foreach ($request->file('document') as $k) { 
                            $file = $k;
                            $filename = $file->getClientOriginalName();
                            $extension = $file->getClientOriginalExtension() ?: 'png';
                            $folderName = '/upload/images/Delivery/doc';
                            $picture = "delivery_".time() . '.' . $extension;
                            $destinationPath = public_path() . $folderName;
                            $k->move($destinationPath, $picture);
                            $arr[] = $picture;
                    }
                    $store->document= implode(",",$arr);
                    $store->status = 0;
                }
                $store->save();
                $response = array("status" =>1, "msg" => __("messages.Document Upload Successfully"),"data"=>$store);            
           }
           return $response;
    }
    
    public function get_document_status(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'deliveryboy_id' => 'required'
                    ]; 
                    
                    
            $messages = array(
                      'deliveryboy_id.required' => "deliveryboy_id is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                
                    $store = Deliveryboy_Document::where("deliveryboy_id",$request->get("deliveryboy_id"))->get();
                    if(count($store)>0){
                        $arr = array();
                        $arr['doc_1']=Deliveryboy_Document::where("deliveryboy_id",$request->get("deliveryboy_id"))->where("doc_id",1)->where("status",'1')->first()?1:0;
                        $arr['doc_2']=Deliveryboy_Document::where("deliveryboy_id",$request->get("deliveryboy_id"))->where("doc_id",2)->where("status",'1')->first()?1:0;
                        $arr['doc_3']=Deliveryboy_Document::where("deliveryboy_id",$request->get("deliveryboy_id"))->where("doc_id",3)->where("status",'1')->first()?1:0;
                        $arr['doc_4']=Deliveryboy_Document::where("deliveryboy_id",$request->get("deliveryboy_id"))->where("doc_id",4)->where("status",'1')->first()?1:0;
                        $arr['doc_5']=Deliveryboy_Document::where("deliveryboy_id",$request->get("deliveryboy_id"))->where("doc_id",5)->where("status",'1')->first()?1:0;
                        $response = array("status" =>1, "msg" => __("messages.Document Found"),"data"=>$arr);  
                    }else{
                         $response = array("status" =>0, "msg" => __("messages.Document Not Found"));  
                    }
                    
                
                         
           }
           return $response;
    }
    
    
     public function deliveryboy_presence(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
        $rules = [
            'status' => 'required',
            'deliverboy_id' => 'required'
        ];
        $messages = array(
                  'status.required' => "status is required",
                  'deliverboy_id.required'=>'deliverboy_id is required'
        );

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
        } else {
              $update=Delivery::where("id",$request->get("deliverboy_id"))->update(["attendance"=>$request->get("status")]);
              if($update){
                 $response['status']="1";
                 $response['msg']=$request->get("status");
              }
              else{
                $response['status']="0";
                $response['msg']="Something went wrong";
              }             
          
        }
        return json_encode($response);
   }
   
    
    public function getDocument(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'deliveryboy_id' => 'required',
                      'doc_id' => 'required',
                      'lang'=>'required'
                    ]; 
                    
                    
            $messages = array(
                      'deliveryboy_id.required' => "deliveryboy_id is required",
                      'doc_id.required' => "doc_id are required",
                      'lang.required'=>"lang is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $data = Deliveryboy_Document::where("deliveryboy_id",$request->get("deliveryboy_id"))->where("doc_id",$request->get("doc_id"))->first();
                if($data){
                     $response = array("status" =>1, "msg" => __("messages.Document Get Successfully"),"data"=>$data); 
                }else{
                    $response = array("status" =>0, "msg" => __("messages.Something wrong"));
                }           
           }
           return $response;
    }
    
    public function deleteDocument(Request $request){
         $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required',
                      'lang'=>'required'
                    ]; 
                    
                    
            $messages = array(
                      'id.required' => "id is required",
                      'lang.required'=>"lang is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $data = Deliveryboy_Document::find($request->get("id"));
                if($data){
                     $arr = explode(",",$data->document);
                     foreach($arr as $a){
                         $image_path = public_path() ."/upload/images/Delivery/doc/".$a;
                            if(file_exists($image_path)&&$a!="") {
                                try{
                                         unlink($image_path);
                                    }
                                    catch(\Exception $e)
                                    {
                                        
                                    }
                            }
                     }
                     
                     $data->document = "";
                     $data->status = 0;
                     $data->save();
                     $response = array("status" =>1, "msg" => __("messages.media delete Successfully")); 
                }else{
                    $response = array("status" =>0, "msg" => __("messages.Something wrong"));
                }           
           }
           return $response;
    }
    

     
  
  
  public function Showlogin(Request $request){
        $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'lang'=>'required',
                      'login_type'=>'required',
                      'token'=>'required',
                      'token_type'=>'required'
                    ];
                    
                    if($request->input('login_type')=="1"){
                        $rules['user_id'] = 'required';
                    }
                    
                    
                    if($request->input('login_type')=="4"){
                        $rules['phone'] = 'required';
                        $rules['password'] = 'required';
                    }
            $messages = array(
                      'token.required' => "token is required",
                      'token_type.required' => "token_type is required",
                      'phone.required' => "phone is required",
                      'password.required'=>"password is required",
                      "lang.required"=>"lang is required",
                      "login_type.required"=>"login_type is required",
                      "user_id.required"=>"user_id is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['register'] = $message;
            } else {
                      App::setlocale($request->get("lang"));
                      session()->put('locale', $request->get("lang"));
                     
                      if($request->get("login_type")==1){
                          $user = User::find($request->get("user_id"));
                          if($user){
                              $delivery=Delivery::where("mobile_no",$user->phone)->where("is_deleted",'0')->where("password",$user->password)->first();
                              if($delivery){
                                    $gettoken=Token::where("token",$request->get("token"))->first();
                                      if(!$gettoken){
                                             $store=new Token();
                                             $store->token=$request->get("token");
                                             $store->type=$request->get("token_type");
                                             $store->delivery_boyid=$delivery->id;
                                             $store->save();
                                     }
                                      else{
                                             $gettoken->delivery_boyid=$delivery->id;
                                             $gettoken->save();
                                      }
                                    $response = array("status" =>1, "msg" => __("messages.Login Successfully"),"data"=>$delivery);
                                }else{
                                    
                                     $delivery = new Delivery();
                                     $delivery->name = $user->first_name;
                                     $delivery->email = $user->email;
                                     $delivery->mobile_no = $user->phone;
                                     $delivery->password = $user->password;
                                     $delivery->save();
                                     $gettoken=Token::where("token",$request->get("token"))->first();
                                      if(!$gettoken){
                                             $store=new Token();
                                             $store->token=$request->get("token");
                                             $store->type=$request->get("token_type");
                                             $store->delivery_boyid=$delivery->id;
                                             $store->save();
                                     }
                                      else{
                                             $gettoken->delivery_boyid=$delivery->id;
                                             $gettoken->save();
                                      }
                                    $response = array("status" =>1, "msg" => __("messages.Login Successfully"),"data"=>Delivery::find($delivery->id));
                                }
                          }else{
                               $response = array("status" =>0, "msg" => __("messages.Login Credentials Are Wrong"));
                          }
                          
                      }
                      
                      if($request->get("login_type")==4){
                           $user=Delivery::where("mobile_no",$request->get("phone"))->where("is_deleted",'0')->where("password",$request->get("password"))->first();
                          if($user){
                                $gettoken=Token::where("token",$request->get("token"))->first();
                                  if(!$gettoken){
                                         $store=new Token();
                                         $store->token=$request->get("token");
                                         $store->type=$request->get("token_type");
                                         $store->delivery_boyid=$user->id;
                                         $store->save();
                                 }
                                  else{
                                         $gettoken->delivery_boyid=$user->id;
                                         $gettoken->save();
                                  }
                                $response = array("status" =>1, "msg" => __("messages.Login Successfully"),"data"=>$user);
                            }else{
                               $response = array("status" =>0, "msg" => __("messages.Login Credentials Are Wrong"));
                            }
                      }
                     
               
            }
            return Response::json(array("data"=>$response));
   }


   /*Make me New My Api*/

   public function order_history_by_filter(Request $request)
    {
          $response = array("status" => "0", "msg" => "Validation error");
          $rules = [
                  'id' => 'required',
                  'from_date' => 'required',
                  'to_date' => 'required',
                ]; 
                    
          $messages = array(
                'id.required' => "id is required",
                'from_date.required' => "from_date is required",
                'to_date.required' => "to_date is required",
          );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $from_date=$request->get("from_date");
                $to_date=$request->get("to_date");
                // $atten_dance=Attendance::where('user_id',$id)->where('date', '>=', $first_day_this_month)->where('date', '<=', $last_day_this_month)->get();
                $data = Order::select('id','order_no','orderplace_datetime','total','delivery_charge','status')->where("assign_id",$request->get("id"))->whereDate('orderplace_datetime', '>=', $from_date)->whereDate('orderplace_datetime', '<=', $to_date)->paginate(15);  


                $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                if($data){
                    $total_charges = 0;
                    $total_order = 0;
                    $arr = array();
                    foreach($data as $d){
                        $getorder = Order::find($d->id);
                        $getuser = User::find($getorder->user_id);
                        $getaddress = Addresses::where("id",$getorder->user_address_id)->first();
                        $d->username = $getuser->first_name;
                        $d->address = $getaddress;
                      //  $d->status = "7";
                        $total_charges = $total_charges + $d->delivery_charges;
                       if($d->status==7)
                        {
                          $total_order = $total_order+1;
                        }
                        
                        $d->payment_method = $getorder->payment_method;
                        unset($getuser->delivery_charges);
                    }
                    // $complete_amount = OrderData::where("assign_id",$request->get("id"))->whereMonth('created_at',date('m'))->sum('delivery_charges');
                    $complete_amount = Order::where("assign_id",$request->get("id"))->whereDate('orderplace_datetime', '>=', $from_date)->whereDate('orderplace_datetime', '<=', $to_date)->sum('delivery_charge');
                    if($complete_amount=='0')
                    {
                        $complete_amount='0';
                    }
                    
                    $getlastorder = Order::where("assign_id",$request->get("id"))->where("status",'7')->orderby('id','DESC')->first();
                    $response = array("status" =>1, "msg" => __("messages.Order Histroy"),"data"=>array("order"=>$data,"total_earning"=>$complete_amount,"complete_order"=>$total_order,"currency"=>isset($currency[1])?trim($currency[1]):'',"last_order_complete_time"=>isset($getlastorder->complete_datetime)?$getlastorder->complete_datetime:''));  
                }else{
                     $response = array("status" =>0, "msg" => __("messages.No Order Histroy"));    
                }
                
                
           }
           return $response;
    }
  
 
}
?>

