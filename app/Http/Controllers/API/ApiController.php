<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Response;
use Sentinel;
use Validator;
use App\Models\User;
use App\Models\Categories;
use App\Models\CartData;
use App\Models\Brand;
use App\Models\Offer;
use App\Models\Lang_core;
use App\Models\Product;
use App\Models\Seasonaloffer;
use App\Models\Banner;
use App\Models\Deal;
use App\Models\Sepicalcategories;
use App\Models\ContactUs;
use App\Models\Setting;
use App\Models\AttributeSet;
use App\Models\Options;
use App\Models\Optionvalues;
use App\Models\Attributes;
use App\Models\Attributevalues;
use App\Models\Review;
use App\Models\ProductAttributes;
use App\Models\ProductOption;
use App\Models\FileMeta;
use App\Models\OrderData;
use App\Models\Taxes;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\FeatureProduct;
use App\Models\Wishlist;
use App\Models\Delivery;
use App\Models\OrderResponse;
use App\Models\PaymentMethod;
use App\Models\ResetPassword;
use App\Models\QueryAns;
use App\Models\QueryTopic;
use App\Models\Token;
use App\Models\Complain;
use App\Models\Pages;
use App\Models\City;
use App\Models\Addresses;
use App\Models\OrderDataApp;
use App\Models\About;

use DateTimeZone;
use DateTime;
use Session;
use Image;
use Mail;
use App;
use DB;


