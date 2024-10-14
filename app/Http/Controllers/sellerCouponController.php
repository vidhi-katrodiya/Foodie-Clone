<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Lang_core;
use App\Models\FileMeta;
use Auth;
Use Image;
use Hash;
use Cart;
class sellerCouponController extends Controller {
      public function __construct() {
         parent::callschedule();
    }
      public function index(){
            $lang = Lang_core::all();
           return view("seller.coupon.default")->with("lang",$lang);
      }

      public function checkcoupon(Request $request){
          return $this->verifiedcoupon($request->get("coupon"));
      }

      public function coupondatatable(){
            $coupon =Coupon::orderBy('id','DESC')->where("user_id",Auth::id())->where("is_deleted",'0')->get();
            return DataTables::of($coupon)
                ->editColumn('id', function ($coupon) {
                   return $coupon->id;
                })
                ->editColumn('name', function ($coupon) {
                    /*$getmeta = FileMeta::where("model_id",$coupon->id)->where("model_name","Coupon")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
                    return isset($getmeta->meta_value)?$getmeta->meta_value:'';*/
                     return $coupon->name;
                })           
                ->editColumn('code', function ($coupon) {
                    return $coupon->code;            
                })
                 ->editColumn('date', function ($coupon) {
                  return $coupon->created_at;        
                   /* if($coupon->start_date!=""){
                        return $coupon->start_date."-".$coupon->end_date;
                    }
                    else{
                        return "";
                    }*/
                   
                })
                ->editColumn('value', function ($coupon) {
                    if($coupon->discount_type=='0'){
                        return $coupon->value;
                    }
                    if($coupon->discount_type=='1'){
                        return $coupon->value."%";
                    }
                    return '';
                })           
                ->editColumn('action', function ($coupon) {
                     $edit=url('seller/editcoupon',array('id'=>$coupon->id));
                     $delete=url('seller/deletecoupon',array('id'=>$coupon->id));
                     if($coupon->status=="0"){
                      $ck="";
                     }else{
                      $ck="checked";
                     }
                     $swt='<label class="switch m5">
                                <input type="checkbox" '.$ck.' onchange="offer_status('.$coupon->id.','.$coupon->status.');">
                                <small></small>
                              </label> ';
                         
                     $return = '<a href="'.$edit.'" rel="tooltip" title="active" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-edit f-s-25" style="font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $delete. "'" . ')" rel="tooltip" title="Delete Category" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';     
                     return $swt.$return;      
                })           
            ->make(true);
      }
   
      public function addcoupon(){
        // die("FFFF");
        $lang = Lang_core::all();
         return view("seller.coupon.addcoupon")->with("lang",$lang);
      }
       public function offer_status(Request $request)
       {
          $data=Coupon::where("id",$request->get("id"))->first(); 
          if($request->get("status")=="1")
          {
            $data->status='0';
            echo "coupon is disable";
          }
          if($request->get("status")=="0")
          {
            $data->status='1';
            echo "coupon is enable";
          } 
          $data->save();
       }

      public function savecoupon(Request $request){        
          if($request->get("coupon_id")==0||$request->get("coupon_id")=="")
          {
                $checkcoupon=Coupon::where("code",$request->get("code"))->first();
                
                $data=new Coupon();
          }
          else{
                
              $data=Coupon::find($request->get("coupon_id"));
          }
          if($request->get("is_main_offer")==1)
          {
            $id= Auth::id();
            $main_offer=Coupon::where("user_id",$id)->where("is_main_offer",$request->get("is_main_offer"))->first();
              $arr=array("id"=>$main_offer->id,"msg"=>"Your main offer already exist","status"=>0);
              echo json_encode($arr);
             
          }
          else
          {

            
            $data->code=$request->get("code");
            $data->user_id = Auth::id();
            $data->discount_type=$request->get("discount_type");
            $data->name=$request->get("coupon_name");
            $data->value=$request->get("value");
            // $data->start_date=$request->get("start_date");
            // $data->end_date=$request->get("end_date");
            // $data->free_shipping=$request->get("free_shipping")?$request->get("free_shipping"):0;
            // $data->status=$request->get("status");
            $data->is_main_offer=$request->get("is_main_offer");
            $data->save();
            $arr=array("id"=>$data->id,"msg"=>"Genaral deatil add successfully","status"=>1);
            echo json_encode($arr);
            die();
            
          }
          
      }

