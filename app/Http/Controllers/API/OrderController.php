<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Sentinel;
use Validator;
use App\Models\User;
use App\Models\Lang_core;
use App\Models\Categories;
use App\Models\OrderData;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\FileMeta;
use App\Models\Product;
use App\Models\OrderResponse;
use App\Models\Addresses;
use App\Models\Setting;
use DateTimeZone;
use App;
use DateTime;
use Image;
use Mail;
use DB;
class OrderController extends Controller {
    public function __construct() {
         parent::callschedule();
    }
    
   public function vieworder(Request $request){
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
                        $id = $request->get("id");
                        App::setlocale($request->get("lang"));
                        session()->put('locale', $request->get("lang")); 
                        $order=Order::with('orderdatals')->where("id",$id)->first();
                        if($order)
                        {
                          /*echo "<pre>";
                          print_r($order);
                          die();*/
                          //$order=OrderData::with('productdata','Orderdetail')->where("id",$id)->first();
                          
                          // echo "<pre>";print_r($order);exit;  ($order){
                             $orderdata=OrderData::with('productdata','Orderdetail')->where("order_id",$order->id)->get();

                                 $order_details=array();
                                 $main_prod=array();
                                 foreach ($orderdata as  $oredr_val) {
                                    $arr=array();
                                       $arr['basic_image']=asset('public/upload/product/').'/'.$oredr_val->productdata->basic_image;
                                       $arr["name"]=$oredr_val->productdata->name;
                                       $cat_data=Categories::where('id',$oredr_val->productdata->category)->first();
                                       $arr['category'] = isset($cat_data)?$cat_data->cat_name:'';
                                       /*$getlang = FileMeta::where("model_id",$oredr_val->productdata->category)->where("lang",$request->get("lang"))->where("model_name","Categories")->where("meta_key","name")->first();
                                       $arr['category'] = isset($getlang)?$getlang->meta_value:'';*/
                                     
                                       $arr["qty"]=$oredr_val->quantity;
                                       $arr["price"]=$oredr_val->productdata->price;
                                       $option_name_arr=$oredr_val->option_name;
                                       $option_label_arr=$oredr_val->label;
                                       $option_price_arr=$oredr_val->option_price;
                                      
                                        $na=(explode("-",$option_name_arr));
                                        
                                       $la=(explode("-",$option_label_arr));
                                       $pr=(explode("-",$option_price_arr));
                                       $data1=array();
                                       $j = 0;
                             
                                       foreach ($na as $p) {
                                           $a = array();
                                          $a['name'] = $na[$j];
                                          $a['label'] = $la[$j];
                                          $a['price'] = $pr[$j];
                                          $data1[]=  $a;
                                         
                                          
                                          $j++;
                                       }
                                       
                                       $arr['option_detail'] =$data1;
                                       
                                       $arr["amount"]=$oredr_val->total_amount;
                                      /* $getlang = FileMeta::where("model_id",$oredr_val->productdata->id)->where("lang",$request->get("lang"))->where("model_name","Product")->where("meta_key","name")->first();
                                       $arr['name'] = isset($getlang)?$getlang->meta_value:'';*/
                                       $main_prod[]=$arr;

                                 }
                             

                                       $useraddress = Addresses::where("id",$order->user_address_id)->first();
                                       
                                       $sellerinfor = User::select('first_name','phone','address','lat','long')->find($order->seller_id);
                                       
                                    $order_details=array(
                                      "products"=>$main_prod,
                                      "subtotal"=>$order->sub_total,
                                      "shipping_charge"=>$order->delivery_charge,
                                      "shipping_method"=>$order->shipping_method,
                                      "freeshipping"=>$order->is_freeshipping,
                                      "taxes_charge"=>$order->tax,
                                      "payment_method"=>$order->payment_method,
                                      "total"=>$order->total,
                                      "to_ship"=>$order->to_ship,
                                      "shipping_name"=>$order->shipping_first_name,
                                      "shipping_address"=>$order->shipping_address,
                                      "shipping_city"=>$order->shipping_city,
                                      "shipping_pincode"=>$order->shipping_pincode,
                                      "billing_name"=>$order->billing_first_name,
                                      "billing_address"=>$order->billing_address,
                                      "billing_city"=>$order->billing_city,
                                      "billing_pincode"=>$order->billing_pincode,
                                      "coupon_price"=>$order->coupon_price,
                                      "coupon_code"=>$order->coupon_code,
                                      "phone"=>$order->phone,
                                      "total_item"=>1,
                                      "assigned" => $order->assigned
                                    );
                                   $order_status_arr=array();
                                   if($order->orderplace_datetime!=""){
                                        $order_placed=$order->orderplace_datetime;
                                   }
                                   else{
                                      $order_placed="";
                                   }
                                   if($order->accept_datetime!=""){
                                        $pending=$order->accept_datetime;
                                   }
                                   else{
                                      $pending="";
                                   }
                                   if($order->onhold_datetime!=""){
                                       $onhold=$order->onhold_datetime;
                                   }
                                   else{
                                      $onhold="";
                                   }
                                   if($order->prepare_datetime!=""){
                                       $processing=$order->prepare_datetime;
                                   }
                                   else{
                                      $processing="";
                                   }
                                   if($order->completed_datetime!=""){
                                       $complete=$order->completed_datetime;
                                   }
                                   else{
                                      $complete="";
                                   }
                                   if($order->cancel_datetime!=""){
                                       $cancel=$order->cancel_datetime;
                                   }
                                   else{
                                      $cancel="";
                                   }
                                   if($order->refund_datetime!=""){
                                       $refund=$order->refund_datetime;
                                   }
                                   else{
                                      $refund="";
                                   }
                                   
                                   $deliveryarr = array();
                                   $deliveryboy = Delivery::find($order->assign_id);
                                   if($deliveryboy){
                                       $deliveryarr['name'] = $deliveryboy->name;
                                        $deliveryarr['phone'] = $deliveryboy->mobile_no;
                                       
                                   }else{
                                       $deliveryarr['name'] = "";
                                       $deliveryarr['phone'] = "";
                                   }
                                   if(is_null($order->orderplace_datetime)){
                                       $order_placed = "";
                                   }else{
                                       $order_placed = date("Y-m-d H:i:s", strtotime($order->orderplace_datetime));
                                   }
                                   if(is_null($order->accept_datetime)){
                                       $pending = "";
                                   }else{
                                       $pending = date("Y-m-d H:i:s", strtotime($order->orderplace_datetime));
                                   }
                                   if(is_null($order->accept_datetime)){
                                       $pending = "";
                                   }else{
                                       $pending = date("Y-m-d H:i:s", strtotime($order->orderplace_datetime));
                                   }
                                   if(is_null($order->prepare_datetime)){
                                       $processing = "";
                                   }else{
                                       $processing = date("Y-m-d H:i:s", strtotime($order->prepare_datetime));
                                   }
                                   if(is_null($order->onhold_datetime)){
                                       $onhold = "";
                                   }else{
                                       $onhold = date("Y-m-d H:i:s", strtotime($order->onhold_datetime));
                                   }
                                   if(is_null($order->complete_datetime)){
                                       $completed_datetime = "";
                                   }else{
                                       $completed_datetime = date("Y-m-d H:i:s", strtotime($order->complete_datetime));
                                   }
                                   if(is_null($order->cancel_datetime)){
                                       $cancel_datetime = "";
                                   }else{
                                       $cancel_datetime = date("Y-m-d H:i:s", strtotime($order->cancel_datetime));
                                   }
                                   if(is_null($order->refund_datetime)){
                                       $refund = "";
                                   }else{
                                       $refund = date("Y-m-d H:i:s", strtotime($order->refund_datetime));
                                   }
                                   if(is_null($order->reject_datetime)){
                                       $reject_datetime = "";
                                   }else{
                                       $reject_datetime = date("Y-m-d H:i:s", strtotime($order->reject_datetime));
                                   }
                                   $order_status_arr=array(
                                      "order_placed"=>$order_placed,
                                      "pending"=>$pending,
                                      "processing"=>$processing,
                                      "onhold"=>$onhold,
                                      "completed_datetime"=>$completed_datetime,
                                      "cancel_datetime"=>$cancel_datetime,
                                      "refund"=>$refund,
                                      "reject_datetime"=>$reject_datetime
                                   );
                                    $setting = Setting::find(1);
                                    $currency = explode("-",$setting->default_currency);
                                 $data=array(
                                     "order_date"=>$order->orderplace_datetime,
                                     "order_id"=>$order->order_no,
                                     "order_details"=>$order_details,
                                     "order_status_details"=>$order_status_arr,
                                     "order_status"=>$order->status,
                                     "user_address_detail"=>$useraddress,
                                     "seller_address_detail"=>$sellerinfor,
                                     "deliveryboy_detail"=>$deliveryarr,
                                     "currency"=>isset($currency[1])?trim($currency[1]):''
                                 );
                                 $response = array("status" => 1, "msg" => __("messages.Order Details"),"data"=>$data); 
                        }
                        else{
                                $response = array("status" => 0, "msg" => __("messages.Data Not Found")); 
                        }
            }       
           return Response::json(array("data"=>$response));
    }


  public function order_history(Request $request){
     // dd($request->all());
           $response = array("status" => "0", "msg" => "Validation error");
           $rules = [
                      'lang' => 'required',
                      'id'=>'required',
                      
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
                        $user_id = $request->get("id");
                        App::setlocale($request->get("lang"));
                        session()->put('locale', $request->get("lang"));
                        $data=array();
                        
                        if($request->get("type")==1){
                           /* $today=DB::table('order_data')
                           ->select('order_data.*','order_data.orderplace_datetime')
                           ->join('order_record', 'order_record.id', '=', 'order_data.order_id')
                           ->where('order_record.user_id',$user_id)
                           ->orderby("order_data.id","DESC")
                           ->where("order_data.status",'7')
                           ->paginate(10);*/
                          $today=Order::select('id','order_no','status','orderplace_datetime','payment_method','total','seller_id')->where('user_id',$user_id)->where("status",'7')->orderby("id","DESC")->paginate(10);
                        }
                        else if($request->get("type")==2){
                           
                          /*$today=DB::table('order_data')
                           ->select('order_data.*','order_data.orderplace_datetime')
                           ->join('order_record', 'order_record.id', '=', 'order_data.order_id')
                           ->where('order_record.user_id',$user_id)
                           ->orderby("order_data.id","DESC")
                           ->whereIn("order_data.status",array(2,8))
                           ->paginate(10);*/
                           $today=Order::select('id','order_no','status','orderplace_datetime','payment_method','total','seller_id')->where('user_id',$user_id)->wherein('status',array(2,8))->orderby("id","DESC")->paginate(10);
                          
                        }
                        else if($request->get("type")==3){
                             /*$today=DB::table('order_data')
                           ->select('order_data.*','order_data.orderplace_datetime')
                           ->join('order_record', 'order_record.id', '=', 'order_data.order_id')
                           ->where('order_record.user_id',$user_id)
                           ->orderby("order_data.id","DESC")
                           ->wherein("order_data.status",array(1,3,4,5,6))
                           ->paginate(10);*/
                           $today=Order::select('id','order_no','status','orderplace_datetime','payment_method','total','seller_id')->where('user_id',$user_id)->wherein('status',array(1,3,4,5,6))->orderby("id","DESC")->paginate(10);
                        }else{
                           /* $today=DB::table('order_data')
                           ->select('order_data.*','order_data.orderplace_datetime')
                           ->join('order_record', 'order_record.id', '=', 'order_data.order_id')
                           ->where('order_record.user_id',$user_id)
                           ->orderby("order_data.id","DESC")
                           ->paginate(10);*/
                           $today=Order::select('id','order_no','status','orderplace_datetime','payment_method','total','seller_id')->where('user_id',$user_id)->orderby("id","DESC")->paginate(10);

                        }
                        
                        $pro_array=array();
                        foreach ($today as $k) {
                           
                          $res_data=user::where('id',$k->seller_id)->first();
                          if($res_data)
                          {
                             $k->res_image=$res_data->res_image;
                             $k->res_name=$res_data->first_name;
                             $address_data=Addresses::where('user_id',$res_data->id)->first();
                             if($address_data)
                             {
                                $k->res_address=$address_data->address;
                             }
                             else 
                             {
                                $k->res_address="";
                             }
                          }
                          else
                          {
                            $k->res_image="";
                            $k->res_name="";
                            $k->res_address="";
                          }
                          

                        }
                        if(empty($today)){
                          $response = array("status" =>0, "msg" => __("messages.No Order History"));
                        }
                        else{
                         
                          $response = array("status" => 1, "msg" => __("messages.Order History"),"orders"=>$today); 
                        }
            }
        
        
        return Response::json(array("data"=>$response));
    }

}
?>

