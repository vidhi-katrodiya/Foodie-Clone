<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;
use App\Models\Setting;
use App\Models\Shipping;
use App\Models\OrderResponse;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Taxes;
use App\Models\OrderData;
use App\Models\PaymentMethod;
use Cart;
use DateTimeZone;
use DateTime;
use Auth;
use Freaktemplate\payment\Http\Controllers\PaypalController;
use Request;

class Order extends Model
{
    use HasFactory;
    protected $table = 'order_record';
    protected $primaryKey = 'id';
    public function userdata(){
       return $this->hasone('App\Models\User', 'id', 'user_id');
    }
    public function orderdatals(){
       return $this->hasone('App\Models\OrderData', 'order_id', 'id');
    }
   
    public static function generate_timezone_list(){
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

    public static function gettimezonename($timezone_id){
          $getall=self::generate_timezone_list();
          foreach ($getall as $k=>$val) {
             if($k==$timezone_id){
                 return $val;
             }
          }
    }

    public static function getitemarray($request){
      $shipping=Shipping::all();
      $cartCollection = Cart::getContent();
      $dataarr =array();
      $total =array();
      if($request->get("shipping_type")==1){
          $delivery_charges=$shipping[0]->cost;
      }else{
          $delivery_charges=$shipping[1]->cost;
      }
      $jsondata=self::getorderjson();        
      foreach($jsondata["order"] as $k) {
         $totalcharge=$delivery_charges*$k["ProductQty"];
         $total[]=$k["ProductTotal"]-$k["exterdata"]["couponprice"]+$k["tax_amount"]+$totalcharge;
         $productinfo = Product::find($k["ProductId"]);
         $dataarr[]=array("name"=>$productinfo->name,"qty"=>$k["ProductQty"],"total_amount"=>$k["ProductTotal"]);
      }
      return array('itemlist' =>$dataarr,"amount" =>array_sum($total));
    }
        
    public static function OrderPlace($request,$is_complete,$transaction_id)
    {
      $setting=Setting::find(1);
      $shipping=Shipping::all();
      $cartCollection = Cart::getContent();
      $gettimezone=self::gettimezonename($setting->default_timezone);
      date_default_timezone_set($gettimezone);
      $input = $request->input();
      DB::beginTransaction();      
      try {
              $store=new Order();
              $store->user_id=Auth::id();
              $store->orderdate=date("d-m-Y h:i:s");
              $store->payment_method=$request->get("payment_method");
              $store->billing_first_name=$request->get("order_firstname");
              $store->billing_address=$request->get("order_billing_address");
              $store->billing_city=$request->get("order_billing_city");
              $store->billing_pincode=$request->get("order_billing_pincode");
              $store->phone=$request->get("order_phone");
              $store->email=$request->get("order_email");
              $store->to_ship=$request->get("to_ship");
              $store->notes=$request->get("order_notes");
              $store->shipping_city=$request->get("order_shipping_city");
              $store->shipping_pincode=$request->get("order_shipping_pincode");
              $store->shipping_first_name=$request->get("order_ship_firstname");
              $store->shipping_address=$request->get("order_shipping_address");
              $store->shipping_method=$request->get("shipping_type");
              $store->is_completed = $is_complete;
              $store->transaction_id = $transaction_id;
              $store->save();
              $storeres=new OrderResponse();
              $storeres->order_id=$store->id;
              $storeres->desc=json_encode(self::getorderjson());
              $storeres->save();
              if($request->get("shipping_type")==1){
                      $delivery_charges=$shipping[0]->cost;
              }else{
                      $delivery_charges=$shipping[1]->cost;
              }
                $dataarr =array();
                $jsondata=self::getorderjson();        
                foreach($jsondata["order"] as $k) {
                    $add=new OrderData();
                    $add->order_id=$store->id;
                    $add->order_no= $store->id."#".mt_rand(100000, 999999);
                    $add->product_id=$k["ProductId"];
                    $add->seller_id=$k["seller_id"];
                    $add->process_datetime=date("Y-m-d h:i:s");
                    $add->quantity=$k["ProductQty"];
                    $add->price=$k["ProductAmt"];
                    $add->per_product_seller_price=$k['seller_price'];
                    $add->per_product_commission_price=$k['admin_commission'];
                    $add->admin_txt_price=$k['admin_txt_price'];
                    $add->seller_txt_price=$k['seller_txt_price'];
                    $add->tax_charges=$k["tax_amount"];
                    $add->tax_name=$k["tax_name"];
                    $add->option_name=$k["exterdata"]["option"];
                    $add->label=$k["exterdata"]["label"];
                    $add->option_price=$k["exterdata"]["price"];
                    $add->coupon_code=$k["exterdata"]["couponcode"];
                    $add->coupon_price=$k["exterdata"]["couponprice"];
                    $totalcharge=$delivery_charges*$k["ProductQty"];
                    $add->delivery_charges=$totalcharge;
                    $add->total_amount=$k["ProductTotal"]-$k["exterdata"]["couponprice"];
                    $total[]=$k["ProductTotal"]-$k["exterdata"]["couponprice"]+$k["tax_amount"]+$totalcharge;
                    $add->status=0;
                    $add->save();
                }
                $store->total=array_sum($total);
                $store->save();
                
                DB::commit();
                 $setting=Setting::find(1);
                if($is_complete=='1'){
                  $getdata=OrderData::where("order_id",$store->id)->get();
                    foreach ($getdata as $k) {
                       $message = array("id"=>$k->id,"data" => "New order");
                       Notifyuser::generate(Auth::id(),$k->seller_id,'Order','3',$message,"new_order_create");
                       Notifyuser::generate(Auth::id(),1,'Order','3',$message,"new_order_create");
                       $msg = 
                       $android=$this->send_notification_android($setting->android_api_key,$k->user_id,$msg,$store->id,'user_id');
                       $ios=$this->send_notification_IOS($setting->iphone_api_key,$k->user_id,$msg,$store->id,'user_id');
                       
                       $android=$this->send_notification_android($setting->android_api_key,$k->seller_id,$msg,$store->id,'seller_id');
                       $ios=$this->send_notification_IOS($setting->iphone_api_key,$k->seller_id,$msg,$store->id,'seller_id');
                   }
                }
                return $store->id;
              } catch (\Exception $e) {
                DB::rollback();
                return 0;
          }
    }
      
    public static function preOrderPlacetoApi($request,$is_complete)
    {

        $setting=Setting::find(1);
        $shipping=Shipping::all();
        $cartCollection = Cart::getContent();
        $gettimezone=self::gettimezonename($setting->default_timezone);
        date_default_timezone_set($gettimezone);
        $input = $request->input();
       DB::beginTransaction();      
           try {

                $jsondata=json_decode($request->get("orderjson")); 

                $store = Order::where("user_id",$request->get("user_id"))->where("is_completed",'0')->first();

                if(empty($store)){
                    $store=new Order();
                }else{
                    OrderData::where("order_id",$store->id)->delete();
                    OrderResponse::where("order_id",$store->id)->delete();
                }
                 $store->user_id=$request->get("user_id");
                 $store->is_completed = $is_complete;
                $store->shipping_method = $request->get("shipping_method");
                 if($request->get("shipping_method")==1){
                    $delivery_charge=$setting->delivery_charges;
                }else{
                       $delivery_charge=0;
                }
                $store->sub_total =$request->get("sub_total");
                 $store->total =$request->get("total");
                if($request->get("coupon_code") !="" && !empty($request->get("coupon_code")))
                {
                    $store->coupon_code =$request->get("coupon_code");
                }
                if($request->get("coupon_price") !="" && !empty($request->get("coupon_price")))
                {
                    $store->coupon_price =$request->get("coupon_price");
                }
                
                
                $store->delivery_charge =$delivery_charge;
                $store->tax =$jsondata->tax;
                $store->orderplace_datetime =  date("Y-m-d h:i:s");
                $store->status=0;
                $store->seller_id=$request->get("seller_id");
                $store->save();
                $store->order_no= $store->id."#".mt_rand(100000, 999999);
                $store->save();
                
               
                $storeres=new OrderResponse();
                $storeres->order_id=$store->id;
                $storeres->desc=$request->get("orderjson");
                
                $storeres->save();
                $dataarr =array();
                
                foreach($jsondata->order as $k) {
                     $product = Product::find($k->ProductId);
                     $categorypro=Categories::find($product->category);
                     
                     $add=new OrderData();
                     $product->totalorders = $product->totalorders+1;
                     $product->save();
                     /*if($request->get("shipping_method")==1){
                            $delivery_charges=$categorypro->delivery_charges;
                     }else{
                            $delivery_charges=$shipping[1]->cost;
                     }*/
                     $totalcharge=$store->delivery_charge;
                     $coupon = isset($k->exterdata->couponprice)?$k->exterdata->couponprice:'0';
                     $productprice = $k->ProductQty*$k->ProductAmt;
                     $totalsales = ($productprice)-($coupon)+($k->tax_amount);
                      
                      $admin_commission = $totalsales - ($categorypro->commission+$totalcharge);
                      $add->order_id=$store->id;
                      //$add->order_no= $store->id."#".mt_rand(100000, 999999);
                      $add->product_id=$k->ProductId;
                      $add->seller_id=$product->user_id;
                      //$add->orderplace_datetime =  date("Y-m-d h:i:s");
                      $add->quantity=$k->ProductQty;
                      $add->price=$k->ProductAmt;
                      $add->per_product_seller_price=$admin_commission;
                      $add->per_product_commission_price=$categorypro->commission;                     
                      //$add->tax_charges=isset($k->tax_amount)?$k->tax_amount:'';
                      //$add->tax_name=isset($k->tax_name)?$k->tax_name:'';
                      $add->option_name= $k->exterdata->option;
                      $add->label= $k->exterdata->label;
                      $add->option_price= $k->exterdata->price;
                    //   $add->coupon_code = isset($k->exterdata->couponcode)?$k->exterdata->couponcode:'0';
                    //   $add->coupon_price= isset($k->exterdata->couponprice)?$k->exterdata->couponprice:'0';
                      //$add->delivery_charges=$totalcharge;
                      $add->total_amount= $totalsales;
                      
                      $total[]=$totalsales;
                      //$add->status=0;
                      $add->save();
                  }
                 // $store->total=array_sum($total);
                  $store->save();
                  DB::commit();
                  return $store->id;
            } catch (\Exception $e) {
                    DB::rollback();
                    return 0;
            }
    }

    public static function OrderPlacetoApi($request,$is_complete)
    {
       // dd($request);
        $setting=Setting::find(1);
        //$payment_model = PaymentMethod::find(2);
        $shipping=Shipping::all();
        $cartCollection = Cart::getContent();
        $gettimezone=self::gettimezonename($setting->default_timezone);
        date_default_timezone_set($gettimezone);
        $input = $request->input();
        DB::beginTransaction();      
         try {
                $store=Order::find($request->get("id"));
                
                // dd($store);
                $store->shipping_method=$request->get("shipping_method");
                $store->payment_method=$request->get("payment_method");
                $store->user_address_id=$request->get("user_address_id");
                /*$store->delivery_time=$request->get("delivery_time");
                $store->delivery_date=$request->get("delivery_date");*/
                $store->is_completed = '1';
                $store->notes = $request->get("notes");
                $store->save();
               
               DB::commit();
                if($is_complete=='1'){
                   
                      $getdata=Order::where("id",$store->id)->first();
                        //echo "<pre>";print_r($getdata); die();
                         $message = array("id"=> $getdata->id,"data" => "New order");
                         //Notifyuser::generate($request->get("user_id"), $getdata->seller_id,'Order','3',$message,"new_order_create");
                        // Notifyuser::generate($request->get("user_id"),1,'Order','3',$message,"new_order_create");
                     
                  }
                  
                return $store->id;
                  } catch (\Exception $e) {
                  DB::rollback();
                       return 0;
                 }
    }

    public static function getorderjson()
    {
      $cartCollection = Cart::getContent();
      $total1=0;
      $main_array=array();  
        foreach ($cartCollection as $item) {
           $order=array();
           $gettotal=array();
           $subtotal=$item->price*$item->quantity;
           $producttax=Product::where("name",$item->name)->first();
           $categorypro=Categories::find($producttax->category);
           $taxdata=Taxes::find($producttax->tax_class);
           $total=$item->price*$categorypro->commission/100;
           $a=$taxdata->rate/100;
           $b=$subtotal*$a;
           $totaltxt=number_format((float)$b, 2, '.', '');
           $admin_txt=$totaltxt*$categorypro->commission/100;
           $order["ProductId"]=$producttax->id;
           $order["seller_id"]=$producttax->user_id;
           $order["ProductQty"]=$item->quantity;
           $order["ProductAmt"]=$item->price;
           $order["ProductTotal"]=$item->price*$item->quantity;
           $order["tax_name"]=$taxdata->tax_name;
           $order["admin_commission"]=number_format($total,2,'.','');
           $order["seller_price"]=number_format($item->price-$total,2,'.','');
           $order["admin_txt_price"]=number_format($admin_txt,2,'.','');
           $order["seller_txt_price"]=number_format($totaltxt-$admin_txt,2,'.','');
           $order["tax_amount"]=$totaltxt;
           $order["exterdata"]=$item->attributes[0];          
           $main_array[]=$order;
           $total1=$total1+$b;
        }
     return array("order"=>$main_array,"total"=>number_format($total1,2,'.',''));
    }
}