      public function savecouponsecondstep(Request $request){
            $data=Coupon::find($request->get("id"));
            $data->minmum_spend=$request->get("minmum_send");
            $data->maximum_spend=$request->get("maximum_spend");
            
            if($request->get("coupon_on")==0)
            {
              $data->product=$request->get("product");
            }
            else
            {
              $data->product=NULL;
            }
            if($request->get("coupon_on")==1)
            {
              $data->categories=$request->get("category");
            }
            else
            {
              $data->categories=NULL;
            }
           
            $data->coupon_on=$request->get("coupon_on");
            $data->save();
            return $data->id;
      }

      public function savecouponstepthree(Request $request){
            $data=Coupon::find($request->get("id"));
            $data->usage_limit_per_coupon=$request->get("per_coupon");
            $data->usage_limit_per_customer=$request->get("per_customer");
            $data->save();
            return $data->id;
      }

      public function editcoupon($id){
        $data=Coupon::find($id);
        $lang = Lang_core::all();
        $language =Lang_core::all();  
          foreach ($language as $k) {         
            $getmeta = FileMeta::where("model_id",$id)->where("model_name","Coupon")->where("meta_key","name")->where("lang",$k->code)->first();
                $name = "name_".$k->code;
                $data->$name = isset($getmeta->meta_value)?$getmeta->meta_value:'';
          }

       //   echo "<pre>";print_r($data);exit;
        return view("seller.coupon.addcoupon")->with("data",$data)->with("lang",$lang);
      }
      public function verifiedcoupon($coupon_code){
         $date=date("Y-m-d");
         $data=Coupon::where("code",$coupon_code)->where("status",'1')->first();
         if(!$data){
               return 0;
         }
         else{
                $start_date=date("Y-m-d",strtotime($data->start_date)); 
                $end_date=date("Y-m-d",strtotime($data->end_date));
                if(($date>=$start_date)&&($date<=$end_date)){
                        $order=Order::where("coupon_code",$coupon_code)->get();
                        $orderuser=Order::where("coupon_code",$coupon_code)->where("user_id",Auth::id())->get();
                        if($data->usage_limit_per_coupon!=""&&($data->usage_limit_per_coupon<count($order))){
                             return 0;
                        }
                        elseif($data->usage_limit_per_customer!=""&&($data->usage_limit_per_customer<=count($orderuser))){
                             return 0;
                        }
                        elseif($data->minmum_spend!=""&&$data->minmum_spend>=Cart::getTotal()){
                             return 0;
                        }
                        elseif($data->maximum_spend!=""&&$data->maximum_spend<=Cart::getTotal()){
                             return 0;
                        }
                        else{
                          
                              $temp=0;
                              $cartCollection = Cart::getContent();
                              foreach ($cartCollection as $item) {
                                $arr[]=$item->id;
                              }
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
                                   $discount=(Cart::getTotal()*$data->value)/100;
                              }
                              else{
                                 $discount=$data->value;
                              }
                                 return $data;
                              }
                              else{
                               return 0;
                              }
                             
                           }
                        }
                        else{
                           return 0;
                        }
                      }
          
   }

   public function deletecoupon($id){
      $get=Coupon::where("id",$id)->where("user_id",Auth::id())->first();
      if($get){
              $get->is_deleted='1';
              $get->save();
              Session::flash('message',__('messages_error_success.Coupon_Delete')); 
              Session::flash('alert-class', 'alert-success');
              return redirect()->back();
      }
      return redirect()->back();
   }

}