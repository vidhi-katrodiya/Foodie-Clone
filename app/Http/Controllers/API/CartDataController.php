<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Sentinel;
use Validator;
use App\Models\User;
use App\Models\CartData;
use App\Models\Taxes;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\Setting;
use App\Models\Lang_core;
use App\Models\Categories;
use App\Models\FileMeta;
use DateTimeZone;
use DateTime;
use Image;
use Mail;
use DB;
use App;
use Session;
class CartDataController extends Controller {
    public function __construct() {
         parent::callschedule();
    }
   
    public function addcart(Request $request){
          $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'user_id' => 'required',
                      'product_id' => 'required',
                      'qty'=>'required',
                      'product_price'=>'required',
                      'lang'=>'required'             
                    ];                    
            $messages = array(
                      'user_id.required' => "user_id is required",
                      'product_id.required' => "product_id is required",
                      'qty.required'=>"qty is required",
                      'product_price.required'=>"product_price is required",
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
                     $data=array();
                     $qty=$request->get("qty");
                     $product_id=$request->get("product_id");
                     $user_id=$request->get("user_id");
                    if($qty == 0 ){
                        
                        $data=CartData::where('product_id' ,$product_id)->get();
                        
                        foreach($data as $value){
                            $id=$value->delete();
                            }
                         $gettotal=CartData::where("user_id",$request->get("user_id"))->get();
                         $array=array("total"=>count($gettotal));
                         $response = array("status" =>1, "msg" => __("delete item"),"data"=>$array);

                    }else{
                        
                        if(CartData::where('user_id',$user_id)->where('product_id',$product_id)->exists()){
                            
                            if(CartData::where('user_id',$user_id)->where('product_id',$product_id)->where('option',$request->get("option"))->where('label',$request->get("label"))->where('option_price',$request->get("option_price"))->exists())
                            {
                                $data = CartData::where('user_id',$user_id)->where('product_id',$product_id)->first();
                                $qty_price=$request->get("qty")*$request->get("product_price");
                                $producttax=Product::find($request->get("product_id"));
                                $taxdata=Taxes::find($producttax->tax_class);
                                $b=0;
                                if($taxdata){
                                    $getlang = FileMeta::where("model_id",$taxdata->id)->where("lang",Session::get('locale'))->where("model_name","Taxes")->where("meta_key","tax_name")->first();
                                    $tax_name = isset($getlang)?$getlang->meta_value:'';
                                    $a=$taxdata->rate/100;
                                    $b=$subtotal*$a;
                                }
                                $data->qty = $request->get("qty");
                                $data->qty_price = $qty_price;
                                $data->tax_name=isset($tax_name)?$tax_name:'';
                                $data->tax=number_format((float)$b, 2, '.', '');
                                $data->save();
                            }else{
                                $qty_price=$request->get("qty")*$request->get("product_price");
                                $subtotal=$request->get("qty")*$request->get("product_price");
                                $producttax=Product::find($request->get("product_id"));
                                $taxdata=Taxes::find($producttax->tax_class);
                                $b=0;
                                if($taxdata){
                                        $getlang = FileMeta::where("model_id",$taxdata->id)->where("lang",Session::get('locale'))->where("model_name","Taxes")->where("meta_key","tax_name")->first();
                                        $tax_name = isset($getlang)?$getlang->meta_value:'';
                                        $a=$taxdata->rate/100;
                                        $b=$subtotal*$a;
                                }
                                $data=new CartData();
                                $data->user_id=$request->get("user_id");
                                $data->product_id =$request->get("product_id");
                                $data->option=$request->get("option");
                                $data->label=$request->get("label");
                                $data->option_price=$request->get("option_price");
                                $data->qty=$request->get("qty");
                                $data->qty_price = $qty_price;
                                $data->price_product=$request->get("product_price");
                                $data->tax_name=isset($tax_name)?$tax_name:'';
                                $data->tax=number_format((float)$b, 2, '.', '');
                                $data->save();
                            }
                        }else{
                            $qty_price=$request->get("qty")*$request->get("product_price");
                            $subtotal=$request->get("qty")*$request->get("product_price");
                            $producttax=Product::find($request->get("product_id"));
                            $taxdata=Taxes::find($producttax->tax_class);
                            $b=0;
                            if($taxdata){
                                    $getlang = FileMeta::where("model_id",$taxdata->id)->where("lang",Session::get('locale'))->where("model_name","Taxes")->where("meta_key","tax_name")->first();
                                    $tax_name = isset($getlang)?$getlang->meta_value:'';
                                    $a=$taxdata->rate/100;
                                    $b=$subtotal*$a;
                            }
                            $data=new CartData();
                            $data->user_id=$request->get("user_id");
                            $data->product_id =$request->get("product_id");
                            $data->option=$request->get("option");
                            $data->label=$request->get("label");
                            $data->option_price=$request->get("option_price");
                            $data->qty=$request->get("qty");
                            $data->qty_price = $qty_price;
                            $data->price_product=$request->get("product_price");
                            $data->tax_name=isset($tax_name)?$tax_name:'';
                            $data->tax=number_format((float)$b, 2, '.', '');
                            $data->save();
                        }
                        
                        $id=$data->id;
                        $gettotal=CartData::where("user_id",$request->get("user_id"))->get();
                        $array=array("total"=>count($gettotal),"id"=>$id);
                        $response = array("status" =>1, "msg" => __("messages.Cart Add Successfully"),"data"=>$array);
                    }
           }
           return Response::json(array("data"=>$response));
    }

    public function getcart(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required',
                      'id'=>'required'                
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required",
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
                      $setting=Setting::find(1);
                      $delivery_charges=$setting->delivery_charges;
                      $id = $request->get("id");
                      App::setlocale($request->get("lang"));
                      session()->put('locale', $request->get("lang"));
                      $getcartdata=CartData::with('productdata')->where("user_id",$id)->orderBy('id','DESC')->get();
                      if(count($getcartdata)!=0){
                         $main_array=array();
                         foreach ($getcartdata as $k) {
                             $id=$k->productdata->user_id;
                             
                             $seller=User::find($id);
                             $seller_id=$seller->id;
                             $tax=$seller->tax;
                             $data=array();
                             $data['image']=asset('public/upload/product').'/'.$k->productdata->basic_image;
                              
                             $data['name'] = $k->productdata->name; 
                            
                      $categorypro=Categories::find($k->productdata->category);
                     
                        /*if($categorypro){
                            $delivery_charges=$categorypro->delivery_charges;
                        }else{
                            $delivery_charges=0;
                        }*/
                        
                            if($k->option !=" " && $k->option !="null" && $k->option !=NULL)
                            {
                               
                                $na=(explode("-",$k->option));
                                
                            }
                            else{
                                $na=array();
                            }
                            if($k->label !="" && $k->label !="null" && $k->option !=NULL)
                            {
                                $la=(explode("-",$k->label));
                            }
                            else{
                                $la=array();
                            }
                            if($k->option_price !="" && $k->option_price !="null" && $k->option !=NULL)
                            {
                               $pr=(explode("-",$k->option_price));
                            }
                            else{
                                $pr=array();
                            }
                                        
                           
                           
                           
                           $data1=array();
                           
                           if(!empty($na) && !empty($la) && !empty($pr))
                           {$j = 0;
                               foreach ($na as $p) {
                              $a = array();
                              $a['name'] = $p;
                              $a['label'] = $la[$j];
                              $a['price'] = $pr[$j];
                              $data1[]=  $a;
                             
                              
                              $j++;
                           } 
                           }
                           else
                           {
                               $data1=array();
                           }
                           
                              
                        
                        /*if(empty($k->label)){
                            $k->label = "";
                        }*/
                        
                             $data['name']=$k->productdata->name;
                             $data['qty']=$k->qty;
                             $data['price']=$k->price_product;
                             $data['option_detail']=$data1;
                             
                             $data['product_id']=$k->product_id;
                             
                             
                             $data['cart_id']=$k->id;
                             
                             $main_array[]=$data;
                         }
                          
                        $Shipping=Shipping::all();
                        $ls=array("cartdata"=>$main_array,"shipping"=>$Shipping,"seller_id"=>$seller_id,"delivery_charges"=>$delivery_charges,"tax"=>$tax);
                        $response = array("status" =>1, "msg" => __("messages.Cart Get Successfully"),"data"=>$ls);
                     }
                     else{
                        $response = array("status" =>0, "msg" => __("messages.Cart Empty"));
                     }
           }
           return Response::json(array("data"=>$response));
    }

    public function removecart(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required',
                      'id'=>'required'                
                    ];                    
            $messages = array(
                      'lang.required' => "lang is required",
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
                      $cart_id = $request->get("id");
                      App::setlocale($request->get("lang"));
                      session()->put('locale', $request->get("lang"));
                      $getcartdata=CartData::find($cart_id);
                      if($getcartdata){
                         $user_id=$getcartdata->user_id;
                        
                        $getcartdata->delete();
                        $total=CartData::where("user_id",$user_id)->get();
                        $response = array("status" =>1, "msg" => __("messages.Cart Remove Successfully"),"data"=>count($total));
                      }
                      else{
                        $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                      }
            }
           return Response::json(array("data"=>$response));
    }
    
    public function removeallitem(Request $request){
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'user_id'=>'required'                
                    ];                    
            $messages = array(
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
                      
                     $data=CartData::where('user_id' ,$user_id)->get();
                     
                     if($data){
                          foreach($data as $value){
                            $id=$value->delete();
                            }
                           if(empty($id)){
                              $response = array("status" =>0, "msg" => __("messages.Data Not Found"));
                               
                           }else{
                              $response = array("status" =>1, "msg" => __("messages.Data Remove Successfully"));
                           }
                         
                     }
            }
           return Response::json(array("data"=>$response));
    }
}
?>