class ApiController extends Controller {
    public function __construct() {
         parent::callschedule();
    }
    public function userregister(Request $request){ 
       $response = array("status" => "0", "msg" => "Validation error");
       $rules = [
                  'name' => 'required',
                  'email' => 'required|unique:users',
                  'password' => 'required',
                  'phone'=>'required',
                  "token"=>"required",
                  "lang"=>"required",
                ];                    
        $messages = array(
                  'name.required' => "name is required",
                  'email.unique' => 'Email Already exist',
                  'email.required' => "email are required",
                  'password.required' => "password is required",
                  'phone.required'=>"phone is required",
                  'token.required'=>"Token is required",
                  "lang.required"=>"lang is required",
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
            $setting=Setting::find(1);  
            $user =User::where("email",$request->get("email"))->first();
            //echo "<pre>";print_r($user);exit;
             if(empty($user)){
                $otp = random_int(100000, 999999);
                       $user=new User();
                        $user->first_name=$request->get("name");
                        $user->email=$request->get("email");
                        $user->password=$request->get("password");
                        $user->is_email_verified='1';
                        $user->login_type=1;
                        $user->phone=$request->get("phone");
                        $user->user_type="1";                 
                        $user->login_otp = $otp;
                        $user->dob = $request->get("dob");
                        $user->gender = $request->get("gender");
                        $user->save();
                        $gettoken=Token::where("token",$request->get("token"))->update(["user_id"=>$user->id]);
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
    
    public function Showlogin(Request $request){
        $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'login_type' => 'required',
                      'token' => 'required',
                      'token_type'=>'required',
                      'phone' => 'required',
                      'lang'=>'required'  
                    ];
                    if($request->input('login_type')=='1'){
                        $rules['password'] = 'required';
                    }
                    if($request->input('login_type')=='2'||$request->input('login_type')=='3'){
                        $rules['soical_id'] = 'required';
                        $rules['name']='required';
                    }
                   
            $messages = array(
                      'login_type.required' => "login_type is required",
                      'token.required' => "token is required",
                      'token_type.required' => "token_type is required",
                      'phone.required' => "phone is required",
                      'password.required'=>"password is required",
                      "soical_id.required"=>"soical_id is required",
                      "name.required"=>"name is required",
                      "lang.required"=>"lang is required"
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
                      $setting=Setting::find(1);
                      if($request->input('login_type')=='1'){
                          
                      $user=User::where("phone",$request->get("phone"))->where("password",$request->get("password"))->where("is_deleted",0)->first();
     
                      if($user){
                              if($setting->customer_reg_email=='1'&&$user->is_email_verified=='0'){
                                   $response = array("status" =>0, "msg" => __("messages.Please Verified Your Email"));
                              }
                              else{
                              $gettoken=Token::where("token",$request->get("token"))->first();
                              if(!$gettoken){
                                     $store=new Token();
                                     $store->token=$request->get("token");
                                     $store->type=$request->get("token_type");
                                     $store->user_id=$user->id;
                                     $store->save();
                              }
                              else{
                                     $gettoken->user_id=$user->id;
                                     $gettoken->save();
                              }
                                    
                                   if($user->soical_id==null){
                                     $user->soical_id="";
                                   }
                                   if($user->billing_address==null){
                                      $user->billing_address="";
                                   }
                                   if($user->shipping_address==null){
                                      $user->shipping_address="";
                                   }
                                   if($user->profile_pic==null){
                                      $user->profile_pic="";
                                   }
                                   if($user->first_name==null){
                                      $user->first_name="";
                                   }
                                   if($user->address==null){
                                      $user->address="";
                                   }
                                   if($user->phone==null){
                                      $user->phone="";
                                   }
                                   
                                    $otp = random_int(100000, 999999);
                                    $user->login_otp = '';//$otp;
                                    $user->save();
                                    $msg = __("messages.Your Otp")." :".$otp;
                                    $cartdata=CartData::where("user_id",$user->id)->get();
                                        $wishdata=Wishlist::where("user_id",$user->id)->get();
                                        $user->cart=count($cartdata);
                                        $user->totalwish=count($wishdata);
                                   
                                       $response = array("status" =>1, "msg" => __("messages.Login Successfully"),"data"=>$user);
                               }
                              }
                            else{
                                 $response = array("status" =>0, "msg" => __("messages.Login Credentials Are Wrong"));
                            }
                 }
                 if($request->input('login_type')=='2'){
                    $checkuser=User::where("soical_id",$request->get("soical_id"))->first();
                    if($checkuser){
                      $gettoken=Token::where("token",$request->get("token"))->first();
                              if(!$gettoken){
                                     $store=new Token();
                                    $store->token=$request->get("token");
                             $store->type=$request->get("token_type");
                             $store->user_id=$checkuser->id;
                             $store->save();
                              }
                               else{
                                     $gettoken->user_id=$checkuser->id;
                                     $gettoken->save();
                              }
                          
                          if($checkuser->soical_id==null){
                                      $checkuser->soical_id="";
                                   }
                                   if($checkuser->billing_address==null){
                                      $checkuser->billing_address="";
                                   }
                                   if($checkuser->shipping_address==null){
                                      $checkuser->shipping_address="";
                                   }
                                   if($checkuser->profile_pic==null){
                                      $checkuser->profile_pic="";
                                   }
                                   if($checkuser->first_name==null){
                                      $checkuser->first_name="";
                                   }
                                   if($checkuser->address==null){
                                      $checkuser->address="";
                                   }
                                    if($checkuser->phone==null){
                                      $checkuser->phone="";
                                   }
                                  
                                   
                                    if($checkuser->permissions==null){
                                      $checkuser->permissions="";
                                   }
                                    if($checkuser->last_login==null){
                                      $checkuser->last_login="";
                                   }
                                  
                                     if($request->get("image")!=""){
                                         $png_url = "profile-".mt_rand(100000, 999999).".png";
                                         $path = public_path().'/upload/profile/' . $png_url;
                                         $content=$this->file_get_contents_curl($request->get("image"));
                                            $savefile = fopen($path, 'w');
                                            fwrite($savefile, $content);
                                            fclose($savefile);
                                            $img=public_path().'/upload/profile/' . $png_url;
                                          $checkuser->profile_pic=$png_url;
                                     }
                           
                                    $checkuser->soical_id=$request->get("soical_id");
                                    $checkuser->login_type=$request->input('login_type');
                                    $checkuser->save();
                                    $cartdata=CartData::where("user_id",$checkuser->id)->get();
                                    $wishdata=Wishlist::where("user_id",$checkuser->id)->get();
                                   
                                     $otp = random_int(100000, 999999);
                                      $checkuser->cart=count($cartdata);
                                    $checkuser->totalwish=count($wishdata);
                                    // echo $checkuser->phone;exit;
                                     
                                        $response = array("status" =>1, "msg" =>__("messages.Login Successfully"),"data"=>$checkuser); 
                                     
                                   
                    }
                    else{//register
                       
                            $png_url="";
                            if($request->get("image")!=""){
                                 $png_url = "profile-".mt_rand(100000, 999999).".png";
                                 $path = public_path().'/upload/profile/' . $png_url;
                                 $content=$this->file_get_contents_curl($request->get("image"));
                                            $savefile = fopen($path, 'w');
                                            fwrite($savefile, $content);
                                            fclose($savefile);
                                            $img=public_path().'/upload/profile/' . $png_url;
                            }
                            $str=explode(" ", $request->get("name"));
                            $store=new User();
                            $store->first_name=$str[0];
                            $store->email=$request->get("email");
                            $store->login_type=$request->get("login_type");
                            $store->is_email_verified="1";
                            $store->profile_pic=$png_url;
                            $store->soical_id=$request->get("soical_id");
                            $store->save();
                            $gettoken=Token::where("token",$request->get("token"))->update(["user_id"=>$store->id]);
                             if($store->soical_id==null){
                                      $store->soical_id="";
                                   }
                                   if($store->billing_address==null){
                                      $store->billing_address="";
                                   }
                                   if($store->shipping_address==null){
                                      $store->shipping_address="";
                                   }
                                   if($store->profile_pic==null){
                                      $store->profile_pic="";
                                   }
                                   if($store->first_name==null){
                                      $store->first_name="";
                                   }
                                   if($store->address==null){
                                      $store->address="";
                                   }
                                   if($store->phone==null){
                                      $store->phone="";
                                   }
                                  
                                     if($store->permissions==null){
                                      $store->permissions="";
                                   }
                                    if($store->last_login==null){
                                      $store->last_login="";
                                   }

                                    $cartdata=CartData::where("user_id",$store->id)->get();
                                    $wishdata=Wishlist::where("user_id",$store->id)->get();
                                    $store->cart=count($cartdata);
                                    $store->totalwish=count($wishdata);
                                    $store->login_otp=0;
                             $response = array("status" =>1, "msg" => __("messages.Login Successfully"),"data"=>$store);
                      
                        
                    }
                 }
                 
                if($request->input('login_type')=='3'){
                       $checkuser=User::where("soical_id",$request->get("soical_id"))->first();
                    if($checkuser){//login
                      
                          $gettoken=Token::where("token",$request->get("token"))->first();
                              if(!$gettoken){
                                     $store=new Token();
                           $store->token=$request->get("token");
                           $store->type=$request->get("token_type");
                           $store->user_id=$checkuser->id;
                           $store->save();
                              } else{
                                     $gettoken->user_id=$checkuser->id;
                                     $gettoken->save();
                              }
                            if($checkuser->soical_id==null){
                                      $checkuser->soical_id="";
                                   }
                           
                                   if($checkuser->billing_address==null){
                                      $checkuser->billing_address="";
                                   }
                                   if($checkuser->shipping_address==null){
                                      $checkuser->shipping_address="";
                                   }
                                   if($checkuser->profile_pic==null){
                                      $checkuser->profile_pic="";
                                   }
                                   if($checkuser->first_name==null){
                                      $checkuser->first_name="";
                                   }
                                   if($checkuser->address==null){
                                      $checkuser->address="";
                                   }
                                   if($checkuser->phone==null){
                                      $checkuser->phone="";
                                   }
                                   
                                    if($checkuser->permissions==null){
                                      $checkuser->permissions="";
                                   }
                                    if($checkuser->last_login==null){
                                      $checkuser->last_login="";
                                   }
                                   if($request->get("image")!=""){
                                            $png_url = "profile-".mt_rand(100000, 999999).".png";
                                            $path = public_path().'/upload/profile/' . $png_url;
                                            $content=$this->file_get_contents_curl($request->get("image"));
                                            $savefile = fopen($path, 'w');
                                            fwrite($savefile, $content);
                                            fclose($savefile);
                                            $img=public_path().'/upload/profile/' . $png_url;
                                            $checkuser->profile_pic=$png_url;
                                     }
                           
                                    $checkuser->soical_id=$request->get("soical_id");
                                    $checkuser->login_type=$request->input('login_type');
                                    $checkuser->save();
                                    $cartdata=CartData::where("user_id",$checkuser->id)->get();
                                    $wishdata=Wishlist::where("user_id",$checkuser->id)->get();

                                    
                                         $checkuser->cart=count($cartdata);
                                    $checkuser->totalwish=count($wishdata);
                                            $response = array("status" =>1, "msg" =>__("messages.Login Successfully"),"data"=>$checkuser); 
                                     
                      
                    }
                    else{
                       
                            $png_url="";
                            if($request->get("image")!=""){
                                 $png_url = "profile-".mt_rand(100000, 999999).".png";
                                 $content=$this->file_get_contents_curl($request->get("image"));
                                            $savefile = fopen($path, 'w');
                                            fwrite($savefile, $content);
                                            fclose($savefile);
                                            $img=public_path().'/upload/profile/' . $png_url;
                            }
                            $str=explode(" ", $request->get("name"));
                            $store=new User();
                            $store->first_name=$str[0];
                            $store->email=$request->get("email");
                            $store->login_type=$request->get("login_type");
                            $store->profile_pic=$png_url;
                            $store->is_email_verified="1";
                            $store->soical_id=$request->get("soical_id");
                            $store->save();
                            $gettoken=Token::where("token",$request->get("token"))->update(["user_id"=>$store->id]);
                            if($store->soical_id==null){
                                      $store->soical_id="";
                                   }
                                   if($store->billing_address==null){
                                      $store->billing_address="";
                                   }
                                   if($store->shipping_address==null){
                                      $store->shipping_address="";
                                   }
                                   if($store->profile_pic==null){
                                      $store->profile_pic="";
                                   }
                                   if($store->first_name==null){
                                      $store->first_name="";
                                   }
                                   if($store->address==null){
                                      $store->address="";
                                   }
                                   if($store->phone==null){
                                      $store->phone="";
                                   }
                                   
                                     if($store->permissions==null){
                                      $store->permissions="";
                                   }
                                    if($store->last_login==null){
                                      $store->last_login="";
                                   }
                                   $cartdata=CartData::where("user_id",$store->id)->get();
                                    $wishdata=Wishlist::where("user_id",$store->id)->get();
                                    $store->cart=count($cartdata);
                                    $store->totalwish=count($wishdata);
                                    $store->login_otp = 0;
                             $response = array("status" =>1, "msg" =>__("messages.Login Successfully"),"data"=>$store);
                        }
                        
                   
                 }
            }
            return Response::json(array("data"=>$response));
    }
    public function getcategory(Request $request){
      $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required'                
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required"
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
             $setting = Setting::find(1);
              $currency = explode("-",$setting->default_currency);
                $getcategory=Categories::select('id','image')->where("is_active",'1')->where("is_delete",'0')->where('parent_category','0')->get();
                      foreach ($getcategory as $k) {
                           if($request->get("lang")){
                               $getlang = FileMeta::where("model_id",$k->id)->where("lang",$request->get("lang"))->where("model_name","Categories")->where("meta_key","name")->first();
                           }else{
                               $getlang = FileMeta::where("model_id",$k->id)->where("lang","en")->where("model_name","Categories")->where("meta_key","name")->first();
                           }
                            
                            $k->name = isset($getlang)?$getlang->meta_value:'';
                            $k->total_product = count(Product::where("category",$k->id)->where("is_deleted",'0')->where("status",'1')->get());
                         }
                 $response = array(
                        'status' =>1,
                        "category"=>$getcategory,
                        "currency"=>isset($currency[1])?trim($currency[1]):''
                      );
            }
            return Response::json($response);
    }
    public function editprofile(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'name' => 'required',
                      'phone' => 'required',
                      'user_id'=>"required",
                      'lang'=>'required',
                    ];                    
            $messages = array(
                      'name.required' => "name is required",
                      'address.required' => 'address is required',
                      'phone.required' => "phone is required", 
                      'user_id.required' => "user_id is required",
                      'lang.required'=>"lang is required",
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
                 $setting=Setting::find(1);  
                 $user =User::find($request->get("user_id")); 
                 if($user){
                    $user->first_name=$request->get("name");
                    $user->phone=$request->get("phone");
                    $user->dob = $request->get("dob");
                    $user->gender = $request->get("gender");
                      if ($request->file('upload_image')) {
                        $file = $request->file('upload_image');
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension() ?: 'png';
                        $folderName = '/upload/profile/';
                        $picture = "profile_".time() . '.' . $extension;
                        $destinationPath = public_path() . $folderName;
                        $request->file('upload_image')->move($destinationPath, $picture);
                        $user->profile_pic =$picture;
        }
                    $user->save();
                    $response = array("status" =>1, "msg" => __("messages.Profile Update Successfully"),"data"=>$user);
                 }
                 else{
                  $response = array("status" =>0, "msg" => __("messages.User not Found"));
                 }                
           }
           return $response;
    }
    public function show_getAddress(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                      'lang'=>'required'
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                      'lang.required'=>"Lang is required"
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
                     $getdata=Addresses::where("is_delete",'0')->where("user_id",$request->get("user_id"))->get();
                     $language = Lang_core::all();
                     App::setlocale($request->get("lang"));
                     session()->put('locale', $request->get("lang"));
                     if(count($getdata)>0){
                       
                        $response = array("status" =>1, "msg" => __("messages.Get List Of Address"),"data"=>$getdata);
                     }else{
                        $response = array("status" =>0, "msg" => __("messages.Not Found"));
                     }
           }
           return Response::json(array("data"=>$response));
    }
    public function show_SaveAddress(Request $request){
       $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                      'lang'=>'required',
                      'address'=>'required',
                      'lat'=>'required',
                      'long'=>'required',
                      'state'=>"required",
                      'id'=>"required",
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                      'lang.required'=>"Lang is required",
                      "address.required"=>"address is required",
                      "lat.required"=>"lat is required",
                      "long.required"=>"long is required",
                      "city.required"=>"city is required",
                      "state.required"=>"State is required",
                      "pincode.required"=>"Pincode is required",
                      "id.required"=>"id is required",
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
                     $language = Lang_core::all();
                     App::setlocale($request->get("lang"));
                     session()->put('locale', $request->get("lang"));
                     if($request->get("id")==0){
                         $data = new Addresses();
                         $msg = __("messages.Address Add Successfully");
                     }else{
                         $data = Addresses::find($request->get("id"));
                         $msg = __("messages.Address Update Successfully");
                     }
                     $data->user_id = $request->get("user_id");
                     
                     $data->address = $request->get("address");
                     $data->lat = $request->get("lat");
                     $data->long = $request->get("long");
                     $data->city = $request->get("city");
                     $data->state = $request->get("state");
                     $data->pincode = $request->get("pincode");
                     if($request->get("mobile_no") != "")
                     {
                         $data->mobile_no = $request->get("mobile_no");
                     }
                     if($request->get("name") != "")
                     {
                         $data->name =$request->get("name");
                     }
                     $data->save();
                     $response = array("status" =>1, "msg" => $msg ,"data"=>$data);
                    
           }
           return Response::json(array("data"=>$response));
    }
    public function gethelp(Request $request){
            $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'id' => 'required',
                      'lang'=>'required'             
                    ];                    
            $messages = array(
                      'id.required' => "id is required",
                      'lang.required' => "lang is required"
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
                        $gettext=QueryTopic::with("Question")->where("page_id",$request->get("id"))->get(); 
                        if($gettext){
                            if(count($gettext)>0){
                                foreach ($gettext as $k) {
                                    $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","QueryTopic")->where("meta_key","topicname")->first();
                                    $k->topic = isset($getlang)?$getlang->meta_value:'';
                                    foreach ($k->question as $g) {
                                        $getlang = FileMeta::where("model_id",$g->id)->where("lang",Session::get('locale'))->where("model_name","QueryAns")->where("meta_key","ques")->first();
                                        $g->question = isset($getlang)?$getlang->meta_value:'';
                                        $getlang = FileMeta::where("model_id",$g->id)->where("lang",Session::get('locale'))->where("model_name","QueryAns")->where("meta_key","ans")->first();
                                        $g->answer = isset($getlang)?$getlang->meta_value:'';
                                    }
                                }
                                $response = array("status" =>1, "msg" => __("messages.Help get Successfully"),"help"=>$gettext);
                            }else{
                                $response = array("status" =>0, "msg" => __("messages.Help Not Found"),"help"=>array());
                            }
                            
                        }else{
                             $response = array("status" =>0, "msg" => __("messages.Data Not Found"),"help"=>array());
                        }               
            }
           return Response::json(array("data"=>$response));
    }
    public function getwishlist(Request $request){
       $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'user_id'=>'required',
                      'lang'=>'required'       
                    ];                    
            $messages = array(
                    'user_id.required' => "user_id is required",
                    'lang.required'=>'lang is required'
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }else {
                       App::setlocale($request->get("lang"));
                       session()->put('locale', $request->get("lang"));
                       $data=Wishlist::where("user_id",$request->get("user_id"))->get();
                       if(count($data)!=0){
                          foreach ($data as $k) {
                           
                             $user=User::select('id','res_image','address','first_name','delivery_time','access_cat')->where('id',$k->res_id)->first();
                             
                            if($user){
                               $category_str=  $user->access_cat;
                                 $cat = explode(",",$category_str);
                                 $cat_name = array();
                                    foreach($cat as $val){
                                        $cat =Categories::select('cat_name')->where("id",$val)->WHERE('parent_category',0)->first();
                                        if($cat)
                                        {
                                          
                                        $cat_name[] =$cat->cat_name; 
                                        $str= implode(",",$cat_name);
                                        }
                                        else
                                        {$str="";
                                            
                                        }
                                    }
                                    $user->access_cat = $str;
                                 $rate = Review::where('res_id',$user->id)->avg('ratting')?Review::where('res_id',$user->id)->avg('ratting'):'0.0';
                                 $user->ratting=(string)$rate;
                                $k->restaurant=$user;
                            }
                            else
                            {
                                $k->restaurant="";
                            }
                          }
                          $response = array(
                            'status' =>1,
                            "Wish"=>$data
                          );
                       }   
                       else{
                           $response = array(
                            'status' =>"0",
                            "msg"=>__("messages.No WishList Found")
                          );
                       }
            }
        return Response::json($response);
   }
   public function forgotpassword(Request $request){
            $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'email' => 'required',
                      'lang'=>'required'          
                    ];                    
            $messages = array(
                      'email.required' => "email is required",
                      'lang.required'=>'lang is required'
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
                 $setting=Setting::find(1);  
                 $checkmobile=User::where("email",$request->get("email"))->get();
                  if(count($checkmobile)!=0){
                      $code=mt_rand(100000, 999999);
                      $store=array();
                      $store['email']=$checkmobile[0]->email;
                      $store['name']=$checkmobile[0]->name;
                      $store['code']=$code;
                      $add=new ResetPassword();
                      $add->user_id=$checkmobile[0]->id;
                      $add->code=$code;
                      $add->save();
                       try {
                              Mail::send('email.forgotpassword', ['user' => $store], function($message) use ($store){
                                $message->to($store['email'],$store['name'])->subject('Shop');
                            });
                       } catch (\Exception $e) {
                       }
                      $response = array("status" =>1, "msg" => __("messages.Email Send Successfully"));
                  }
                 else{
                    $response = array("status" =>0, "msg" => __("messages.Email Id Not Exist"));
                 }                
           }
           return $response;  
    }
    public function verifiedcoupon1(Request $request){
        $response = array("success" => "0", "discount" => "Not Set");
           $rules = [
                      'coupon_code' => 'required',
                      'user_id' => 'required',
                      'total' => 'required',
                      'product'=>'required',
                      'lang'=>'required'             
                    ];                    
            $messages = array(
                      'coupon_code.required' => "coupon_code is required",
                      'user_id.required' => "user_id is required",
                      'total.required' => "total is required",
                      'product.required'=>"Product is required",
                      'lang.required'=>'lang is required'
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['discount'] = $message;
            } else {
                 App::setlocale($request->get("lang"));
                 session()->put('locale', $request->get("lang"));
                 $date=date("Y-m-d");
                 $data=Coupon::where("code",$request->get("coupon_code"))->where("status",'1')->orderBy('id','desc')->first();
                 if(!$data){
                  $response = array("status" =>0, "discount" => __("messages.Coupon Not Found"));
                 }
                else{
                      if($date){
                         
                        $order=Order::where("coupon_code",$request->get("coupon_code"))->where('user_id','!=',$request->get("user_id"))->groupBy('user_id')->get();
                        
                        $orderuser=DB::table('order_data')
                           ->select('order_data.id')
                           ->join('order_record', 'order_record.id', '=', 'order_data.order_id')
                           ->where('order_record.coupon_code',$request->get("coupon_code"))
                           ->where('order_record.user_id',$request->get("user_id"))
                           ->get();
                              $temp=0;
                              $arr=explode(",",$request->get("product"));

                              if($data->coupon_on=='1'){ 
                                      
                                $codepro=explode(",", $data->categories);
                                
                                foreach ($arr as $k) {
                                  $getcategory=Product::find($k);  
                                                                
                                  if(in_array($getcategory->category,$codepro)){
                                          $temp=1;
                                  }
                                }
                              
                              }
                              else{     
                                  
                                 $codepro=explode(",", $data->product);
                                 
                                 foreach ($arr as $k) {
                                      if(in_array($k,$codepro)){
                                          
                                          $temp=1;
                                      }
                                 }
                              }
                              if($temp==0){
                                   $response = array("status" =>0, "discount" => __("messages.Coupon Invaild"));
                                   return Response::json(array("data"=>$response));
                              }
                         
                        if($data->usage_limit_per_coupon!=""&&($data->usage_limit_per_coupon<count($order))){
                              $response = array("status" =>0, "discount" =>__("messages.Coupon Limit Over"));
                        }
                        elseif($data->usage_limit_per_customer!=""&&($data->usage_limit_per_customer<=count($orderuser))){
                              $response = array("status" =>0, "discount" => __("messages.Your Coupon Limit Over"));
                        }
                        elseif($data->minmum_spend!=""&&$data->minmum_spend>$request->get("total")){
                             $response = array("status" =>0, "discount" => __("messages.Not Vaild Coupon,total less than minimum amount of coupon"));
                        }
                        elseif($data->maximum_spend!=""&&$data->maximum_spend<=$request->get("total")){
                                 $response = array("status" =>0, "discount" => __("messages.Not Valid Coupon,total greater than maximum amount of coupon"));
                        }
                        else{

                              $temp=0;
                              $arr=explode(",",$request->get("product"));
                              if($data->coupon_on=='1'){
                                $codepro=explode(",", $data->categories);

                                foreach ($arr as $k) {
                                  $getcategory=Product::find($k);  
                                                                
                                  if(in_array($getcategory->category,$codepro)){
                                          $temp=1;
                                  }
                                }
                              
                              }
                              else{                                 
                                 $codepro=explode(",", $data->product);
                                 foreach ($arr as $k) {
                                      if(in_array($k,$codepro)){
                                          $temp=1;
                                      }
                                 }
                              }
                              if($temp==1){
                                  if($data->discount_type=='1'){
                                   $discount=($request->get("total")*$data->value)/100;
                                  }
                                  else{
                                     $discount=$data->value;
                                  }
                                 $data=array("discount_price"=>$discount,"freeshipping"=>$data->free_shipping);
                                     $response = array("status" =>1,"discount"=>$data);
                              }
                              else{
                                $response = array("status" =>0, "discount" => __("messages.Coupon Invaild"));
                              }
                             
                           }
                        }
                        else{
                          $response = array("status" =>0, "discount" => __("messages.Coupon Invaild"));
                        }
                      }
           }
           return Response::json(array("data"=>$response));
   }
   public function addwish(Request $request){
       $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'res_id' => 'required',
                      'user_id'=>'required'        
                    ];                    
            $messages = array(
                    'res_id.required' => "res_id is required",
                    'user_id.required' => "user_id is required"
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }else {
                $getwish=Wishlist::where("res_id",$request->get("res_id"))->where("user_id",$request->get("user_id"))->first();
                if(!empty($getwish)){
                      $getwish->delete();
                      $total=Wishlist::where("user_id",$request->get("user_id"))->get();
                      $response = array(
                            'status' =>1,
                            "remove"=>"yes",
                            "wish"=>count($total)
                          );
                }
                else{
                          $data=new Wishlist();
                          $data->res_id=$request->get("res_id");
                          $data->user_id=$request->get("user_id");
                          $data->save();
                          $total=Wishlist::where("user_id",$request->get("user_id"))->get();
                          $response = array(
                            'status' =>1,
                            "remove"=>"no",
                            "wish"=>count($total)
                          );
                }
                 
            }
      
      return Response::json($response);
   }
   public function postreview(Request $request){
          $response = array("status" => "0", "register" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                    //   'product_id' => 'required',
                      'review' => 'required',
                      'ratting' => 'required',
                      'lang'=>'required'               
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                    //   'product_id.required' => "product_id is required",
                      'review.required' => "review is required",
                      'ratting.required' => "ratting is required",
                      'lang.required' => "lang is required"
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
                
                if($request->get("res_id")){
                    $res = $request->get("res_id");
                }else{
                    $res = "";
                }
                
                if($request->get("product_id")){
                    $pro = $request->get("product_id");
                }else{
                    $pro = "";
                }
                
                $data=array();
                $data=new Review();
                $data->product_id=$pro;
                $data->res_id = $res;
                $data->user_id=$request->get("user_id");
                $data->review=$request->get("review");
                $data->ratting=$request->get("ratting");
                $data->save();
                $response = array("status" =>1, "msg" => __("messages.Review Add Successfully"),"data"=>$data);
           }
           return Response::json(array("data"=>$response));
     }
   public function viewproduct(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required'                
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required"
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
                    $user_id = $request->get("user_id");
                    $product=Product::where("is_deleted",'0')->where("id",$id)->first();
               
                    if(!empty($product)){  
                       
                             $main_array=array();
                             $attributearr=array();
                             $attribute_set=array();
                             $data=array();
                             $seller = User::find($product->user_id);
                             $product->seller_brand_name =User::find($product->user_id)?User::find($product->user_id)->brand_name:'';
                             
                             $wish=Wishlist::where("res_id",$id)->where("user_id",$user_id)->get();
                             $product->wish=count($wish);
                             $totalreview=Review::where("product_id",$id)->get();
                             $product->total_review=count($totalreview);
                             $getlang = FileMeta::where("model_id",$product->id)->where("lang",Session::get("locale"))->where("model_name","Product")->where("meta_key","colorname")->first();
                             $product->colorname=isset($getlang)?$getlang->meta_value:'';
                             $getlang = FileMeta::where("model_id",$product->id)->where("lang",Session::get("locale"))->where("model_name","Product")->where("meta_key","meta_keyword")->first();
                             $product->meta_keyword=isset($getlang)?$getlang->meta_value:'';
                             
                             $arr = explode(",",$product->related_product);
                             $da = array();
                            if(!empty($arr)&&$product->related_product!=""){
                                foreach($arr as $a){
                                   $ls = array();
                                   $k=Product::where("status",'1')->where("is_deleted",'0')->where("id",$a)->first();
                                   if($k){
                                   $getlang = FileMeta::where("model_id",$a)->where("lang",Session::get("locale"))->where("model_name","Product")->where("meta_key","name")->first();
                                       $ls['name']=isset($getlang)?$getlang->meta_value:'';
                                 
                                  $getreview=Review::where("product_id",$a)->get();
                                  $ls['total_review']=count($getreview);
                                  $avgStar = Review::where("product_id",$a)->avg('ratting');
                                  $ls['avgStar']=round($avgStar);
                                  $wish=Wishlist::where("product_id",$a)->where("user_id",$user_id)->get();
                                  $ls['wish']=count($wish);
                                 
                                  $ls['price']= $k->price;
                                  $ls['basic_image']=asset("public/upload/product")."/".$k->basic_image;
                                  $ls['id']=$k->id;
                                  $options=ProductOption::where("product_id",$k->id)->groupBy('option_id')->get();
                            $data = array();
                            $i=0;
                          
                            foreach ($options as $k) {
                                
                                $d1 = ProductOption::where("product_id",$k->id)->where("option_id",$k->option_id)->where("lang",Session::get("locale"))->first();
                                if($d1){
                                    $data[$i]['optionname'] = $k->name;
                                    $data[$i]['type'] = $k->type;
                                    $data[$i]['required'] = $k->is_required;
                                     $la = explode("#",$k->label);
                                     $pr = explode("#",$k->price);
                                     $j = 0;
                                     
                                     foreach ($pr as $p) {
                                         $a = array();
                                        $a['label'] = $la[$j];
                                        $a['price'] = $p;
                                        $data[$i]['optionvalues'][] =  $a;
                                        $j++;
                                     }
                                    $i++;
                                }
                            }
                            $ls['options'] = $data;
                                  $da[]=$ls;
                                   }
                                
                             }
                             
                            
                             $product->related_product = $da;
                            }
                            else{
                                $product->related_product = array();
                            }
                             
                            
                            $img=array();
                    $product->imglist = $product->additional_image;
                    if($product->additional_image!=""){
                           
                           $images=explode(",",$product->additional_image);
                           $i=1;
                            foreach ($images as $k) {
                                if($k!=""){
                                     $img[$i]=asset('public/upload/product/').'/'.$k;
                                $i++;
                                }
                               
                            }
                           $product->additional_image=implode(",",$img);
                            
                    }
                  
                          
                            $product->options=ProductOption::where("product_id",$id)->groupBy('option_id')->get();
                            $price=$product->price;
                            
                            $data = array();
                            $i=0;
                            foreach ($product->options as $k) {
                                $d1 = ProductOption::where("product_id",$id)->where("option_id",$k->option_id)->where("lang",Session::get("locale"))->first();
                                if($d1){
                                    $data[$i]['optionname'] = $k->name;
                                    $data[$i]['type'] = $k->type;
                                    $data[$i]['required'] = $k->is_required;
                                     $la = explode("#",$k->label);
                                     $pr = explode("#",$k->price);
                                     $j = 0;
                                     
                                     foreach ($pr as $p) {
                                         $a = array();
                                        $a['label'] = $la[$j];
                                        $a['price'] = $p;
                                        $data[$i]['optionvalues'][] =  $a;
                                        $j++;
                                     }
                                    $i++;
                                }
                            }
                            $product->options = $data;
                            
                           
                            $product->review=Review::where("product_id",$id)->where("is_deleted",'0')->orderby("id","DESC")->take(5)->get();
                             foreach ($product->review as $re) {
                                 $users = User::find($re->user_id);
                                 $ls = array();
                                 if(isset($users)){
                                     
                                     $ls['profile_pic']=asset("public/upload/profile/").'/'.$users->profile_pic;
                                     $ls['name'] = $users->first_name;
                                     $re->userdata = $ls;
                                 }else{
                                     $re->userdata = (object) $ls;
                                 }
                             }
                            
                            $avgStar = Review::where("product_id",$id)->avg('ratting');
                            if(empty($avgStar)){
                               $avgStar=0;
                            }
                            else{
                               $avgStar=round($avgStar);
                            }
                            if($product->basic_image!=""){
                                $product->basic_image=asset('public/upload/product/').'/'.$product->basic_image;
                            }else{
                                 $product->basic_image="";
                            }
                            
                            $product->avgStar=$avgStar;
                            $cat=Categories::find($product->category);
                            $sub=Categories::find($product->subcategory);
                            
                             if($request->get("lang")){
                                $lang= $request->get("lang");
                            }else{
                                $lang = "en";
                            }
                            $getlang = FileMeta::where("model_id",$product->brand)->where("lang",$lang)->where("model_name","Brand")->where("meta_key","name")->first();
                            $product->brand=isset($getlang)?$getlang->meta_value:'';
                            $getlang = FileMeta::where("model_id",$cat->id)->where("lang",$lang)->where("model_name","Categories")->where("meta_key","name")->first();
                            $product->category=isset($getlang)?$getlang->meta_value:'';
                            $product->subcategory=isset($getlang)?$getlang->meta_value:'';
                            
                            $lang = Lang_core::all();
                            $arr_pro = array();
                            foreach($lang as $l){
                                $ls = array();
                                $ls['lang'] = $l->code;
                                $getlang = FileMeta::where("model_id",$product->id)->where("lang",$l->code)->where("model_name","Product")->where("meta_key","name")->first();
                             $ls['name']=isset($getlang)?$getlang->meta_value:'';
                             $getlang = FileMeta::where("model_id",$product->id)->where("lang",$l->code)->where("model_name","Product")->where("meta_key","colorname")->first();
                             $ls['colorname']=isset($getlang)?$getlang->meta_value:'';
                             $getlang = FileMeta::where("model_id",$product->id)->where("lang",$l->code)->where("model_name","Product")->where("meta_key","meta_keyword")->first();
                             $ls['meta_keyword']=isset($getlang)?$getlang->meta_value:'';
                             $arr_pro[]=$ls;
                            }
                            $product->lang_val = $arr_pro;
                            $product->price=$price;
                               $response = array(
                                  'status' =>1,
                                  "offers"=>$product
                                );
                    }
                    else{
                        $response = array(
                          'status' =>0,
                          "offers"=>__("messages.No Product Found")
                        );
                    }
        }
       return Response::json($response);
   }
   public function show_changes_password(Request $request){
          $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required',
                      'lang'=>'required',
                      'password'=>'required'
                    ];                    
            $messages = array(
                      'id.required' => "id is required",
                      'lang.required'=>"Lang is required",
                      'password.required'=>"password is required",
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
                     $getdata=User::find($request->get("id"));
                     $language = Lang_core::all();
                     App::setlocale($request->get("lang"));
                     session()->put('locale', $request->get("lang"));
                     if($getdata){
                        $getdata->password = $request->get("password");
                        $getdata->save();
                        $response = array("status" =>1, "msg" => __("messages.Password Change Successfully"));
                     }else{
                        $response = array("status" =>0, "msg" => __("messages.Not Found"));
                     }
           }
           return Response::json(array("data"=>$response));
   }
   public function show_DeleteAddress(Request $request){
          $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required',
                      'lang'=>'required'
                    ];                    
            $messages = array(
                      'id.required' => "id is required",
                      'lang.required'=>"Lang is required"
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
                     $getdata=Addresses::find($request->get("id"));
                     $language = Lang_core::all();
                     App::setlocale($request->get("lang"));
                     session()->put('locale', $request->get("lang"));
                     if($getdata){
                        $getdata->is_delete = '1';
                        $getdata->save();
                        $response = array("status" =>1, "msg" => __("messages.Address Delete Successfully"));
                     }else{
                        $response = array("status" =>0, "msg" => __("messages.Not Found"));
                     }
           }
           return Response::json(array("data"=>$response));
   }
    public function preplaceorder(Request $request){
       $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                      'shipping_method' => 'required',
                      'orderjson'=>'required',
                      'lang'=>'required',
                       'sub_total'=>'required',
                       'total'=>'required',
                      'seller_id'=>'required'
                    ];
            $messages = array(
                  'user_id.required' => "user_id is required",
                  'shipping_method.required' => "shipping_method is required",
                  'orderjson.required' => "orderjson is required",
                  'lang.required' => "lang is required",
                  'sub_total.required' => "sub_total is required",
                  'total.required' => "total is required",
                  'seller_id.required' => "seller_id is required"
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
                    $setting=Setting::find(1);
                    App::setlocale($request->get("lang"));
                    session()->put('locale', $request->get("lang"));

                     $data = Order::preOrderPlacetoApi($request,0);
                     
                     if($data){
                          $response = array("status" => 1, "msg" => __("messages.Order Save Successfully"),"data" =>$data);
                     }else{
                          $response = array("status" => 0, "msg" => __("messages.Something wrong"));                         
                     }
                   
            }
            return Response::json(array("data"=>$response));
   }

    public function postplaceorder(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      
                      'payment_method' => 'required',
                      'user_address_id' => 'required',
                      'lang'=>'required',
                      'id'=>'required'
                    ];
                    
                    if($request->input('payment_method')=="2"){
                           $rules['stripeToken'] = 'required';
                    }
                    if($request->input('payment_method')=="1"){
                           $rules['pay_pal_paymentId'] = 'required';
                    }
            $messages = array(
                  'shipping_method.required' => "shipping_method is required",
                  'payment_method.required' => "payment_method is required",
                  'user_address_id.required' => "user_address_id is required",
                  'delivery_time.required' => "delivery_time is required",
                  'delivery_date.required' => "delivery_date is required",
                  'lang.required' => "lang is required",
                  'subtotal.required'=>'subtotal is required',
                  'lang.required'=>'lang is required'
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
                    $setting=Setting::find(1);
                    App::setlocale($request->get("lang"));
                    session()->put('locale', $request->get("lang"));

                     $data = Order::OrderPlacetoApi($request,1);
                     if($data){
                            
                             $orderstore = Order::find($data);
                             $msg = __("messages.Your Order Has Been Placed Successfully");
                             $android=$this->send_notification_android($setting->android_api_key,$orderstore->user_id,$msg,$orderstore->id,'user_id');
                             $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderstore->user_id,$msg,$orderstore->id,'user_id');
                          
                        
                             $msg = __("messages.You Get New Order");
                             $android=$this->send_notification_android($setting->android_api_key,$orderstore->seller_id,$msg,$orderstore->order_id,'seller_id');
                             $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderstore->seller_id,$msg,$orderstore->order_id,'seller_id');
                         
                          CartData::where("user_id",$orderstore->user_id)->delete();
                          $response = array("status" => 1, "msg" => __("messages.Order Placed Successfully"),"data" =>$data);
                     }else{
                          $response = array("status" => 0, "msg" => __("messages.Something wrong"));                         
                     }
                   
            }
            return Response::json(array("data"=>$response));
   }
   public function delete_user(Request $request){
            $response = array("status" => "0", "register" => "Validation error");
           $rules = [
                      'user_id' => 'required'        
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required"
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
                      $id = $request->get("user_id");
                      $user=User::find($id);
                      
                      if($user){
                          
                          $order=Order::where("user_id",$id)->get();
                          foreach ($order as $k) {
                                $order=OrderResponse::where("order_id",$k->id)->delete();
                                $order=OrderData::where("order_id",$k->id)->delete();
                                $k->delete();
                          }
                          $delreview=Review::where("user_id",$id)->delete();
                          $user->delete();
                      }
                      $response = array("status" =>1, "msg" => __('messages_error_success.user_del'));
           }
           return Response::json(array("data"=>$response));
   }


    public function add_order_data(Request $request)
    {
          $rules = [
                      'order_id' => 'required',
                      'payment_method' => 'required',
                      'user_address_id'=>'required',
                      'lang'=>'required',
                      'delivery_time'=>'required',
                      'delivery_date'=>'required',
                      
                    ];
            $messages = array(
                  'order_id.required' => "order_id is required",
                  'payment_method.required' => "payment_method is required",
                  'user_address_id.required' => "user_address_id is required",
                  'lang.required' => "lang is required",
                  'delivery_time.required' => "delivery_time is required",
                  'delivery_date.required' => "delivery_date is required"
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

              $data= new OrderDataApp();
              $data->order_id=$request->get("order_id");
              $data->payment_method=$request->get("payment_method");
              $data->user_address_id=$request->get("user_address_id");
              $data->lang=$request->get("lang");
              $data->delivery_time=$request->get("delivery_time");
              $data->delivery_date=$request->get("delivery_date");
              $data->notes=$request->get("notes");
              $data->save();
              $response = array("status" =>1, "msg" => "Added successfully","data"=>$this->encryptstring($data->id));
            }
         
           return Response::json(array("data"=>$response));
    }

    public function encryptstring($val){
        return Crypt::encryptString($val);
    }

    public function decyptstring($val){
        return Crypt::decryptString($val);
    }

   
    public function send_notification_android($key,$user_id,$msg,$id,$field){
          $getuser=Token::where("type",1)->where($field,$user_id)->get();
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
   public function send_notification_IOS($key,$user_id,$msg,$id,$field){
      $getuser=Token::where("type",2)->where($field,$user_id)->get();
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
   
 
  

     

    

    

     
    function getcode() { 
          $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
          $randomString = ''; 
        
          for ($i = 0; $i <10; $i++) { 
              $index = rand(0, strlen($characters) - 1); 
              $randomString .= $characters[$index]; 
          } 
        
          return $randomString; 
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
    public function getcurrency(){
            $setting=Setting::find(1);
            $cur=explode("-",$setting->default_currency);  
            return $cur[1];                  
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


   public function categoryoffer(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required'                
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required"
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
                    $date=date('Y-m-d');
                    $getcategory=Categories::where("parent_category",0)->where("is_active",'1')->where("is_delete","0")->get();
                    foreach ($getcategory as $k) {
                        $od=array();
                        $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get("locale"))->where("model_name","Categories")->where("meta_key","name")->first();                       
                        $k->name = isset($getlang)?$getlang->meta_value:'';
                        $offers=Offer::where("category_id",$k->id)->get();
                        foreach ($offers as $of) {
                             $start_date=date("Y-m-d",strtotime($of->start_date)); 
                             $end_date=date("Y-m-d",strtotime($of->end_date));
                          if(($date>=$start_date)&&($date<=$end_date)){
                                $od[]=$of;
                          }

                          $getlang = FileMeta::where("model_id",$of->id)->where("lang",Session::get("locale"))->where("model_name","Offer")->where("meta_key","title")->first();
                           $of->title = isset($getlang)?$getlang->meta_value:'';
                           $getlang = FileMeta::where("model_id",$of->id)->where("lang",Session::get("locale"))->where("model_name","Offer")->where("meta_key","main_title")->first();
                           $of->main_title = isset($getlang)?$getlang->meta_value:'';
                        }
                        $k->offers=$od;
                        $getsubcategory=Categories::where("parent_category",$k->id)->where("is_delete","0")->where("is_active",'1')->get();
                        foreach($getsubcategory as $k1){                           
                            $getlang = FileMeta::where("model_id",$k1->id)->where("lang",Session::get("locale"))->where("model_name","Categories")->where("meta_key","name")->first();
                            $k1->name = isset($getlang)?$getlang->meta_value:'';
                        }
                        $k->subcategory=$getsubcategory;
                    }
                    $response = array(
                      'status' =>1,
                      "data"=>$getcategory
                    );
                    
           }
           return Response::json($response);
      
   }

 public function bestselling(Request $request){
       $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required',
                      'user_id'=>'required'              
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required",
                      'user_id.required'=>'user_id is required'
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
                    $user_id = $request->get("user_id");
                    App::setlocale($request->get("lang"));
                    session()->put('locale', $request->get("lang"));
                    $data=Product::where('products.is_deleted','0')->where('products.status','1')->select('id','basic_image as image','selling_price as price','MRP','discount','totalorders')->orderby('products.totalorders','DESC') 
                           ->paginate(16);
                           
                         if(count($data)!=0){
                              foreach ($data as $k) {
                                   $dat=[];
                               
                                    $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                                    $wish=Wishlist::where("product_id",$k->id)->where("user_id",$user_id)->get();
                                    $total=Review::where("product_id",$k->id)->get();
                                    $getlang = FileMeta::where("model_id",$k->id)->where("lang",$request->get("lang"))->where("model_name","Product")->where("meta_key","name")->first();
                                     $k->name = isset($getlang)?$getlang->meta_value:'';
                                     
                                    
                                    $k->image=asset('public/upload/product/').'/'.$k->image;
                                    
                                    $k->ratting=round($avgStar);
                                    $k->totalreview=count($total);
                                    $k->wish=count($wish);
                                    $k->discount=$k->discount;
                                     $options=ProductOption::where("product_id",$k->id)->groupBy('option_id')->get();
                                        $data1 = array();
                                        $i=0;
                                        foreach ($options as $k1) {
                                            $d1 = ProductOption::where("product_id",$k->id)->where("option_id",$k1->option_id)->where("lang",Session::get("locale"))->first();
                                            if($d1){
                                                $data1[$i]['optionname'] = $k1->name;
                                                $data1[$i]['type'] = $k1->type;
                                                $data1[$i]['required'] = $k1->is_required;
                                                 $la = explode("#",$k1->label);
                                                 $pr = explode("#",$k1->price);
                                                 $j = 0;
                                                 
                                                 foreach ($pr as $p) {
                                                     $a = array();
                                                    $a['label'] = $la[$j];
                                                    $a['price'] = $p;
                                                    $data1[$i]['optionvalues'][] =  $a;
                                                    //$data[$i]['optionvalues'][]['price']=$p;
                                                    $j++;
                                                 }
                                                $i++;
                                            }
                                        }
                                         $k->options = $data1;
                                   // $k->productdetail=$dat;
                                   }           
                               
                                $total=Wishlist::where("user_id",$user_id)->get();
                                $cartdata=CartData::where("user_id",$user_id)->get();
                               // echo "<pre>";print_r($cartdata);exit;
                               $response = array(
                                'status' => 1,
                                "product"=>array("data"=>$data,"totalwish"=>count($total),"carttotal"=>count($cartdata))
                              );
                         }
                         else{
                                  $response = array(
                                        'status' => 0,
                                        "product"=>$data
                                      );
                         }
            }
            return Response::json($response);
 }

 public function taxlist(Request $request){
   $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required'                
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required"
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
                      $gettax=Taxes::all();
                      foreach ($gettax as $k) {
                          $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get("locale"))->where("model_name","Taxes")->where("meta_key","tax_name")->first();
                          $k->tax_name = isset($getlang)?$getlang->meta_value:'';
                      }
                      $response = array(
                          'status' => 0,
                          "product"=>$gettax
                        );
            }
      return Response::json($response);
 }

  
 public function mainoffers(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required'                
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required"
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
                      $best=array();
                      $date=date("Y-m-d");
                      $getcategory=Categories::select('id','image')->where("is_active",'1')->where("is_delete",'0')->where('parent_category','0')->get();
                      foreach ($getcategory as $k) {
                           if($request->get("lang")){
                               $getlang = FileMeta::where("model_id",$k->id)->where("lang",$request->get("lang"))->where("model_name","Categories")->where("meta_key","name")->first();
                           }else{
                               $getlang = FileMeta::where("model_id",$k->id)->where("lang","en")->where("model_name","Categories")->where("meta_key","name")->first();
                           }
                            
                            $k->name = isset($getlang)?$getlang->meta_value:'';
                            $k->total_product = count(Product::where("category",$k->id)->where("is_deleted",'0')->where("status",'1')->get());
                         }
                      $bestoffer=Offer::where("offer_type","1")->orderby('id',"DESC")->get();
                      foreach ($bestoffer as $bo) {
                          $start_date=date("Y-m-d",strtotime($bo->start_date)); 
                          $end_date=date("Y-m-d",strtotime($bo->end_date));
                          if(($date>=$start_date)&&($date<=$end_date)){
                                  if($bo->is_product=='1'){
                                     
                                  }
                                  if($bo->is_product=='2'){
                                    
                                  }
                                  $best[]=$bo;
                          }
                           $getlang = FileMeta::where("model_id",$bo->id)->where("lang",$request->get("lang"))->where("model_name","Offer")->where("meta_key","title")->first();
                           $bo->title = isset($getlang)?$getlang->meta_value:'';
                           $getlang = FileMeta::where("model_id",$bo->id)->where("lang",$request->get("lang"))->where("model_name","Offer")->where("meta_key","main_title")->first();
                           $bo->main_title = isset($getlang)?$getlang->meta_value:'';
                        }
                     $data=Deal::with('offer')->get();
                     foreach ($data as $k) {
                      if(isset($k->offer)&&$k->offer->is_product=='1'){
                                    
                                     $best[]=$k->offer;
                                  }
                                  if(isset($k->offer)&&$k->offer->is_product=='2'){
                                     
                                     $best[]=$k->offer;
                                  }
                                if(isset($k->offer)){
                                    $getlang = FileMeta::where("model_id",$k->offer->id)->where("lang",$request->get("lang"))->where("model_name","Offer")->where("meta_key","title")->first();
                           $k->offer->title = isset($getlang)?$getlang->meta_value:'';
                           $getlang = FileMeta::where("model_id",$k->offer->id)->where("lang",$request->get("lang"))->where("model_name","Offer")->where("meta_key","main_title")->first();
                           $k->offer->main_title = isset($getlang)?$getlang->meta_value:'';
                                }
                                   
                       
                     }
                      $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                     $response = array(
                        'status' =>1,
                        "offers"=>$best,
                        "category"=>$getcategory,
                        "currency"=>isset($currency[1])?trim($currency[1]):''
                      );
     
           }
            return Response::json($response);
     
 }




  


   public function resendsms(Request $request){
       $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required',
                      'phone'=>'required'
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required",
                      'phone.required' => "phone is required"
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
                    if($request->get("type")==1){
                          $data = Delivery::where("mobile_no",$request->get("phone"))->first();
                        if($data){
                            $response = array("status" =>0, "msg" => __("messages.Phone No Already Exist"));
                        }else{
                            $otp = random_int(100000, 999999);
                           // $otp = 123456;
                            $msg = __("messages.Your Otp")." :".$otp;
                            $result = $this->sendotpmsg($request->get("phone"),$msg);
                            $response = array("status" =>1, "msg" => __("messages.Otp Send Successfully"),"login_otp"=>$otp);
                        }
                    }else{
                            
                         $data = User::where("phone",$request->get("phone"))->where("user_type",'1')->first();
                        if($data){
                            $response = array("status" =>0, "msg" => __("messages.Phone No Already Exist"));
                        }else{
                            $otp = random_int(100000, 999999);
                           // $otp = 123456;
                            $msg = __("messages.Your Otp")." :".$otp;
                            $result = $this->sendotpmsg($request->get("phone"),$msg);
                            $response = array("status" =>1, "msg" => __("messages.Otp Send Successfully"),"login_otp"=>$otp);
                        }
                    }
                    
            }
            
            return Response::json($response);
            
   }
  
  
 public function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }
 
     

   public function showoffers(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                      'page_no'=>'required',
                      'lang'=>'required'                
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                      'page_no.required'=>'page_no is required',
                      'lang.required' => 'lang is required'
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
                    $user_id=$request->get("user_id");
                    $page_no = $request->get("page_no");
                    App::setlocale($request->get("lang"));
                    session()->put('locale',$request->get("lang"));
                    $date=date("Y-m-d");
                    $best=array();
                    $normal=array();
                    $sen_offer=Seasonaloffer::where("is_active","1")->first();  
                    $bestoffer=Offer::where("offer_type","1")->orderby('id',"DESC")->get();
                    foreach ($bestoffer as $bo) {
                      $start_date=date("Y-m-d",strtotime($bo->start_date)); 
                      $end_date=date("Y-m-d",strtotime($bo->end_date));
                      if(($date>=$start_date)&&($date<=$end_date)){
                              if($bo->is_product=='1'){
                                 $bo->new_price="";
                                 $bo->product_id="";
                              }
                              if($bo->is_product=='2'){
                                 $bo->fixed="";
                                 $bo->category_id="";
                              }
                              if($bo->is_product=='3'){
                                 $bo->fixed="";
                                 $bo->category_id="";
                                 $bo->new_price="";
                                 $bo->product_id="";
                              }
                           $getlang = FileMeta::where("model_id",$bo->id)->where("lang",Session::get("locale"))->where("model_name","Offer")->where("meta_key","title")->first();
                           $bo->title = isset($getlang)?$getlang->meta_value:'';
                           $getlang = FileMeta::where("model_id",$bo->id)->where("lang",Session::get("locale"))->where("model_name","Offer")->where("meta_key","main_title")->first();
                           $bo->main_title = isset($getlang)?$getlang->meta_value:'';
                              $best[]=$bo;
                      }
                    }
                    $normaloffer=Offer::where("offer_type","2")->orderby('id',"DESC")->get();
                   
                    foreach ($normaloffer as $bo) {
                        $start_date=date("Y-m-d",strtotime($bo->start_date)); 
                        $end_date=date("Y-m-d",strtotime($bo->end_date));

                        if(($date>=$start_date)&&($date<=$end_date)){
                         
                              if($bo->is_product=='1'){
                                 $bo->new_price="";
                                 $bo->product_id="";
                              }
                              if($bo->is_product=='2'){
                                 $bo->fixed="";
                                 $bo->category_id="";
                              }
                              if($bo->is_product=='3'){
                                 $bo->fixed="";
                                 $bo->category_id="";
                                 $bo->new_price="";
                                 $bo->product_id="";
                              }
                              $getlang = FileMeta::where("model_id",$bo->id)->where("lang",Session::get("locale"))->where("model_name","Offer")->where("meta_key","title")->first();
                              $bo->title = isset($getlang)?$getlang->meta_value:'';
                               $getlang = FileMeta::where("model_id",$bo->id)->where("lang",Session::get("locale"))->where("model_name","Offer")->where("meta_key","main_title")->first();
                               $bo->main_title = isset($getlang)?$getlang->meta_value:'';
                                $normal[]=$bo;
                        }
                    }
                   
                    $product=DB::table('file_meta')
                               ->select('file_meta.model_id as id', 'file_meta.meta_value as name','products.MRP','products.price','products.basic_image','products.selling_price','products.discount','products.product_color','products.special_price_start','products.special_price_to')
                               ->join('products', 'products.id', '=', 'file_meta.model_id')
                               ->where('products.is_deleted','0')
                               ->where("file_meta.lang",Session::get('locale'))
                               ->where("file_meta.model_name","Product")
                               ->where("file_meta.meta_key","name")
                               ->where("file_meta.meta_value","like","%".$request->get("search")."%")
                               ->where('products.status','1')
                               ->get();
                    $main=array();
                    foreach ($product as $k) {
                      $start_date=date("Y-m-d",strtotime($k->special_price_start)); 
                      $end_date=date("Y-m-d",strtotime($k->special_price_to)); 
                      if(($date>=$start_date)&&($date<=$end_date)){
                          $k->name=$k->name;
                          $getreview=Review::where("product_id",$k->id)->get();
                          $k->total_review=count($getreview);
                          $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                          $k->avgStar=round($avgStar);
                          $wish=Wishlist::where("product_id",$k->id)->where("user_id",$user_id)->get();
                          $k->wish=count($wish);
                         
                          $k->price=$k->selling_price;
                          $k->basic_image=asset("public/upload/product")."/".$k->basic_image;
                          unset($k->selling_price);
                          unset($k->special_price_start);
                          unset($k->selling_price);
                          unset($k->special_price_to);
                          $main[]=$k;
                      } 
                     } 
                      $found_data=array();
                      if(count($main) > 0){
                         $found_data = array_slice($main,(($page_no-1)*10),10);
                          if(count($found_data) > 0){
                            $data=array("big_offer"=>$best,"normal_offer"=>$normal,"product"=>$found_data,"sensonal_offer"=>$sen_offer);
                          } else {
                            $data=array("big_offer"=>$best,"normal_offer"=>$normal,"product"=>$found_data,"sensonal_offer"=>$sen_offer);
                          }
                      } else {
                         $data=array("big_offer"=>$best,"normal_offer"=>$normal,"product"=>$found_data,"sensonal_offer"=>$sen_offer);
                      }
                      $response = array("status" =>1, "msg" => __("messages.Offer Data"),"offerdata"=>$data);
           }
           return Response::json(array("data"=>$response));

       
       
       return Response::json(array("data"=>$response));
   }

   public function searchproduct(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'search' => 'required',
                      'lang'=>'required',
                       'id'=>'required'
                    ];                    
            $messages = array(
                      'search.required' => "search is required",
                      'lang.required'=>'lang is required',
                      'id.required'=>'id is required'
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
                  $user_id=$request->get("user_id");
                  App::setlocale($request->get("lang"));
                  session()->put('locale',$request->get("lang"));
                  if($request->get("id")==0){
                      if($request->get("seller_id")){
                          $product=DB::table('file_meta')
                               ->select('file_meta.model_id as id', 'file_meta.meta_value as name','products.MRP','products.price','products.basic_image','products.selling_price','products.discount','products.product_color','products.status','products.stock')
                               ->join('products', 'products.id', '=', 'file_meta.model_id')
                               ->where('products.is_deleted','0')
                               ->where("file_meta.lang",Session::get('locale'))
                               ->where("file_meta.model_name","Product")
                               ->where("file_meta.meta_key","name")
                               ->where("file_meta.meta_value","like","%".$request->get("search")."%")
                               //->where('products.status','1')
                               ->where('products.user_id',$request->get("seller_id"))
                               ->paginate(10);
                      }else{
                          $product=DB::table('file_meta')
                               ->select('file_meta.model_id as id', 'file_meta.meta_value as name','products.MRP','products.price','products.basic_image','products.selling_price','products.discount','products.product_color')
                               ->join('products', 'products.id', '=', 'file_meta.model_id')
                               ->where('products.is_deleted','0')
                               ->where("file_meta.lang",Session::get('locale'))
                               ->where("file_meta.model_name","Product")
                               ->where("file_meta.meta_key","name")
                               ->where("file_meta.meta_value","like","%".$request->get("search")."%")
                               ->where('products.status','1')
                               ->paginate(10);
                      }
                      
                  }else{
                       if($request->get("seller_id")){
                           $product=DB::table('file_meta')
                               ->select('file_meta.model_id as id', 'file_meta.meta_value as name','products.MRP','products.price','products.basic_image','products.selling_price','products.discount','products.product_color')
                               ->join('products', 'products.id', '=', 'file_meta.model_id')
                               ->where('products.is_deleted','0')
                               ->where("file_meta.lang",Session::get('locale'))
                               ->where("file_meta.model_name","Product")
                               ->where("file_meta.meta_key","name")
                               ->where("file_meta.meta_value","like","%".$request->get("search")."%")
                               ->where('products.status','1')
                               ->where('products.user_id',$request->get("seller_id"))
                               ->where('products.category',$request->get('id'))
                               ->paginate(10);
                       }else{
                           $product=DB::table('file_meta')
                               ->select('file_meta.model_id as id', 'file_meta.meta_value as name','products.MRP','products.price','products.basic_image','products.selling_price','products.discount','products.product_color')
                               ->join('products', 'products.id', '=', 'file_meta.model_id')
                               ->where('products.is_deleted','0')
                               ->where("file_meta.lang",Session::get('locale'))
                               ->where("file_meta.model_name","Product")
                               ->where("file_meta.meta_key","name")
                               ->where("file_meta.meta_value","like","%".$request->get("search")."%")
                               ->where('products.status','1')
                               
                               ->where('products.category',$request->get('id'))
                               ->paginate(10);
                       }
                      
                  }
                  
                             
                 foreach ($product as $k) {
                         $option=ProductOption::where("product_id",$k->id)->first();
                         $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                         if($avgStar==""){
                            $k->ratting=0.00;
                         }
                         else{
                           $k->ratting=number_format($avgStar,2,'.','');
                         }
                         
                         $wish=Wishlist::where("product_id",$k->id)->where("user_id",$user_id)->get();
                         $k->wish=count($wish);
                         $re=Review::where("product_id",$k->id)->get();
                         $k->totalreview=count($re);
                         $k->basic_image=asset('public/upload/product/').'/'.$k->basic_image;
                         $k->price=$k->price;
                          $options=ProductOption::where("product_id",$k->id)->groupBy('option_id')->get();
                                        $data1 = array();
                                        $i=0;
                                        foreach ($options as $k1) {
                                            $d1 = ProductOption::where("product_id",$k->id)->where("option_id",$k1->option_id)->where("lang",Session::get("locale"))->first();
                                            if($d1){
                                                $data1[$i]['optionname'] = $k1->name;
                                                $data1[$i]['type'] = $k1->type;
                                                $data1[$i]['required'] = $k1->is_required;
                                                 $la = explode("#",$k1->label);
                                                 $pr = explode("#",$k1->price);
                                                 $j = 0;
                                                 
                                                 foreach ($pr as $p) {
                                                     $a = array();
                                                    $a['label'] = $la[$j];
                                                    $a['price'] = $p;
                                                    $data1[$i]['optionvalues'][] =  $a;
                                                    //$data[$i]['optionvalues'][]['price']=$p;
                                                    $j++;
                                                 }
                                                $i++;
                                            }
                                        }
                                         $k->options = $data1;
                         unset($k->selling_price);
                }

                $response = array("status" =>1, "msg" => __("messages.Search Result"),"data"=>$product);
           }
           return Response::json(array("data"=>$response));
   }

   public function addcomplain(Request $request){
            $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'user_id' => 'required',
                      'description'=>'required',
                      'complain_type'=>'required',
                      'lang'=>'required'             
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                      'description.required' => "description is required",
                      'complain_type.required' => "complain_type is required",
                      'lang.required'=>'lang is required'
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
                $getuser=User::find($request->get("user_id"));
                if($getuser){

                   $product=new Complain();
                   $product->email=$getuser->email;
                   $product->user_id=$request->get("user_id");
                   $product->description=$request->get("description");
                   $product->report_error=$request->get("complain_type");
                   $product->save();                 
                   $response = array("status" =>1, "msg" => __("messages.Complain Add Successfully"),"data"=>$product);
                }else{
                  $response = array("status" =>0, "msg" => __("messages.User Not Found"));
                }
          }
           return Response::json(array("data"=>$response));
   }

   public function save_token(Request $request){

            $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'token' => 'required',
                      'type'=>'required'             
                    ];                    
            $messages = array(
                      'token.required' => "token is required",
                      'type.required' => "type is required"
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
                if($request->get("token")!=""&&$request->get("type")!=""&&$request->get("token")!="null"){
                     $store=new Token();
                     $store->token=$request->get("token");
                     $store->type=$request->get("type");
                     $store->save();
                     $response = array("status" =>1, "msg" => "Token Save Successfully","data"=>$store);
                }
                else{

                 $response = array("status" =>0, "msg" => "Fields is Required");
                }
                
          }
           return Response::json(array("data"=>$response));
   }
   
   public function viewpage(Request $request){
            $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'id' => 'required',
                      'lang'=>'required'             
                    ];                    
            $messages = array(
                      'id.required' => "id is required",
                      'lang.required' => "lang is required"
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
                       $page=Pages::find($request->get("id"));
                       App::setlocale($request->get("lang"));
                       session()->put('locale', $request->get("lang"));
                       if($page){
                            $getlang = FileMeta::where("model_id",$request->get("id"))->where("lang",$request->get("lang"))->where("model_name","Pages")->where("meta_key","page_name")->first();
                            $page->page_name = isset($getlang)?$getlang->meta_value:'';
                            $getlang = FileMeta::where("model_id",$request->get("id"))->where("lang",$request->get("lang"))->where("model_name","Pages")->where("meta_key","description")->first();
                            $page->description = isset($getlang)?$getlang->meta_value:'';
                            $response = array("status" =>1, "msg" => __("messages.Page Found"),"page"=>$page);
                       }
                       else{
                           $response = array("status" =>0, "msg" => __("messages.Page not found"),"page"=>array());
                       }                
            }
           return Response::json(array("data"=>$response));
   }
   
   


   public function getbannerfrombrand(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang'=>'required'                
                    ];                    
            $messages = array(
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
            } else {
                      $city=$request->get("city");
                      App::setlocale($request->get("lang"));
                      session()->put('locale', $request->get("lang"));
                      if($city!=""){
                          $gettext=Brand::where("image","!=","")->select("id","image")->where("city",$city)->orderby('id')->paginate(10); 
                      }else{
                          $gettext=Brand::where("image","!=","")->select("id","image")->orderby('id')->paginate(10); 
                      }
                      if(count($gettext)>0){
                          foreach ($gettext as $k) {
                            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Brand")->where("meta_key","name")->first();
                            $k->brand_name = isset($getlang)?$getlang->meta_value:'';
                            $k->image=asset('public/upload/category/banner').'/'.$k->image;
                          }
                          $response = array("status" =>1, "msg" => __("messages.Brand Banner get Successfully"),"data"=>$gettext);
                      }else{
                          $response = array("status" =>0, "msg" => __("messages.Data Not Found"),"data"=>array());
                      }
           }
           return Response::json(array("data"=>$response));                   
   }

   public function searchsuggestion(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'search' => 'required',
                      'lang'=>'required'                
                    ];                    
            $messages = array(
                      'search.required' => "search is required",
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
            } else {
                     
                      $search = $request->get("search");
                      App::setlocale($request->get("lang"));
                      session()->put('locale',$request->get("lang"));
                      if($request->get("id")!=0){
                          $product=DB::table('file_meta')
                               ->select('file_meta.model_id as id', 'file_meta.meta_value as name')
                               ->join('products', 'products.id', '=', 'file_meta.model_id')
                               ->where('products.is_deleted','0')
                               ->where('products.category',$request->get("id"))
                               ->where("file_meta.lang",Session::get('locale'))
                               ->where("file_meta.model_name","Product")
                               ->where("file_meta.meta_key","name")
                               ->where("file_meta.meta_value","like","%".$search."%")
                               ->where('products.status','1')
                               ->paginate(10); 
                      }else{
                          $product=DB::table('file_meta')
                               ->select('file_meta.model_id as id', 'file_meta.meta_value as name')
                               ->join('products', 'products.id', '=', 'file_meta.model_id')
                               ->where('products.is_deleted','0')
                               ->where("file_meta.lang",Session::get('locale'))
                               ->where("file_meta.model_name","Product")
                               ->where("file_meta.meta_key","name")
                               ->where("file_meta.meta_value","like","%".$search."%")
                               ->where('products.status','1')
                               ->paginate(10); 
                      }
                      
                      $response = array("status" =>1, "msg" => __("messages.Search Result"),"data"=>$product);
           }
           return Response::json(array("data"=>$response));
   }
   
   public function listofcity(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required'                
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required"
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
                     $citydata=City::where("is_delete",'0')->select("id")->get();
                     $language = Lang_core::all();
                     App::setlocale($request->get("lang"));
                     session()->put('locale', $request->get("lang"));
                     if(count($citydata)>0){
                        foreach ($citydata as $k) {
                             $getmeta = FileMeta::where("model_id",$k->id)->where("model_name","City")->where("meta_key","name")->where("lang",$request->get("lang"))->first();
                             $k->name = isset($getmeta->meta_value)?$getmeta->meta_value:'';                            
                        }
                        $response = array("status" =>1, "msg" => __("messages.Get List Of City"),"citylist"=>$citydata);
                     }else{
                        $response = array("status" =>0, "msg" => __("messages.Not Found"));
                     }
           }
           return Response::json(array("data"=>$response));
   }
   
   public function sendsms(Request $request){
       return $this->sendotpmsg($request);
   }

   public function getlang(){
        $lang = Lang_core::select('id','name','code','is_rtl')->get();
        App::setlocale('en');
        session()->put('locale', 'en');
        $response = array("status" =>1, "msg" => __("messages.Get List Of Language"),"lang"=>$lang);
         $headers=array("Access-Control_Allow_Origin"=>"*","Accept"=>"application/json","Access-Control-Allow-Credentials"=>true,"Access-Control-Allow-Headers"=>"Origin,Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token,locale","Access-Control-Allow-Methods"=>"POST, OPTIONS");
          
          
        //return Response::json(array("data"=>$response,"header"=>$headers));
        return json_encode(array("data"=>$response,"header"=>$headers), JSON_NUMERIC_CHECK);
   }
   
   
   
   
   
   
   
   
    
   
   public function searchcategorybypro(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required',
                      'user_id'=>'required',
                      'lang'=>'required'
                    ];                    
            $messages = array(
                      'id.required' => "id is required",
                      'user_id.required'=>"user_id is required",
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
            } else {
                     $getdata=Product::where("category",$request->get("id"))->select("id","MRP","price","basic_image","selling_price","discount","product_color")->paginate(10);
                     $user_id = $request->get("user_id");
                     $language = Lang_core::all();
                     App::setlocale($request->get("lang"));
                     session()->put('locale', $request->get("lang"));
                     if($getdata){
                       foreach ($getdata as $k) {
                         $option=ProductOption::where("product_id",$k->id)->first();
                         $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                         $k->ratting=round($avgStar);
                         $wish=Wishlist::where("product_id",$k->id)->where("user_id",$user_id)->get();
                         $k->wish=count($wish);
                         $re=Review::where("product_id",$k->id)->get();
                         $k->totalreview=count($re);
                         $k->basic_image=asset('public/upload/product/').'/'.$k->basic_image;
                         $k->price=$k->selling_price;
                         $getlang = FileMeta::where("model_id",$k->id)->where("lang",$request->get("lang"))->where("model_name","Product")->where("meta_key","name")->first();
                         $k->name = isset($getlang)?$getlang->meta_value:'';
                         unset($k->selling_price);
                }
                        $response = array("status" =>1, "msg" => __("messages.product get successfully"),"data"=>$getdata);
                     }else{
                        $response = array("status" =>0, "msg" => __("messages.Not Found"));
                     }
           }
           return Response::json(array("data"=>$response));
   }
   
   public function offerfilter(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'value' => 'required',
                      'type'=>'required',
                      'lang'=>'required'
                    ];                    
            $messages = array(
                      'value.required' => "value is required",
                      'type.required'=>"type is required",
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
            } else {
                   
                     App::setlocale($request->get("lang"));
                     session()->put('locale', $request->get("lang"));
                     if($request->get("type")==1){ // category
                         $getcategory = Categories::find($request->get("value"));
                         if($getcategory->parent_category=="0"){//category
                            $product = Product::where("category",$getcategory->id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->paginate(10);
                         }else{//subcategory
                             $product = Product::where("subcategory",$getcategory->id)->where("status",'1')->orderby("id",'DESC')->where("is_deleted",'0')->paginate(10);
                         }
                         
                          foreach ($product as $k) {
                                 $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get("locale"))->where("model_name","Product")->where("meta_key","name")->first();
                                                         $k->name = isset($getlang)?$getlang->meta_value:'';
                                $getreview=Review::where("product_id",$k->id)->get();
                                $k->total_review=count($getreview);
                                $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                                $k->avgStar=round($avgStar);
                                $wish=Wishlist::where("product_id",$k->id)->where("user_id",$request->get("user_id"))->get();
                                $k->wish=count($wish);
                                $pricelist[]=$k->price;
                                $k->disper=$k->discount;
                                $k->price=$k->selling_price;  
                                
                                    $options=ProductOption::where("product_id",$k->id)->groupBy('option_id')->get();
                                    $data = array();
                                    $i=0;
                                    foreach ($options as $k1) {
                                        $d1 = ProductOption::where("product_id",$k->id)->where("option_id",$k1->option_id)->where("lang",Session::get("locale"))->first();
                                        if($d1){
                                            $data[$i]['optionname'] = $k1->name;
                                            $data[$i]['type'] = $k1->type;
                                            $data[$i]['required'] = $k1->is_required;
                                             $la = explode("#",$k1->label);
                                             $pr = explode("#",$k1->price);
                                             $j = 0;
                                             
                                             foreach ($pr as $p) {
                                                 $a = array();
                                                $a['label'] = $la[$j];
                                                $a['price'] = $p;
                                                $data[$i]['optionvalues'][] =  $a;
                                                //$data[$i]['optionvalues'][]['price']=$p;
                                                $j++;
                                             }
                                            $i++;
                                        }
                                    }
                                    $k->options = $data;
                            }
                             $response = array("status" =>1, "msg" => __("messages.product get successfully"),"data"=>$product);
                     }else{ // coupon
                            $getcode=Coupon::where("code",$request->get("value"))->first();
                            
                            $date=date("Y-m-d");
                            if($getcode){
                                $arr = explode(",",$getcode->product);
                                foreach($arr as $a){
                                    $product[] = Product::find($a);
                                }
                               // echo "<pre>";print_r($ls);exit;
                                    $start_date=date("Y-m-d",strtotime($getcode->start_date)); 
                                    $end_date=date("Y-m-d",strtotime($getcode->end_date));
                                    if(($date>=$start_date)&&($date<=$end_date)){ 
                                        $searls=array();             
                                        if($getcode->coupon_on=='0'){//product
                                            foreach ($product as $k) {
                                              $products=explode(",",$getcode->product);
                                              if(in_array($k->id,$products)){
                                                    $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get("locale"))->where("model_name","Product")->where("meta_key","name")->first();
                                                             $k->name = isset($getlang)?$getlang->meta_value:'';
                                                    $getreview=Review::where("product_id",$k->id)->get();
                                                    $k->total_review=count($getreview);
                                                    $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                                                    $k->avgStar=round($avgStar);
                                                    $wish=Wishlist::where("product_id",$k->id)->where("user_id",$request->get("user_id"))->get();
                                                    $k->wish=count($wish);
                                                    $pricelist[]=$k->price;
                                                    $k->disper=$k->discount;
                                                    $k->price=$k->selling_price;    $options=ProductOption::where("product_id",$k->id)->groupBy('option_id')->get();
                                        $data = array();
                                        $i=0;
                                        foreach ($options as $k1) {
                                            $d1 = ProductOption::where("product_id",$k->id)->where("option_id",$k1->option_id)->where("lang",Session::get("locale"))->first();
                                            if($d1){
                                                $data[$i]['optionname'] = $k1->name;
                                                $data[$i]['type'] = $k1->type;
                                                $data[$i]['required'] = $k1->is_required;
                                                 $la = explode("#",$k1->label);
                                                 $pr = explode("#",$k1->price);
                                                 $j = 0;
                                                 
                                                 foreach ($pr as $p) {
                                                     $a = array();
                                                    $a['label'] = $la[$j];
                                                    $a['price'] = $p;
                                                    $data[$i]['optionvalues'][] =  $a;
                                                    //$data[$i]['optionvalues'][]['price']=$p;
                                                    $j++;
                                                 }
                                                $i++;
                                            }
                                        }
                                        $k->options = $data;
                                                    $searls[]=$k;
                                              }
                                            }
                                        }
                                        $data = $this->paginate($product);
                                         $response = array("status" =>1, "msg" => __("messages.product get successfully"),"data"=>$data);
                                        
                                    }else{
                                            $response = array("status" =>0, "msg" => __("messages.Not Found"));
                                   }
                            }else{
                                $response = array("status" =>0, "msg" => __("messages.Code Not Found"));
                            }
                           
                     }
                     
                    
           }
           return Response::json(array("data"=>$response));
   }
   
    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    
    
    public function verified_coupon_code(Request $request)
    {
        $response = array("success" => "0", "discount" => "Not Set");
           $rules = [
                      'coupon_code' => 'required',
                      'res_id' => 'required',
                      'user_id' => 'required',
                      'total' => 'required',
                      'product'=>'required',
                      'lang'=>'required'             
                    ];                    
            $messages = array(
                      'coupon_code.required' => "coupon_code is required",
                      'res_id.required' => "res_id is required",
                      'user_id.required' => "user_id is required",
                      'total.required' => "total is required",
                      'product.required'=>"Product is required",
                      'lang.required'=>'lang is required'
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                  $message = '';
                  $messages_l = json_decode(json_encode($validator->messages()), true);
                  foreach ($messages_l as $msg) {
                         $message .= $msg[0] . ", ";
                  }
                  $response['discount'] = $message;
            } else {
                 App::setlocale($request->get("lang"));
                 session()->put('locale', $request->get("lang"));
                 $date=date("Y-m-d");
                 $data=Coupon::where("code",$request->get("coupon_code"))->where("user_id",$request->get("res_id"))->where("status",'1')->first();
                 if(!$data){
                   $response = array("status" =>0, "discount" => __("messages.Coupon Not Found"));
                 }
                else{
                     /* $start_date=date("Y-m-d",strtotime($data->start_date)); 
                      $end_date=date("Y-m-d",strtotime($data->end_date));

                        if(($date>=$start_date)&&($date<=$end_date))
                        {*/
                        
                        $order=OrderData::where("coupon_code",$request->get("coupon_code"))->get();
                        
                        $orderuser=DB::table('order_data')
                           ->select('order_data.id')
                           ->join('order_record', 'order_record.id', '=', 'order_data.order_id')
                           ->where('order_data.coupon_code',$request->get("coupon_code"))
                           ->where('order_record.user_id',$request->get("user_id"))
                           ->get();

                              $temp=0;
                              $arr=explode(",",$request->get("product"));
                              if($data->coupon_on=='1')
                              {
                                $codepro=explode(",", $data->categories);
                                foreach ($arr as $k) {
                                  $getcategory=Product::find($k);  
                                                                
                                  if(in_array($getcategory->category,$codepro)){
                                      $temp=1;
                                  }
                                }
                              
                              }
                              else{                                 
                                 $codepro=explode(",", $data->product);
                                 foreach ($arr as $k) {
                                      if(in_array($k,$codepro)){
                                          $temp=1;
                                      }
                                 }
                              }
                              if($temp==0){
                                   $response = array("status" =>0, "discount" => __("messages.Coupon Invaild"));
                                   return Response::json(array("data"=>$response));
                              }
                         
                        if($data->usage_limit_per_coupon!=""&&($data->usage_limit_per_coupon<count($order))){
                              $response = array("status" =>0, "discount" =>__("messages.Coupon Limit Over"));
                        }
                        elseif($data->usage_limit_per_customer!=""&&($data->usage_limit_per_customer<=count($orderuser))){
                              $response = array("status" =>0, "discount" => __("messages.Your Coupon Limit Over"));
                        }
                        elseif($data->minmum_spend!=""&&$data->minmum_spend>$request->get("total")){
                             $response = array("status" =>0, "discount" => __("messages.Not Vaild Coupon,total less than minimum amount of coupon"));
                        }
                        elseif($data->maximum_spend!=""&&$data->maximum_spend<=$request->get("total")){
                                 $response = array("status" =>0, "discount" => __("messages.Not Valid Coupon,total greater than maximum amount of coupon"));
                        }
                        else{

                              $temp=0;
                              $arr=explode(",",$request->get("product"));
                              if($data->coupon_on=='1'){
                                $codepro=explode(",", $data->categories);

                                foreach ($arr as $k) {
                                  $getcategory=Product::find($k);  
                                                                
                                  if(in_array($getcategory->category,$codepro)){
                                          $temp=1;
                                  }
                                }
                              
                              }
                              else{                                 
                                 $codepro=explode(",", $data->product);
                                 foreach ($arr as $k) {
                                      if(in_array($k,$codepro)){
                                          $temp=1;
                                      }
                                 }
                              }
                              if($temp==1){
                                  if($data->discount_type=='1'){
                                   $discount=($request->get("total")*$data->value)/100;
                                  }
                                  else{
                                     $discount=$data->value;
                                  }
                                 $data=array("discount_price"=>$discount,"freeshipping"=>$data->free_shipping);
                                     $response = array("status" =>1,"discount"=>$data);
                              }
                              else{
                                $response = array("status" =>0, "discount" => __("messages.Coupon Invaild"));
                              }
                             
                           }
                        /*}
                        else{
                          $response = array("status" =>0, "discount" => __("messages.Coupon Invaild"));
                        }*/
                      }
           }
           return Response::json(array("data"=>$response));
   }  
    public function couponlistforuser(Request $request){
           $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'lang'=>'required'
                      
                    ];
            $messages = array(
                      'lang.required' => "lang is required"
                      
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

                        
                         $coupon=Coupon::select('id','image','user_id','coupon_on','code','name','discount_type','value','categories','product','created_at','updated_at')->where("is_deleted",'0')->orderby('id','DESC')->paginate(10);
                         
                         if(count($coupon)>0){
                             
                             $response = array("status" =>1, "msg" => "Get Coupon List","data"=>$coupon);
                         }else{
                             $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                         }
                         
            }
            return Response::json(array("data"=>$response));
   }
   
   public function about(){
        $data=About::find(1);
        
        unset($data->trems);
        unset($data->privacy);
        unset($data->data_deletion);
        if($data){
              $response['status']= 1;
              $response['msg']="About List";
              $response['data']=$data;
              
          }else{
               $data3 =array();
               $response['status']= 0;
               $response['message']="Data Not Found";
               $response['data'] = $data;               
          }
        return Response::json($response);
   }
   
   public function privacy(){
       $data=About::find(1);
       unset($data->privacy);
        unset($data->about);
        unset($data->data_deletion);
       if($data){
              $response['status']= 1;
              $response['msg']="Privecy List";
              $response['data']=$data;
              
          }else{
               $data3 =array();
               $response['status']= 0;
               $response['message']="Data Not Found";
               $response['data'] = $data;               
          }
        return Response::json($response);
   }

}
?>

