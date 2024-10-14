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
use App\Models\Categories;
use App\Models\Taxes;
use App\Models\Brand;
use App\Models\OrderData;
use App\Models\Order;
use App\Models\FileMeta;
use App\Models\Coupon;
use App\Models\Addresses;
use App\Models\Setting;
use App\Models\Lang_core;
use App\Models\ProductAttributes;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\DeliveryboyReject;
use DateTimeZone;
use DateTime;
use Session;
use Image;
use Mail;
use App;
use DB;
class SellerController extends Controller {

  public function postregister(Request $request){ 
     $response = array("status" => "0", "msg" => "Validation error");
     $rules = [
                'name' => 'required',
                'email' => 'required',
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
                $user = User::where("email",$request->get("email"))->where("user_type",'3')->first();
                 if(empty($user)){
                   
                        $otp = random_int(100000, 999999);
                        $user=new User();
                        $user->first_name=$request->get("name");
                        $user->email=$request->get("email");
                        $user->password=$request->get("password");
                        $user->phone=$request->get("phone");
                        
                        $user->access_cat = $request->get("category");
                        
                        if ($request->hasFile('res_image')) 
                        {
                           $file = $request->file('res_image');
                           $filename = $file->getClientOriginalName();
                           $extension = $file->getClientOriginalExtension() ?: 'png';
                           $folderName = '/upload/restaurant';
                           $picture = "restaurant_".time() . '.' . $extension;
                           $destinationPath = public_path() . $folderName;
                           $request->file('res_image')->move($destinationPath, $picture);
                            
                           $user->res_image=$picture;
                       }
                        $user->user_type = '3';
                        $user->login_otp = $otp;
                        $user->save();
                        $gettoken=Token::where("token",$request->get("token"))->update(["seller_id"=>$user->id]);
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
                  $response = array("status" =>0, "msg" => __("messages.Email ID Already Exists"));
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
                      'email.required' => "email is required",
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
                              $delivery=User::where("phone",$user->phone)->where("password",$user->password)->where("user_type",'3')->where("is_active",'1')->first();
                              if($delivery){
                                    $gettoken=Token::where("token",$request->get("token"))->first();
                                      if(!$gettoken){
                                             $store=new Token();
                                             $store->token=$request->get("token");
                                             $store->type=$request->get("token_type");
                                             $store->seller_id=$delivery->id;
                                             $store->save();
                                     }
                                      else{
                                             $gettoken->seller_id=$delivery->id;
                                             $gettoken->save();
                                      }
                                    $response = array("status" =>1, "msg" => __("messages.Login Successfully"),"data"=>$delivery);
                                }else{
                                    
                                     $delivery = new User();
                                     $delivery->first_name = $user->first_name;
                                     $delivery->email = $user->email;
                                     $delivery->phone = $user->phone;
                                     $delivery->password = $user->password;
                                     $delivery->user_type = '3';
                                     $delivery->save();
                                     $gettoken=Token::where("token",$request->get("token"))->first();
                                      if(!$gettoken){
                                             $store=new Token();
                                             $store->token=$request->get("token");
                                             $store->type=$request->get("token_type");
                                             $store->seller_id=$delivery->id;
                                             $store->save();
                                     }
                                      else{
                                             $gettoken->seller_id=$delivery->id;
                                             $gettoken->save();
                                      }
                                      $data = User::find($delivery->id);
                                      $data->password = $user->password;
                                    $response = array("status" =>1, "msg" => __("messages.Login Successfully"),"data"=>$data);
                                }
                          }else{
                               $response = array("status" =>0, "msg" => __("messages.Login Credentials Are Wrong"));
                          }
                          
                      }
                      
                      if($request->get("login_type")==4){
                           $user=User::where("phone",$request->get("phone"))->where("password",$request->get("password"))->where("user_type",'3')->where("is_active",'1')->first();
                          
                          if($user){
                                $gettoken=Token::where("token",$request->get("token"))->first();
                                  if(!$gettoken){
                                         $store=new Token();
                                         $store->token=$request->get("token");
                                         $store->type=$request->get("token_type");
                                         $store->seller_id=$user->id;
                                         $store->save();
                                 }
                                  else{
                                         $gettoken->seller_id=$user->id;
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
  public function show_editprofile(Request $request){
     
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      "id"=>"required",
                      'name' => 'required',
                      'email' => 'required',
                     
                      'phone'=>'required',
                      "lang"=>"required",
                      "delivery_time"=>"required",
                      "category"=>"required",
                      "two_person_cost"=>"required",
                      "res_time"=>"required"
                    ]; 
                    
                    
            $messages = array(
                    "id.required"=>"id is required",
                    'name.required' => "name is required",
                    'email.required' => "email is required",
                    'phone.required'=>"phone is required",
                    "lang.required"=>"lang is required",
                    "delivery_time.required"=>"delivery_time is required",
                    "two_person_cost.required"=>"two_person_cost is required",
                    "lat.required"=>"lat is required",
                    "category.required"=>"category required",
                    "long.required"=>"long is required",
                    "res_time.required"=>"res_time is required"
            );

           
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ",";
                }
                $response['msg'] = $message;
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                $user =User::find($request->get("id"));
                 if($user){
                        
                        if ($request->hasFile('res_image')) 
                        {
                           $file = $request->file('res_image');
                           $filename = $file->getClientOriginalName();
                           $extension = $file->getClientOriginalExtension() ?: 'png';
                           $folderName = '/upload/restaurant';
                           $picture = "restaurant_".time() . '.' . $extension;
                           $destinationPath = public_path() . $folderName;
                           $request->file('res_image')->move($destinationPath, $picture);
                            $image_path = public_path() ."/upload/restaurant/".$user->res_image;
                                  if(file_exists($image_path)&&$user->res_image!="") {
                                      try {
                                           unlink($image_path);
                                      }
                                      catch(Exception $e) {
                                        
                                      }                        
                                  }
                           $user->res_image=$picture;
                       }
                            $user->first_name=$request->get("name");
                            $user->email=$request->get("email");
                            if($request->get("password") !="")
                            {
                                $user->password=$request->get("password");
                            }
                            
                            $user->phone=$request->get("phone");
                            $user->delivery_time=$request->get("delivery_time");
                            $user->two_person_cost=$request->get("two_person_cost");
                            $user->access_cat=$request->get("category");
                            if($request->get("address")!=""){
                                $user->address=$request->get("address");
                            }
                            if($request->get("area")!=""){
                                $user->area=$request->get("area");
                            }
                             if($request->get("city")!=""){
                                $user->city=$request->get("city");
                            }
                             if($request->get("country")!=""){
                                $user->country=$request->get("country");
                            }
                            if($request->get("pincode")!=""){
                                $user->pincode=$request->get("pincode");
                            }
                            $user->lat=$request->get("lat");
                            $user->long=$request->get("long");
                            $user->res_time=$request->get("res_time");
                            $user->save();
                            $response = array("status" =>1, "msg" => __("messages.Profile Update Successfully"),"data"=>$user); 
                        
                 }
                 else{
                  $response = array("status" =>0, "msg" => __("messages.Something wrong"));
                 }                
           }
           return $response;
  }
  public function show_categorydelete(Request $request){
         $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'lang'=>'required',
                      'id'=>'required'
                    ];
            $messages = array(
                      "lang.required"=>"lang is required",
                      "id.required"=>"id is required"
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
                        $data = Categories::where('id',$request->get("id"))->where('is_delete','0')->first();
                        if(!empty($data)){
                            
                              $data->is_delete = '1';
                              $data->save();
                            $response = array("status" =>1, "msg" =>"Category Delete Successfully");
                        }else{
                            $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                        }
                         
                             
                        
                         
            }
            return Response::json($response);
  }
  public function add_category(Request $request){
            $response = array("status" => "0", "category" => "Validation error");
           $rules = [
                      'id' => 'required',
                      'user_id' => 'required',        
                      'cat_name' => 'required'        
                    ];                    
            $messages = array(
                      'id.required' => "id is required",
                      'cat_name.required' => "cat_name is required",
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
                $user_id=$request->get("user_id");
                    if($request->get("id")!="0")
                    {
                        
                      $store = Categories::where('res_id',$request->get("user_id"))->where("id",$request->get("id"))->first(); 
                         if(empty($store)) {
                           $response = array("status" =>0, "msg" =>"Data Not Found");
                           return Response::json(array("data"=>$response));
                        }
                       
                    }
                    else
                    {
                      $store=new Categories();
                    }
                    $store->cat_name = $request->get("cat_name");
                    $store->res_id =$user_id;
                    $store->parent_category =1;
                    $store->save();
                    
                    if($request->get("id")!="0")
                    {
                      $response = array("status" =>1, "msg" =>"Category Successfully Updated");
                        
                    }else{
                       $response = array("status" =>1, "msg" =>"Category Successfully Added");
                    }    
                     
           }
           return Response::json(array("data"=>$response));
  }
  public function post_saveproduct(Request $request){
         $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'id'=>'required',
                      'category'=>'required',
                      'lang'=>'required',
                      'name'=>'required',
                      'description'=>'required',
                      'price'=>'required',
                      'discount'=>'required',
                      'discount_type'=>'required',
                      'is_veg'=>'required',
                      'seller_id'=>'required'
                    ];
            $messages = array(
                      'id.required' => "id is required",
                      "category.required"=>"category is required",
                      "lang.required"=>"lang is required",
                      "name.required"=>"name is required",
                      "description.required"=>"description is required",
                      "price.required"=>"price is required",
                      "discount.required"=>"price is required",
                      "is_veg.required"=>"is_veg is required",
                      "seller_id.required"=>"seller_id is required"
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
                        
                        
                        if($request->get("id")!=0){
                            $store=Product::find($request->get("id"));
                            if(empty($store)){
                                $response = array("status" =>0, "msg" => __("messages.Product Not Found"));
                                return Response::json(array("data"=>$response));
                            }
                        }
                        else{
                            $store=new Product();
                        }
                        
                        $store->category=$request->get("category");
                        $store->price=$request->get("price");
                        $store->discount=$request->get("discount");
                        $store->discount_type=$request->get("discount_type");
                        $store->name=$request->get("name");
                        $store->description=$request->get("description");
                        $store->is_veg=$request->get("is_veg");
                        
                        $store->user_id=$request->get("seller_id");
                        $store->save();
                        
                        $response = array("status" =>1, "msg" => __("messages.Product Save Successfully"),"data"=>$store);
            }
            return Response::json(array("data"=>$response));
  }
 
  public function post_saveproductimage(Request $request){
           
             $response = array("success" => "0", "register" => "Validation error");
             $rules = [
                        'id'=>'required',
                        'lang'=>'required'
                      ];
              $messages = array(
                        'id.required' => "id is required",
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
                          $store=Product::find($request->get("id"));
                          if(empty($store)){
                                  $response = array("status" =>0, "msg" => __("messages.Product Not Found"));
                               return Response::json(array("data"=>$response));
                          }
                          
                          else{
                               $img_url=$store->basic_image;
                                      $rel_url=$store->image;         
                                      if ($request->file('image')) 
                                      {
                                         
                                                $file = $request->file('image');
                                               $filename = $file->getClientOriginalName();
                                               $extension = $file->getClientOriginalExtension() ?: 'png';
                                               $folderName ='/upload/product';
                                               $picture = "product_".time() . '.' . $extension;
                                               $destinationPath = public_path() . $folderName;
                                               $request->file('image')->move($destinationPath, $picture);
                                               $img_url =$picture;                
                                                $image_path = public_path() ."/upload/product/".$rel_url;
                                                  if(file_exists($image_path)&&$rel_url!="") {
                                                      try {
                                                           unlink($image_path);
                                                      }
                                                      catch(Exception $e) {
                                                        
                                                      }                        
                                                }
                                      }
                                      $store->basic_image=$img_url;
                                      $store->save();
                        
                           $response = array("status" =>1, "msg" => __("messages.Product Images Save Successfully"));
                    }
                    
              }
              return Response::json(array("data"=>$response));
  }
  public function post_saveproductoption(Request $request){
        $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'id'=>'required',
                      'option_json'=>'required',
                      'lang'=>'required',
                    ];
            $messages = array(
                      'id.required' => "id is required",
                      'option_json.required' => "option_json is required",
                      "lang.required"=>"lang is required",
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
                        $store=Product::find($request->get("id"));
                        if(empty($store)){
                             $response = array("status" =>0, "msg" => __("messages.Product Not Found"));
                             return Response::json(array("data"=>$response));
                        }
                        $data = json_decode($request->get("option_json"));
                        if(isset($data->data)){
                            $checkproattri=ProductOption::where("product_id",$request->get("id"))->delete();
                        }
                        $i=1;
                        foreach($data->data as $d){
                            $store=new ProductOption();    
                            $store->product_id=$request->get("id");
                            $store->option_id = $i++;
                            $store->name=$d->name;
                            $store->label=$d->label;
                            $store->min_item_selection=$d->min_item_selection;
                            $store->max_item_selection=$d->max_item_selection;
                            $store->price=$d->price;
                            $store->lang = $d->lang;
                            $store->save();
                        }
                        $response = array("status" =>1, "msg" => __("messages.Product Option Save Successfully"));
            }
            return Response::json(array("data"=>$response));
  }
  public function show_savecoupon(Request $request){
     $response = array("success" => "0", "register" => "Validation error");
     $rules = [
                'lang'=>'required',
                'id'=>'required',
                'name'=>'required',
                'user_id'=>'required',
                'code'=>'required',
                'discount_type'=>'required',
                'value'=>'required',
                'free_shipping'=>'required', 
                'coupon_on'=>'required',
                'description'=>'required',
                'is_enable'=>'required',
                'minmum_spend'=>'required',
                'maximum_spend'=>'required',
                'usage_limit_per_coupon'=>'required',
                'usage_limit_per_customer'=>'required',
              ];
                if($request->get('coupon_on') == "0"){
                  $rules['products'] = 'required';
              }
              if($request->get('coupon_on') == "1"){
                  $rules['category'] = 'required';
              }
      $messages = array(
                "lang.required"=>"lang is required",
                "id.required"=>"id is required",
                "name.required"=>"name is required",
                "user_id.required"=>"user_id is required",
                "code.required"=>"code is required",
                "discount_type.required"=>"discount_type is required",
                "value.required"=>"value is required",
                "free_shipping.required"=>"free_shipping is required",
                "coupon_on.required"=>"coupon_on is required",
                 "description.required"=>"description is required",
                "is_enable.required"=>"is_enable is required",
                "minmum_spend.required"=>"minmum_spend is required",
                "maximum_spend.required"=>"maximum_spend is required",
                "products.required"=>"products is required",
                "category.required"=>"category is required",
                "usage_limit_per_coupon.required"=>"usage_limit_per_coupon is required",
                "usage_limit_per_customer.required"=>"usage_limit_per_customer is required"
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
                  if($request->get("id")==0){
                      $data = new Coupon();
                  }else{
                      $data = Coupon::find($request->get("id"));
                      if(empty($data)){
                          $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                          return Response::json(array("data"=>$response));
                      }
                  }
                  if ($request->file('image')) {
     
                           $file = $request->file('image');
                           $filename = $file->getClientOriginalName();
                           $extension = $file->getClientOriginalExtension() ?: 'png';
                           $folderName = '/upload/offer/image/';
                           $picture = "offer_".time() . '.' . $extension;
                           $destinationPath = public_path() . $folderName;
                           $request->file('image')->move($destinationPath, $picture);
                           $img_url =$picture;
          
                          
                    }
                    else{
                        if($request->get("id")==0)
                        {
                            $img_url="";
                        }
                        else
                        {
                            $img_url=$data->image;
                        }
                             
                    }
                    $data->name=$request->get("name");
                    $data->code=$request->get("code");
                    $data->user_id = $request->get("user_id");
                    $data->discount_type=$request->get("discount_type");
                    $data->value=$request->get("value");
                    $data->free_shipping=$request->get("free_shipping");
                    $data->status=$request->get("is_enable");
                    $data->minmum_spend = $request->get("minmum_spend");
                    $data->maximum_spend=$request->get("maximum_spend");
                    $data->product=$request->get("products");
                    $data->categories=$request->get("category");
                    $data->coupon_on=$request->get("coupon_on");
                    $data->image=$img_url;
                    $data->description=$request->get("description");
                    $data->usage_limit_per_coupon = $request->get("usage_limit_per_coupon");
                    $data->usage_limit_per_customer=$request->get("usage_limit_per_customer");
                    $data->save();
                    
                    
                       $response = array("status" =>1, "msg" => __("messages.Coupon Save Successfully"),"data"=>$data);
                  
                   
      }
      return Response::json(array("data"=>$response));
  }
  public function show_productlistbystore(Request $request){
     $response = array("success" => "0", "register" => "Validation error");
     $rules = [
                'lang'=>'required',
                'seller_id'=>'required',
                'cat_id'=>'required'
              ];
      $messages = array(
                "lang.required"=>"lang is required",
                "seller_id.required"=>"seller_id is required",
                "cat_id.required"=>"cat_id is required"
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
                  
                    $data=Product::where("user_id",$request->get("seller_id"))->where("category",$request->get("cat_id"))->select('id','name','basic_image','status','stock','price')->where("is_deleted",'0')->orderby("id","DESC")->paginate(15);
                   if(count($data)>0){
                      $setting = Setting::find(1);
                      $currency = explode("-",$setting->default_currency);
                       $response = array("status" =>1, "msg" => __("messages.Get Product List Successfully"),"data"=>$data,"currency"=>isset($currency[1])?trim($currency[1]):'');
                   }else{
                       $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                   }
                   
      }
      return Response::json(array("data"=>$response));
  }
  public function show_listofcoupon(Request $request){
       $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'lang'=>'required',
                      'seller_id'=>'required'
                    ];
            $messages = array(
                      "lang.required"=>"lang is required",
                      "seller_id.required"=>"seller_id is required"
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
                        $data = Coupon::where("user_id",$request->get("seller_id"))->where("is_deleted",'0')->get();
                       
                        if(count($data)){
                          
                            $response = array("status" =>1, "msg" => __("messages.Coupon Get Successfully"),"data"=>$data);
                        }else{
                            $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                        }
                    }
            return Response::json(array("data"=>$response));
  }
  public function show_coupon_detail(Request $request){
         $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'lang'=>'required',
                      'id'=>'required'
                    ];
            $messages = array(
                      "lang.required"=>"lang is required",
                      "id.required"=>"id is required"
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
                        $data = Coupon::find($request->get("id"));
                        if(!empty($data)){
                            $response = array("status" =>1, "msg" => __("messages.Coupon Get Successfully"),"data"=>$data);
                        }else{
                            $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                        }
                         
                             
                        
                         
            }
            return Response::json($response);
  }
  public function show_listofoption(Request $request){
        $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'id'=>'required',
                      'lang'=>'required'
                    ];
            $messages = array(
                      'id.required' => "id is required",
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
                    
                          $store=ProductOption::select('option_id')->where("product_id",$request->get("id"))->orderby('option_id')->groupby('option_id')->get();
                         
                        if(empty($store)){
                             $response = array("status" =>0, "msg" => __("messages.Product Options Not Found"));
                             return Response::json($response);
                        }
                         $ls = array();
                        foreach($store as $s){
                            $st = ProductOption::where("product_id",$request->get("id"))->where("option_id",$s->option_id)->orderby('id')->get();
                            $k = array();
                            $k['set']=$s->option_id;
                            $k['options']=$st;
                            $ls[]=$k;
                        }
                        
                        $response = array("status" =>1, "msg" => __("messages.Product Options get Successfully"),"data"=>$ls);
            }
            return Response::json(array("data"=>$response));
  }
  public function show_productdelete(Request $request){
         $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'lang'=>'required',
                      'id'=>'required'
                    ];
            $messages = array(
                      "lang.required"=>"lang is required",
                      "id.required"=>"id is required"
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
                        $data = Product::find($request->get("id"));
                        if(!empty($data)){
                            
                              $data->is_deleted = '1';
                              $data->save();
                            $response = array("status" =>1, "msg" => __("messages.Product Delete Successfully"));
                        }else{
                            $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                        }
             }
            return Response::json($response);
  }
  public function show_coupondelete(Request $request){
         $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'lang'=>'required',
                      'id'=>'required'
                    ];
            $messages = array(
                      "lang.required"=>"lang is required",
                      "id.required"=>"id is required"
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
                        $data = Coupon::find($request->get("id"));
                        if(!empty($data)){
                            
                            $data->is_deleted = '1';
                            $data->save();
                            $response = array("status" =>1, "msg" => __("messages.Coupon Delete Successfully"));
                        }else{
                            $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                        }
                         
                         
            }
            return Response::json($response);
  }
  public function get_populer_restaurants(Request $request){
        
         $response = array("status" => "0", "msg" => "Validation error");

          $getrestaurant= User::select('id','first_name','email','phone','address','res_image','delivery_time','review_count','two_person_cost','area','city','country')->where("user_type",3)->orderBy('review_count','DESC')->limit(10)->get();
          $status="1";
         
          foreach ($getrestaurant as $value) 
          {
            
            if(isset($value->review_count) && !empty($value->review_count))
            {
              $value->review_count=$value->review_count;
            }else{
              $value->review_count=0;
            }

            if(isset($value->two_person_cost) && !empty($value->two_person_cost))
            {
              $value->two_person_cost=$value->two_person_cost;
            }else{
              $value->two_person_cost=0;
            }

            $offer=Coupon::select('id','value','discount_type','minmum_spend')->where('is_main_offer',$status)->where("user_id",$value->id)->first();

            if($offer)
            {
              $value->offer=$offer;
            }
            else
            {
              $value->offer="";
            }
            $category =Categories::select('cat_name')->orderBy('id','DESC')->where("is_delete",'0')->where("res_id",$value->id)->get();
              $arr=array();
              foreach ($category as $val){
                $arr[]=$val['cat_name'];
              }
              if(!empty($arr)){
                   $value->category=$comma_separated=implode(",",$arr);
               }else{
                   $value->category="";
               }

             $rate = Review::where('res_id',$value->id)->avg('ratting')?Review::where('res_id',$value->id)->avg('ratting'):'0.0';  
             $value->ratting=(string)$rate;
              $value->delivery_time=$value->delivery_time.' min';
          }

          $top_offer= User::select('id','first_name','email','phone','address','res_image','delivery_time','review_count','two_person_cost','area','city','country')->where("user_type",3)->whereNotNull('two_person_cost')->orderBy('two_person_cost','DESC')->limit(10)->get();
          $status="1";
         
          foreach ($top_offer as $t_offer) 
          {
            
            if(isset($t_offer->review_count) && !empty($t_offer->review_count))
            {
              $t_offer->review_count=$t_offer->review_count;
            }else{
              $t_offer->review_count=0;
            }

            if(isset($t_offer->two_person_cost) && !empty($t_offer->two_person_cost))
            {
              $t_offer->two_person_cost=$t_offer->two_person_cost;
            }else{
              $t_offer->two_person_cost=0;
            }

            $offer=Coupon::select('id','value','discount_type','minmum_spend')->where('is_main_offer',$status)->where("user_id",$t_offer->id)->first();

            if($offer)
            {
             $t_offer->offer=$offer;
            }
            else
            {
              $t_offer->offer="";
            }
            $category =Categories::select('cat_name')->orderBy('id','DESC')->where("is_delete",'0')->where("res_id",$t_offer->id)->get();
              $arr=array();
              foreach ($category as $val){
                $arr[]=$val['cat_name'];
              }
              if(!empty($arr)){
                   $t_offer->category=$comma_separated=implode(",",$arr);
               }else{
                   $t_offer->category="";
               }
               $rate = Review::where('res_id',$t_offer->id)->avg('ratting')?Review::where('res_id',$t_offer->id)->avg('ratting'):'0.0';
               $t_offer->ratting=(string)$rate;
                $t_offer->delivery_time=$t_offer->delivery_time.' min';
          }

          if(!empty($getrestaurant))
          {                
            $response = array("status" =>1, "msg" => __("Get Restaurant List Successfully"),"populer_restaurants"=>$getrestaurant,"top_economy"=>$top_offer);
          }
          else{
            $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
         }
        return Response::json($response);
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
                if($request->get("type")==1){
                    $data = Order::select('id','order_no','orderplace_datetime','created_at','status')->where("seller_id",$request->get("id"))->whereMonth('created_at',date('m'))->orderby('id','DESC')->paginate(15);
                }else{
                     $data = Order::select('id','order_no','orderplace_datetime','status')->where("seller_id",$request->get("id"))->orderBy('id','DESC')->paginate(15);
                    
                }
                $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                if($data){
                    if($request->get("type")==1){
                    $count_data = Order::select('id','order_no','orderplace_datetime','created_at','status')->where("seller_id",$request->get("id"))->where('status','7')->whereMonth('created_at',date('m'))->orderBy('id','DESC')->get();
                    $sum_data = Order::select('id','order_no','orderplace_datetime','created_at','status')->where("seller_id",$request->get("id"))->whereMonth('created_at',date('m'))->where('status','7')->orderBy('id','DESC')->sum('total');
                    }
                    else{
                    $count_data = Order::select('id','order_no','orderplace_datetime','status')->where("seller_id",$request->get("id"))->where('status','7')->orderBy('id','DESC')->get();
                    $sum_data = Order::select('id','order_no','orderplace_datetime','status')->where("seller_id",$request->get("id"))->where('status','7')->orderBy('id','DESC')->sum('total');
                    }
                    $total_order = count($count_data);
                    $arr = array();
                    foreach($data as $d){
                        $getorder = Order::find($d->id);
                        $d->total_amount=$getorder->total;
                        $d->delivery_charges=$getorder->delivery_charge;
                        $getuser = User::find($getorder->user_id);
                        $getaddress = Addresses::where("id",$getorder->user_address_id)->first();
                        if($getuser->first_name=="")
                        {
                          $d->username = $getuser->first_name;
                        }
                        else
                        {
                          $d->username ="";
                        }
                        $d->address = $getaddress;
                        $d->payment_method = $getorder->payment_method;
                        unset($getuser->per_product_seller_price);
                    }
                    $getlastorder = Order::where("seller_id",$request->get("id"))->where("status",'7')->orderby('id','DESC')->first();
                    
                    $response = array("status" =>1, "msg" => __("messages.Order Histroy"),"data"=>array("order"=>$data,"total_earning"=>(float)$sum_data,"complete_order"=>$total_order,"currency"=>isset($currency[1])?trim($currency[1]):'',"last_order_complete_time"=>isset($getlastorder->complete_datetime)?$getlastorder->complete_datetime:''));  
                }else{
                     $response = array("status" =>0, "msg" => __("messages.No Order Histroy"));    
                }
                
                
           }
           return $response;
  }
  public function show_order_list(Request $request){
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
                     
                    $data = Order::select('id','order_no','total','delivery_charge','status','orderplace_datetime')->where("seller_id",$request->get("id"))->where("status","!=",'7')->where("status","!=",'2')->orderby('id','DESC')->paginate(15);
                   
                                    }
                if($request->get("status")==2){ // complete
                   
                     $data = Order::select('id','order_no','total','delivery_charge','status','orderplace_datetime')->where("seller_id",$request->get("id"))->where("status",'7')->orderby('id','DESC')->paginate(15); 
                }
                if($request->get("status")==3){ // reject
                   $data = Order::select('id','order_no','total','delivery_charge','status','orderplace_datetime')->where("seller_id",$request->get("id"))->where("status",'2')->orderby('id','DESC')->paginate(15); 
                    
                }
                if($request->get("status")==0){ // reject
                    
                      $data = Order::select('id','order_no','total','delivery_charge','status','orderplace_datetime')->where("seller_id",$request->get("id"))->orderby('id','DESC')->paginate(15);

                }
                 
                           
                           
                $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                if(count($data)>0){
                    $total_charges = 0;
                    $total_order = 0;
                    $arr = array();
                    foreach($data as $d){
                        $getorder = Order::find($d->id);
                        $getuser = User::find($getorder->user_id);
                        $getaddress = Addresses::where("id",$getorder->user_address_id)->first();
                        $d->username = isset($getuser->first_name)?$getuser->first_name:'';
                        $d->address = $getaddress;
                        $total_charges = $total_charges + $d->per_product_seller_price  ;
                        $total_order = $total_order+1;
                        $d->shipping_method = $getorder->shipping_method;
                        $d->total_amount = $getorder->total;
                        $d->status=$getorder->status;
                        $d->delivery_charges=$getorder->delivery_charge;
                        $d->payment_method = $getorder->payment_method;
                        unset($getuser->per_product_seller_price);
                    }
                    
                    $today_order = count(Order::where("seller_id",$request->get("id"))->whereDate("created_at",date('Y-m-d'))->get());
                    $complete_amount = Order::where("seller_id",$request->get("id"))->where("status",'7')->sum('total');
                    $previous_week = strtotime("-1 week +1 day");

                    $start_week = strtotime("last sunday midnight",$previous_week);
                    $end_week = strtotime("next saturday",$start_week);
                    
                    $start_week = date("Y-m-d",$start_week);
                    $end_week = date("Y-m-d",$end_week);
                    $last_week_amount = Order::where("seller_id",$request->get("id"))->whereBetween('created_at', [$start_week, $end_week])->sum('total');
                    $day = date('w');
                    $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
                    $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));
 
                    $week_order = count(Order::where("seller_id",$request->get("id"))->whereBetween('created_at', [$week_start, $week_end])->get());
                    $week_amount = Order::where("seller_id",$request->get("id"))->whereBetween('created_at', [$week_start, $week_end])->sum('total');
                    $we_rev = 0;
                    if($week_amount>0){
                        $week_revenue = ($week_amount-$last_week_amount);
                 
                    $we_rev = ($week_revenue/$week_amount);
                    }
                    
                    
                    $yerda = date('Y-m-d',strtotime("-1 days"));
                    $yerterday_am = Order::where("seller_id",$request->get("id"))->whereDate('created_at', $yerda)->sum('total');
                    $today_am = Order::where("seller_id",$request->get("id"))->whereDate('created_at', date('Y-m-d'))->sum('total');
                    $to_rev = 0;
                    if(isset($today_am)&&$today_am>0){
                         $today_revenue = ($today_am-$yerterday_am);
                         $to_rev = ($today_revenue/$today_am);
                    }
                    $response = array("status" =>1, "msg" => __("messages.order List"),"data"=>array("order"=>$data,"today_order"=>$today_order,"total_earning"=>$complete_amount,"week_order"=>$week_order,"today_total_amount"=>$today_am,"week_total_amount"=>$week_amount,"today_revenue_in_percentage"=>$to_rev,"week_revenue"=>(float)number_format($we_rev,2,'.','')),"currency"=>isset($currency[1])?trim($currency[1]):'');  
                }else{
                     $setting = Setting::find(1);
                     $currency = explode("-",$setting->default_currency);
                     $response = array("status" =>0, "msg" => __("messages.No Order List Found"),"currency"=>isset($currency[1])?trim($currency[1]):'');  
                }
                  
                
           }
           return $response;
  }
  public function total_amount_order_list(Request $request){
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'id' => 'required',
                      'type'=>'required'
                    ]; 
                    
                    
            $messages = array(
                      'id.required' => "id is required",
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
            }  else {
                App::setlocale($request->get("lang"));
                session()->put('locale', $request->get("lang"));
                if($request->get("type")==1){
                    $day = date('w');
                    $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
                    $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));
                    $data = Order::select('id','order_no','orderplace_datetime','total','per_product_seller_price','status')->where("seller_id",$request->get("id"))->where('created_at',">=",$week_start.'00:00:00')->where('created_at',"<=",$week_end."23:59:59")->orderby('per_product_seller_price','DESC')->paginate(15); 
                }else if($request->get("type")==2){ // this month
                    $start_date = date('Y-m-01');
                    $end_date  = date('Y-m-d');
                   
                    $data = Order::select('id','order_no','orderplace_datetime','total','per_product_seller_price','status','created_at')->where("seller_id",$request->get("id"))->where('created_at',">=",$start_date.'00:00:00')->where('created_at',"<=",$end_date."23:59:59")->orderby('per_product_seller_price','DESC')->paginate(15); 
                }else if($request->get("type")==4){ // this month complete order
                    $start_date = date('Y-m-01');
                    $end_date  = date('Y-m-d');
                    $data = Order::select('id','order_no','orderplace_datetime','total','per_product_seller_price','status','created_at')->where("seller_id",$request->get("id"))->whereBetween('created_at', [$start_date, $end_date])->where('status',7)->orderby('per_product_seller_price','DESC')->paginate(15); 
                }else if($request->get("type")==3){ // total amount
                    $day = date('w');
                    $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
                    $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));
                    $data = Order::select('id','order_no','orderplace_datetime','total','per_product_seller_price','status')->where("seller_id",$request->get("id"))->orderby('per_product_seller_price','DESC')->paginate(15); 
                }else{
                    $data = Order::select('id','order_no','orderplace_datetime','total','per_product_seller_price','status')->where("seller_id",$request->get("id"))->whereDate('created_at',date('Y-m-d'))->orderby('per_product_seller_price','DESC')->paginate(15); 
                }
                 
                $setting = Setting::find(1);
                $currency = explode("-",$setting->default_currency);
                if($data){
                    $total_charges = 0;
                    $total_order = 0;
                    $arr = array();
                    foreach($data as $d){
                        if($d->per_product_seller_price==NULL)
                        {
                            $d->per_product_seller_price="";
                        }
                        else
                        {
                            $d->per_product_seller_price;
                             $total_charges = $total_charges + $d->per_product_seller_price;
                        }
                        $getorder = Order::find($d->id);
                        $getuser = User::find($getorder->user_id);
                        $getaddress = Addresses::where("id",$getorder->user_address_id)->first();
                        $d->username = $getuser->first_name;
                        $d->address = $getaddress;$total_order = $total_order+1;
                        $d->payment_method = $getorder->payment_method;;
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
                session()->put('locale', $request->get("lang"));$orderdata = Order::find($request->get("id"));
             
                $setting=Setting::find(1);
                if($orderdata){ // sucess
                    $get_order = Order::find($orderdata->id);
                    $msg = "";
                    if($request->get("status")=='1'){ // accept
                        $msg=__('messages.the order has been accepted by the Seller');
                        $orderdata->accept_datetime=$this->getsitedate();
                        $android=$this->send_notification_android($setting->android_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->user_id,$msg,$orderdata->id);
                         
                        $orderdata->status = '1';
                        $orderdata->save();
                    }
                    if($request->get("status")=='2'){
                        $msg=__('messages.the order has been rejected by the Seller');
                        $orderdata->reject_datetime=$this->getsitedate();
                        $android=$this->send_notification_android($setting->android_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $orderdata->status = 2;
                        $orderdata->save();
                    }
                    if($request->get("status")=='3'){ // accept
                        $msg=__('messages.the order has been prepare by the Seller');
                         $orderdata->prepare_datetime=$this->getsitedate();
                        $android=$this->send_notification_android($setting->android_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->user_id,$msg,$orderdata->id);
                         
                        $orderdata->status = '3';
                        $orderdata->save();
                    }
                    
                     if($request->get("status")=='6'&&$get_order->shipping_method='2'){ // accept
                        $msg=__('messages.the order has been prepare by the Seller');
                        $orderdata->out_for_delivery_datetime=$this->getsitedate();
                        $android=$this->send_notification_android($setting->android_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->user_id,$msg,$orderdata->id);
                         
                        $orderdata->status = '3';
                        $orderdata->save();
                    }
                    
                     if($request->get("status")=='7'&&$get_order->shipping_method='2'){ // accept
                        $msg=__('messages.the order has been Deliver by the Seller');
                        $orderdata->complete_datetime=$this->getsitedate();
                        $android=$this->send_notification_android($setting->android_api_key,$orderdata->user_id,$msg,$orderdata->id);
                        $ios=$this->send_notification_IOS($setting->iphone_api_key,$orderdata->user_id,$msg,$orderdata->id);
                         
                        $orderdata->status = '7';
                        $orderdata->save();
                    }
                  
                    $response = array("status" =>1, "msg" => $msg); 
                    
                }else{ 
                    $response = array("status" =>0, "msg" => __("messages.No Data Found"));  
                }
                
           }
           return $response;
  }
  public function get_restaurant_list(Request $request){
         $response = array("status" => "0", "msg" => "Validation error");
          $getrestaurant= User::select('id','first_name','email','phone','address','res_image','access_cat','delivery_time','review_count','two_person_cost','area','city','country')->where("user_type",3)->paginate(10);
          $status="1";
          
          foreach ($getrestaurant as $value) 
          {
            $offer=Coupon::select('id','value','discount_type','minmum_spend')->where('is_main_offer',$status)->where("user_id",$value->id)->first();
            if($offer)
            {
             $value->offer=$offer;
            }
            else
            {
              $value->offer="";
            }

            if(isset($value->review_count) && !empty($value->review_count))
            {
              $value->review_count=$value->review_count;
            }else{
              $value->review_count=0;
            }

            if(isset($value->two_person_cost) && !empty($value->two_person_cost))
            {
              $value->two_person_cost=$value->two_person_cost;
            }else{
              $value->two_person_cost=0;
            }

            $category_str=  $value->access_cat;
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
                  $value->access_cat = $str;

              $rate = Review::where('res_id',$value->id)->avg('ratting')?Review::where('res_id',$value->id)->avg('ratting'):'0.0';
              $value->ratting=(string)$rate;
               $value->delivery_time=$value->delivery_time.' min';
          }

          if(!empty($getrestaurant))
          {                
            
            $response = array("status" =>1, "msg" => __("Get Restaurant List Successfully"),"data"=>$getrestaurant);
          }
          else{
            $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
         }
      return Response::json($response);
  }
  public function getmenucategory(Request $request){
           $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'user_id'=>'required'
                      
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
                  $response['register'] = $message;
            } else {
                        
                        
                         $category=Categories::select('id','cat_name')->where("res_id",$request->get("user_id"))->where("is_delete",'0')->get();
                         
                         if(count($category)>0){
                             
                             $response = array("status" =>1, "msg" => __("messages.Get Category Successfully"),"data"=>$category);
                         }else{
                             $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                         }
                         
            }
            return Response::json(array("data"=>$response));
  }
  public function get_res_product(Request $request){
           $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'user_id'=>'required'
                      
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
                  $response['register'] = $message;
            } else {
                        
                        
                         $product=DB::table('products')->select('products.id','products.name')->join('category', 'products.category', '=', 'category.id')->where('category.is_delete','0')->where("products.user_id",$request->get("user_id"))->get();
                         
                         if(count($product)>0){
                             
                             $response = array("status" =>1, "msg" => __("messages.Get Product Successfully"),"data"=>$product);
                         }else{
                             $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                         }
                         
            }
            return Response::json(array("data"=>$response));
  }
  public function get_restaurants_product(Request $request)
  {
     $response = array("success" => "0", "register" => "Validation error");
       $rules = [
                  'res_id'=>'required',
                  'user_id'=>'required'
                ];
        $messages = array(
                  "res_id.required"=>"res_id is required",
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
            $res_id=$request->get("res_id");
             $user_id=$request->get("user_id");
            $category=Categories::where('res_id',$res_id)->where('is_delete','0')->select('id','res_id','cat_name','image')->get();
           
            foreach ($category as $cat_val)
            {
                 $product=Product::where("category",$cat_val->id)->where("user_id",$res_id)->where("is_deleted",'0')->select('id','name','price','basic_image','weight','description','is_veg')->get();
                 foreach ($product as $pro_key)
                  {
                    $options=ProductOption::where("product_id",$pro_key->id)->get();
                    $data1 = array();
                    $i=0;

                      foreach ($options as $k1) 
                      {
                        $d1 = ProductOption::where("product_id",$pro_key->id)->where("option_id",$k1->option_id)->first();
                        if($d1)
                        {
                            $data1[$i]['optionname'] = $k1->name;
                            $data1[$i]['min_item_selection'] = $k1->min_item_selection;
                            $data1[$i]['max_item_selection'] = $k1->max_item_selection;
                             $la = explode("#",$k1->label);
                             $pr = explode("#",$k1->price);
                             $j = 0;
                             
                             foreach ($pr as $p) {
                                 $a = array();
                                $a['label'] = $la[$j];
                                $a['price'] = $p;
                                $data1[$i]['optionvalues'][] =  $a;
                                $j++;
                             }
                            $i++;
                        }
                    }
                    $pro_key->options = $data1;
                    $rate = Review::where('product_id',$pro_key->id)->avg('ratting')?Review::where('product_id',$pro_key->id)->avg('ratting'):'0.0';
                    $pro_key->ratting=(string)$rate;
                  }
                if(!empty($product))
                {
                  $cat_val->category_product=$product;
                }else{
                  $cat_val->category_product=array();
                }
            }

        $offer=Coupon::select('id','value','code','discount_type','minmum_spend')->where("user_id",$res_id)->get();
        $is_favorite=Wishlist::where("res_id",$res_id)->where("user_id",$user_id)->first();
        if($is_favorite){
            $is_wishlist="1";
        }
        else
        {
            $is_wishlist="0";
        }
        $restaurant_detail=User::where("id",$res_id)->select('id','email','res_image','first_name','two_person_cost','delivery_time','address','lat','long','access_cat','res_time','area','city','country')->first();

           $category_str=  $restaurant_detail->access_cat;
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
                
          $restaurant_detail->category=$str;
            $restaurant_detail->is_wishlist=$is_wishlist;
            
          $rate = Review::where('res_id',$res_id)->avg('ratting')?Review::where('res_id',$res_id)->avg('ratting'):'0.0';
                  
            $restaurant_detail->ratting=(string)$rate;
          $main_arr=array("category_list"=>$category,"offer"=>$offer,"restaurant_detail"=>$restaurant_detail);

        $response = array("status" =>1, "msg" => __("Get Restaurant List Successfully"),"data"=>$main_arr);
        
        }
        return Response::json($response);
  }
  public function get_fillter_restaurant(Request $request){
        $add_rating= User::get();
          foreach ($add_rating as $value) 
          {
             $rate = Review::where('res_id',$value->id)->avg('ratting')?Review::where('res_id',$value->id)->avg('ratting'):'0.0';  
             $add_data=User::where('id',$value->id)->first();
             $add_data->rating=$rate;
             $add_data->save();
          }
          $restaurant=DB::table('users')->select('users.id','users.first_name','users.email','users.phone','users.address','users.area','users.city','users.country','users.res_image','users.delivery_time','users.review_count','users.two_person_cost');
           $sort_by=$request->get("sort_by");
           $cuisins=$request->get("cuisins");
           $is_veg=$request->get("is_veg");
           $rating=$request->get("rating");
           $price=$request->get("price");
           $offer=$request->get("offer");
           if($sort_by != '') {
               if($sort_by ==1)
               {
                $restaurant->orderBy('delivery_time', 'asc');

               }
               if($sort_by ==2)
               {
                $restaurant->orderBy('review_count','DESC');

               }
               if($sort_by ==3)
               {
                $restaurant->orderBy('two_person_cost','asc');

               }
               if($sort_by ==4)
               {
                $restaurant->orderBy('two_person_cost','DESC');

               } 
           }
           if($is_veg != '') {
               if($is_veg ==1)
               {
                 $restaurant->join('products', 'users.id', '=', 'products.user_id')->where('products.is_veg',"=","1")->groupBy('users.id');
                

               }
               if($is_veg ==0)
               {
                 $restaurant->join('products', 'users.id', '=', 'products.user_id')->where('products.is_veg',"=","0")->groupBy('users.id');
               }
              
           }
           if($rating != '') {
               
                  $restaurant->where('rating',">=",$request->get("rating"))->orderBy('rating','DESC');

           }
           if($cuisins != '') {
               
                 $restaurant->Where('access_cat', 'like', '%' . $request->get("cuisins") . '%');
           }
           if($offer != '') {
                if($offer ==1)
                 {
                   $restaurant->join('coupon', 'users.id', '=', 'coupon.user_id')->where('discount_type','1')->orderBy('value','DESC')->groupBy('users.id')->groupBy('users.id');
                  

                 }
                 if($offer ==0)
                 {
                   $restaurant->join('coupon', 'users.id', '=', 'coupon.user_id')->where('discount_type','0')->orderBy('value','DESC')->groupBy('users.id');
                 }
                 
           }
           $data = $restaurant->paginate(10);
            $status="1";
            foreach ($data as $value) 
        {
          $offer=Coupon::select('id','value','discount_type','minmum_spend')->where('is_main_offer',$status)->where("user_id",$value->id)->first();
          if($offer)
          {
           $value->offer=$offer;
          }
          else
          {
            $value->offer="";
          }

          if(isset($value->review_count) && !empty($value->review_count))
          {
            $value->review_count=$value->review_count;
          }else{
            $value->review_count=0;
          }

          if(isset($value->two_person_cost) && !empty($value->two_person_cost))
          {
            $value->two_person_cost=$value->two_person_cost;
          }else{
            $value->two_person_cost=0;
          }

          $category =Categories::select('cat_name')->orderBy('id','DESC')->where("is_delete",'0')->where("res_id",$value->id)->get();
            $arr=array();
            foreach ($category as $val){
              $arr[]=$val['cat_name'];
            }
            if(!empty($arr)){
                 $value->category=$comma_separated=implode(",",$arr);
             }else{
                 $value->category="";
             }

            $rate = Review::where('res_id',$value->id)->avg('ratting')?Review::where('res_id',$value->id)->avg('ratting'):'0.0';
            $value->ratting=(string)$rate;
        }
         if($restaurant){
             
             $response = array("status" =>1, "msg" => __("messages.Get restaurant Successfully"),"data"=>$data);
         }else{
             $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
         }
         
            
            return Response::json(array("data"=>$response));
  }
  public function get_restaurants_by_category(Request $request){
       $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'cat_id'=>'required'
                      
                    ];
            $messages = array(
                      'cat_id.required' => "cat_id is required"
                      
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
                     
        $getrestaurant= User::select('id','first_name','email','phone','address','city','country','area','delivery_time','res_image','delivery_time','review_count','two_person_cost')->Where('access_cat', 'like', '%' . $request->get("cat_id") . '%')->paginate(10);
        $status="1";
        
        foreach ($getrestaurant as $value) 
        {
            $time = $value->delivery_time . " min";
                    $value->delivery_time=$time;
          $offer=Coupon::select('id','value','discount_type','minmum_spend')->where('is_main_offer',$status)->where("user_id",$value->id)->first();
          if($offer)
          {
           $value->offer=$offer;
          }
          else
          {
            $value->offer="";
          }

          if(isset($value->review_count) && !empty($value->review_count))
          {
            $value->review_count=$value->review_count;
          }else{
            $value->review_count=0;
          }

          if(isset($value->two_person_cost) && !empty($value->two_person_cost))
          {
            $value->two_person_cost=$value->two_person_cost;
          }else{
            $value->two_person_cost=0;
          }

          $category =Categories::select('cat_name')->orderBy('id','DESC')->where("is_delete",'0')->where("res_id",$value->id)->get();
            $arr=array();
            foreach ($category as $val){
              $arr[]=$val['cat_name'];
            }
            if(!empty($arr)){
                 $value->category=$comma_separated=implode(",",$arr);
             }else{
                 $value->category="";
             }

            $rate = Review::where('res_id',$value->id)->avg('ratting')?Review::where('res_id',$value->id)->avg('ratting'):'0.0';
            $value->ratting=(string)$rate;
        }

        if(!empty($getrestaurant))
        {                
          
          $response = array("status" =>1, "msg" => __("Get Restaurant List Successfully"),"data"=>$getrestaurant);
        }
        else{
          $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
       }
    return Response::json($response);
    }
   }
  public function serach_restaurants(Request $request)
  {
     $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'name'=>'required'
                      
                    ];
            $messages = array(
                      "name.required"=>"name is required"
                     
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
                $name=$request->get("name");
                $getrestaurant=User::Where('first_name', 'like', '%' . $name . '%')->select('id','first_name','email','access_cat','phone','address','res_image','delivery_time','review_count','two_person_cost','area','city','country')->where("user_type",3)->paginate(10);
                
                $status="1";
                foreach ($getrestaurant as $value) 
                {
                  $offer=Coupon::select('id','value','discount_type','minmum_spend')->where('is_main_offer',$status)->where("user_id",$value->id)->first();
                  if($offer)
                  {
                   $value->offer=$offer;
                  }
                  else
                  {
                    $value->offer="";
                  }
                  if(isset($value->review_count) && !empty($value->review_count))
                  {
                    $value->review_count=$value->review_count;
                  }else{
                    $value->review_count="";
                  }

                  if(isset($value->two_person_cost) && !empty($value->two_person_cost))
                  {
                    $value->two_person_cost=$value->two_person_cost;
                  }else{
                    $value->two_person_cost="";
                  }

                   $category_str=  $value->access_cat;
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
                $value->access_cat = $str;

                    $rate = Review::where('res_id',$value->id)->avg('ratting')?Review::where('res_id',$value->id)->avg('ratting'):'0.0';
                    $value->ratting=(string)$rate;
                    $value->delivery_time=date("i", strtotime($value->delivery_time)).' min';
                }

                if(!empty($getrestaurant))
                {                
                  
                  $response = array("status" =>1, "msg" => __("Get Restaurant List Successfully"),"data"=>$getrestaurant);
                }
                else{
                  $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
               }
            }
            return Response::json($response);
  }
  public function get_restaurants_by_filter(Request $request)
  {
    $response = array("success" => "0", "register" => "Validation error");
           $rules = [
                      'cat_id'=>'required',
                      'serach'=>'required'
                    ];
            $messages = array(
                "cat_id.required"=>"cat_id is required",
                "serach.required"=>"serach is required"
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
                $cat_id=$request->get("cat_id");
                $serach=$request->get("serach");
                
                $getrestaurant = \DB::table("users")
                  ->select('id','first_name','email','phone','address','city','country','area','res_image','delivery_time','review_count','two_person_cost')
                  ->where('first_name', 'like', '%' . $serach . '%')
                  ->whereRaw("find_in_set('".$cat_id."',users.access_cat)")
                  ->where("user_type",3)
                  ->whereNotNull('access_cat')
                  ->paginate(10);
                
                $status="1";
                foreach ($getrestaurant as $value) 
                {   $time = $value->delivery_time . " min";
                    $value->delivery_time=$time;
                  $offer=Coupon::select('id','value','discount_type','minmum_spend')->where('is_main_offer',$status)->where("user_id",$value->id)->first();
                   
                  if($offer)
                  {
                   $value->offer=$offer;
                  }
                  else
                  {
                    $value->offer="";
                  }
                  if(isset($value->review_count) && !empty($value->review_count))
                  {
                    $value->review_count=$value->review_count;
                  }else{
                    $value->review_count="";
                  }

                  if(isset($value->two_person_cost) && !empty($value->two_person_cost))
                  {
                    $value->two_person_cost=$value->two_person_cost;
                  }else{
                    $value->two_person_cost="";
                  }

                  $category =Categories::select('cat_name')->orderBy('id','DESC')->where("is_delete",'0')->where("res_id",$value->id)->get();
                    $arr=array();
                    foreach ($category as $val){
                      $arr[]=$val['cat_name'];
                    }
                    if(!empty($arr)){
                         $value->category=$comma_separated=implode(",",$arr);
                     }else{
                         $value->category="";
                     }

                    $rate = Review::where('res_id',$value->id)->avg('ratting')?Review::where('res_id',$value->id)->avg('ratting'):'0.0';
                    $value->ratting=(string)$rate;
                }

                if(!empty($getrestaurant))
                {                
                  
                  $response = array("status" =>1, "msg" => __("Get Restaurant List Successfully"),"data"=>$getrestaurant);
                }
                else{
                  $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
               }
            }
            return Response::json($response);
  }
  public function view_profile(Request $request){
      //  dd($request->all());
        $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      "id"=>"required",
                     
                    ]; 
                    
                    
            $messages = array(
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
            }  else {
               
                $data =User::select('id','first_name','email','phone','access_cat','address','res_image','delivery_time','review_count','two_person_cost','lat','long','res_time','area','city','city','country','pincode')->where('id',$request->get("id"))->where("user_type",3)->first();
                 if($data){
                        $category_str=  $data->access_cat;
                          $cat = explode(",",$category_str);
                         
                          $cat_name = array();
                          foreach($cat as $val){
                              $cat =Categories::select('cat_name')->where("id",$val)->WHERE('parent_category',0)->first();
                              if($cat)
                              {
                                
                              $cat_name[] =$cat->cat_name;    
                              }
                          }
                          $data->access_cat = implode(",",$cat_name);

                    
                            $response = array("status" =>1, "msg" =>"Get Detail Successfully","data"=>$data); 
                        
                 }
                 else{
                  $response = array("status" =>0, "msg" => __("messages.Something wrong"));
                 }                
           }
           return $response;
  }
  public function delete_owner(Request $request){
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
                      $user=User::find($id);
                      if($user){
                          $user->is_active = '0';
                          $user->save();
                          $get_product =  Product::where('user_id',$id)->get();
                          foreach($get_product as $gp){
                              $gp->is_deleted = '1';
                              $gp->save();
                          }
                          
                      }
                      
                     
                      $response = array("status" =>1, "msg" => __('messages_error_success.delivery_del'));
           }
           return Response::json(array("data"=>$response));
  }
  public function get_category(Request $request){
      $category=Categories::select('id','cat_name')->where("parent_category",'0')->where("is_delete",'0')->get();

      if(count($category)>0){
        $response = array("status" =>1, "msg" =>"Get Category Successfully","data"=>$category);
       }else{
          $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
        }
        return Response::json($response);
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
                    'order_id'=>$id,
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    "sound"=> "default", 
                    "status"=> "done",
                    "screen"=> "screenA"
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
                   'order_id'=>$id,
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    "status"=> "done",
                    "screen"=> "screenA"
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
  
}
?>

