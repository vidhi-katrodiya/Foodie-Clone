<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Sentinel;
use Session;
use DataTables;
use App\Models\Order;
use App\Models\Review;
use App\Models\Paymentlogs;
use App\Models\Shipping;
use App\Models\OrderResponse;
use App\Models\OrderData;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Taxes;
use App\Models\PaymentMethod;
use App\Models\Token;
use App\Models\Lang_core;
use App\Models\FileMeta;
use App\Models\Notifyuser;
use App\Models\Addresses;
use App\Models\Delivery;
use App\Models\OrderDataApp;
use App\Models\User;
use Mail;
use Image;
use Config;
use PDF;
use Hash;
use DateTimeZone;
use DateTime;
use DB;
use Cart;
use Auth;
use Redirect;

class OrderController extends Controller {
     use SerializesModels;
   public function __construct() {
         parent::callschedule();
    }
  public function showorder(){
      $lang = Lang_core::all();
      $delivery = Delivery::where("attendance","yes")->where("is_deleted",'0')->get();
      return view("admin.order.default")->with("lang",$lang)->with("delivery",$delivery);
  }

  public function get_paymet_page(Request $request)
  {  
    // print_r($request->get("id"));
    // die();
    $enc_id = $this->decyptstring($request->get("id"));
    // $data=OrderDataApp::find($enc_id);
    $data=$data::where('id',$enc_id)->orderBy('id', 'DESC')->first();
    $setting=Setting::find(1);
    /*echo "<pre>";
    print_r($data);
    die(); */
    if($data->payment_method == 2)
    {
      $payment=PaymentMethod::find(2); 
      return view("stripe_payement")->with("dec_id",$request->get("id"))->with("setting",$setting)->with("payment",$payment)->with("data",$data);
    }
    if($data->payment_method == 1)
    {
        return view("paypal_view")->with("dec_id",$request->get("id"))->with("setting",$setting)->with("data",$data);
     // return redirect('payWithpaypal?dec_id='.$request->get("id"));
    }

  }

  public function pay_stripe(Request $request)
  {  
    $id = $this->decyptstring($request->get("dec_id"));
    $data=OrderDataApp::find($id);
    /*echo "<pre>";
    print_r($request->get("stripeToken"));
    print_r($data);
    die();*/
      // CURLOPT_URL => "https://customise.freaktemplate.com/grocery/api/placeorder",
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => url('api/placeorder'),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('id'=>$data->order_id,'payment_method' =>$data->payment_method,'user_address_id' =>$data->user_address_id,'lang' =>$data->lang,'delivery_time' => $data->delivery_time,'delivery_date'=> $data->delivery_date,'notes'=> $data->notes,'payment_type'=> 'Stripe','stripeToken'=> $request->get("stripeToken")),
    ));

    $response = curl_exec($curl);

    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
       /*echo $response;
       die();*/
      $data1 = json_decode($response,true);
      // print_r($data1['data']['status']);
      if($data1['data']['status'] == 0)
      {
        // return view("payment_fail");
         return redirect('payment_fail');
      }
      if($data1['data']['status'] == 1)
      {
          $data->delete();
        // return view("payment_success");
        return redirect('payment_success');
      }
    }

  }

   public function payment_success()
  {
     return view("payment_success");
   // return redirect()->away(env('PAYMENT_SUCCESS_URL'));
  }

  public function payment_fail()
  {
    return view("payment_fail");
  }

  public function orderdatatable(){
        // $order = OrderData::with('Orderdetail')->get();
         $order = Order::with('orderdatals')->get();
         
         return DataTables::of($order)
            ->editColumn('id', function ($order) {
                return $order->id;
            })
            ->editColumn('name', function ($order) {
                if(isset($order->user_id)){
                      $data=User::find($order->user_id);
                      if($data){
                          return $data->first_name;
                      }
                      else{
                        return "";
                     }   
                }
            })
            ->editColumn('shipping_method', function ($order) {
                if(isset($order->shipping_method)){
                    $data=Shipping::find($order->shipping_method);
                     if($data){
                        return $data->label;
                     }
                     return "";
                }
                 
            })
             ->editColumn('payment_method', function ($order) {
                 if(isset($order->payment_method)&&$order->payment_method=="1"){
                    return __('messages.paypal');
                 }elseif(isset($order->payment_method)&&$order->payment_method=="2"){
                    return __('messages.stripe');
                 }else{
                    return __('messages.case_on_delivery');
                 }
            })
         
            ->editColumn('total', function ($order) {
                 $setting=Setting::find(1);
                 $getcurrency=explode("-",$setting->default_currency);
                 return $getcurrency[1].number_format($order->total,2,'.','');
            })
             ->editColumn('view', function ($order) {                 
                 return $order->id;
            })
            
             
             ->editColumn('action', function ($order) {
                 $return ="";
                if($order->status=='3'&&$order->shipping_method=='1'){
                  $return='<a onclick="assign_order(' . "'" . $order->id . "'" . ')" rel="tooltip" title="" class="btn btn-sm btn-success" data-original-title="Remove" data-toggle="modal" data-target="#assignorder" style="color: white !important;">'.__('messages.assign_order').'</a>';
                }
                return $return;
                 
            })          
            ->make(true);
  }
  
  
 /* public function vieworder($id){    
    $data=OrderData::with("Orderdetail","productdata")->find($id);
      if($data->productdata){
          $getlang = FileMeta::where("model_id",$data->product_id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
          $data->productdata->name = isset($getlang)?$getlang->meta_value:'';
                    
      }
     $user=User::find($data->Orderdetail->user_id);
     $data->sellerinfo=User::find($data->seller_id);
     $shipping=Shipping::find($data->Orderdetail->shipping_method);
     $setting=Setting::find(1);
     $res_curr=explode("-",$setting->default_currency);
     $lang = Lang_core::all();
     $useraddress = Addresses::find($data->Orderdetail->user_address_id);
     return view("admin.order.vieworder")->with("order",$data)->with("user",$user)->with("shipping",$shipping)->with("currency",$res_curr[1])->with("setting",$setting)->with("lang",$lang)->with("useraddress",$useraddress);
  }*/
  public function vieworder($id){    
     $data = Order::with('orderdatals')->find($id);

     $item_data=OrderData::with('productdata')->where('order_id',$data->id)->get();

     $user=User::find($data->user_id);
     $data->sellerinfo=User::find($data->seller_id);
     $shipping=Shipping::find($data->shipping_method);
   
     $setting=Setting::find(1);
     $res_curr=explode("-",$setting->default_currency);
     $lang = Lang_core::all();
     $useraddress = Addresses::find($data->user_address_id);
     return view("admin.order.vieworder")->with("order",$data)->with("item_data",$item_data)->with("user",$user)->with("shipping",$shipping)->with("currency",$res_curr[1])->with("setting",$setting)->with("lang",$lang)->with("useraddress",$useraddress);
  }

   public function generateorderpdf($id){

      $setting=Setting::find(1);
      $order=Order::with('orderdatals')->where("id",$id)->where("seller_id",Auth::id())->first();

      if($order){
          $item_data=OrderData::with('productdata')->where('order_id',$order->id)->get();
          $res_curr=explode("-",$setting->default_currency);
          $order->setting=$setting;    
          $order->currency=$res_curr[1];
          $order->seller_info=User::find($order->seller_id);
          
          $order->Orderdetail=Order::find($order->order_id);
          $order->user=User::find($order->user_id);
          $order->useraddress=Addresses::find($order->user_address_id);
          $order->productdata=Product::find($order->product_id);
          
         // echo "<pre>";print_r($order);exit;
          $file_name=$this->getName();
          $pdf = PDF::loadView('pdf.invoice',compact('order','item_data'));
          $pdf->setPaper('a4', 'landscape');
          $pdf->setWarnings(false);
          return $pdf->download('invoice.pdf');
      }else{
          return redirect()->back();
      }
  }

  public function latestorder(){
       $order =Order::orderBy('id','DESC')->take(10)->get();
     
         return DataTables::of($order)
            ->editColumn('id', function ($order) {
                return $order->id;
            })
            ->editColumn('customer', function ($order) {
                  if(isset($order->user_id)){
                      $data=User::find($order->user_id);
                      if($data){
                          return $data->first_name;
                      }
                      else{
                        return "";
                     }   
                }
            })
            ->editColumn('status', function ($order) {
                 if($order->status=='6'){
                   $return=__("messages.canceled");
                   if($order->payment_method!='3'){
                        $return=__("messages.refunded");
                   }
                 }else if($order->status=='5'){
                   $return=__("messages.completed");
                 }else if($order->status=='2'){
                   $return=__("messages.on_hold");
                 }else if($order->status=='3'||$order->status=='0'){
                  $return=__("messages.pending");
                }else if($order->status=='1'){
                  $return=__("messages.processing");
                }else if($order->status=='7'&&$order->payment_method!='3'){
                  $return=__("messages.refunded");
                }
                else if($order->status=='4'){
                  $return=__("messages.out_of_delivery");
                }else{
                  $return="";
                }
                return $return;
                 
            })
            ->editColumn('total', function ($order) {
                $setting=Setting::find(1);
                $getcurrency=explode("-",$setting->default_currency);
                return $getcurrency[1].number_format($order->total_amount,2,'.','');
                //return $order->currency.$order->total_amount;
            })      
            ->make(true);
  }

  public function sendordermail(Request $request){
       $user=User::find($request->get("user_id"));
       $user->pdffile=public_path().'/pdf/'.'/'.$request->get("filename");
        try {
           
               $result=Mail::send('email.view_order', ['user' => $user], function($message) use ($user){
                   $message->to($user->email,$user->first_name)->subject('shop on');
                   $message->attach($user->pdffile);
                });
            
        } catch (\Exception $e) {
        }
        Session::flash('message',__('messages_error_success.mail_send_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
  }

  public function latestreview(){
      $order =Review::with('product','userdata')->take(10)->get();
         return DataTables::of($order)
            ->editColumn('product_id', function ($order) {
                if($order->product){
                     $getmeta = Filemeta::where("model_id",$order->product_id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
                    return isset($getmeta->meta_value)?$getmeta->meta_value:'';
                }
            })
            ->editColumn('customer', function ($order) {
               if($order->userdata){
                 return $order->userdata->first_name;
               }
               else{
                  return "";
               }
                
              
            })
            ->editColumn('ratting', function ($order) {
                  return $order->ratting.'/5';
            })
                
            ->make(true);
  }

  public function showtransactionorder(){
     return view("admin.order.transaction");
  }
  
  public function transactiondatatable(){
      $order =Order::orderBy('id','DESC')->where("payment_method",'!=',3)->get();
         return DataTables::of($order)
            ->editColumn('id', function ($order) {
                return $order->id;
            })
            ->editColumn('transaction', function ($order) {
                 if($order->payment_method==2){
                    return $order->charges_id;
                 }
                 if($order->payment_method==1){
                    return $order->paypal_payment_Id;
                 }
                 
            })
            ->editColumn('payment_method', function ($order) {
                  if($order->payment_method==2){
                    return __('messages.paypal');;
                 }
                 if($order->payment_method==1){
                    return __('messages.stripe');;
                 }
            })
                
            ->make(true);
  }

  function getName() { 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
  
    for ($i = 0; $i <5; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 
  
    return "shopno"."_".date('d-m-Y')."_".$randomString.".pdf"; 
} 


public function show_assignorder(Request $request){
     $getorder=Order::find($request->get("id"));
     if($getorder){
           $setting=Setting::find(1);
     
     // date_default_timezone_set($gettimezone);
           $date = $this->getsitedate();
           $msg=__('successerr.order_assign_success');
           $android=$this->send_notification_android($setting->android_api_key,$getorder->user_id,$msg,$getorder->id);
           $ios=$this->send_notification_IOS($setting->iphone_api_key,$getorder->user_id,$msg,$getorder->id);
           $android=$this->send_notification_android($setting->android_api_key,$getorder->delivery_boyid,$msg,$getorder->id);
           $ios=$this->send_notification_IOS($setting->iphone_api_key,$getorder->delivery_boyid,$msg,$getorder->id);
          $getorder->status='4';
          $getorder->assign_datetime=$date;
          $getorder->assign_id=$request->get("assign_id");
          $getorder->assigned='1';
          $getorder->save();
          Session::flash('message',__('messages.order_assign_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
     }
     else{
        Session::flash('message',__('messages.order_not_found')); 
        Session::flash('alert-class', 'alert-danger');
        return redirect()->back();
     }
   }
     public function changeorderstatus($order_id,$staus_id){
         $paymentdata=PaymentMethod::find(2);
        //  DB::beginTransaction();
             //try {
                      $msg="";
                      //$order=OrderData::with("Orderdetail")->where("id",$order_id)->first();
                      $order=Order::with("orderdatals")->where("id",$order_id)->first();
                      $setting=Setting::find(1);
                      $user=User::find($order->user_id);
                      if(!$user){
                            Session::flash('message',__('messages_error_success.user_not_exist')); 
                            Session::flash('alert-class', 'alert-success');
                            return redirect()->back();
                      }
                       if($staus_id==1){//accept
                                  $order->accept_datetime=$this->getsitedate();
                                  $msg=__('messages.Order Has Been Accept Successfully');
                                  $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                  $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }else if($staus_id==2){//reject
                                $order->reject_datetime=$this->getsitedate();
                                $msg=__('messages.Order Has Been Rejected Successfully');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }else if($staus_id==3){//prepare
                                $order->prepare_datetime=$this->getsitedate();
                                $msg=__('messages.Order Has Been Prepared Successfully');
                                $android=$this->send_notification_android($setting->android_api_key,$order->user_id,$msg,$order->id);
                                $ios=$this->send_notification_IOS($setting->iphone_api_key,$order->user_id,$msg,$order->id); 
                             }
                             
                                if($staus_id=='6'&&$order->shipping_method='2'){ // accept
                                    $msg=__('messages.the order has been prepare by the Seller');
                                    $order->out_for_delivery_datetime=$this->getsitedate();
                                    $android=$this->send_notification_android($setting->android_api_key,$getorder->user_id,$msg,$orderdata->id);
                                    $ios=$this->send_notification_IOS($setting->iphone_api_key,$getorder->user_id,$msg,$orderdata->id);
                                     
                                  
                                }
                    
                                 if($staus_id=='7'&&$get_order->shipping_method='2'){ // accept
                                    $msg=__('messages.the order has been Deliver by the Seller');
                                    $order->complete_datetime=$this->getsitedate();
                                    $android=$this->send_notification_android($setting->android_api_key,$getorder->user_id,$msg,$orderdata->id);
                                    $ios=$this->send_notification_IOS($setting->iphone_api_key,$getorder->user_id,$msg,$orderdata->id);
                                     
                                    
                                }


                      $order->status=$staus_id;
                      $order->save();
                //DB::commit();
 
                       $message = array("id"=>$order->id,"data" => $msg);
                       if($staus_id == 1 || $staus_id == 2||$staus_id == 3){ // order accept notification for admin and user
                           Notifyuser::generate(Auth::id(),1,'Order','2',$message,"order_status_change");
                            Notifyuser::generate(Auth::id(),$order->user_id,'Order','2',$message,"order_status_change");
                       }
                       
                      


                      
                             Session::flash('message',__('messages_error_success.order_status_change')); 
                             Session::flash('alert-class', 'alert-success');
                             return redirect()->back();
                        //}
                        //catch (\Exception $e) {
                        //DB::rollback();
                        //Session::flash('message',$e); 
                                       //    Session::flash('alert-class', 'alert-danger');
                                 //          return redirect()->back();       
                                  //    }
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
  
     public function cashorder(Request $request){
        $setting=Setting::find(1);
        $shipping=Shipping::all();
        $cartCollection = Cart::getContent();
        $gettimezone=$this->gettimezonename($setting->default_timezone);
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
                        $store->subtotal=number_format(Cart::gettotal(), 2, '.', '');
                        $store->shipping_method=$request->get("shipping_type");
                        $getjson=$this->getorderjson();
                        if($request->get("couponcode")){
                            $datacoupoun=$this->verifiedcoupon($request->get("couponcode"));
                            if($datacoupoun->discount_type==1){
                                $coupon_price=(Cart::gettotal()*$datacoupoun->value)/100;
                            }else{
                                $coupon_price=$datacoupoun->value;
                            }
                            $store->is_freeshipping=$datacoupoun->free_shipping;
                            $charges=0;
                            if($datacoupoun->free_shipping==1){
                                $store->shipping_charge="0.00";
                            }else{
                                    if($request->get("shipping_type")==1){
                                        $charges=$shipping[0]->cost;
                                        $store->shipping_charge=$shipping[0]->cost;
                                    }else{
                                        $charges=$shipping[1]->cost;
                                        $store->shipping_charge=$shipping[1]->cost;
                                    }
                            }
                            
                            $store->coupon_code=$request->get("couponcode");
                            $store->coupon_price=$coupon_price;
                        }else{
                            $store->is_freeshipping=0;
                            if($request->get("shipping_type")==1){
                                $charges=$shipping[0]->cost;
                                $store->shipping_charge=$shipping[0]->cost;
                            }else{
                                $charges=$shipping[1]->cost;
                                $store->shipping_charge=$shipping[1]->cost;
                            }
                            $store->coupon_code="";
                            $store->coupon_price="";
                            $coupon_price=0;
                        }
                        $store->taxes_charge=number_format($getjson["total"], 2, '.', '');;
                        $total=Cart::gettotal()+$getjson["total"]+$charges-$coupon_price;
                        $store->total=number_format($total, 2, '.', '');
                       
                        $store->order_status='3';
                $store->save();
                $storeres=new OrderResponse();
                $storeres->order_id=$store->id;
                $storeres->desc=json_encode($this->getorderjson());
                $storeres->save();
                $jsondata=$this->getorderjson();
        
                  foreach($jsondata["order"] as $k) {
                      $add=new OrderData();
                      $add->order_id=$store->id;
                      $add->product_id=$k["ProductId"];
                      $add->quantity=$k["ProductQty"];
                      $add->price=$k["ProductAmt"];
                      $add->total_amount=$k["ProductTotal"];
                      $add->tax_charges=$k["tax_amount"];
                      $add->tax_name=$k["tax_name"];
                      $add->option_name=$k["exterdata"]["option"];
                      $add->label=$k["exterdata"]["label"];
                      $add->option_price=$k["exterdata"]["price"];
                      $add->save();
                  }
                   if($request->get("payment_method")==2){
                       try{
                        \Stripe\Stripe::setApiKey(Session::get("stripe_secert"));
                          $unique_id = uniqid(); 
                          $charge = \Stripe\Charge::create(array(
                              'description' => "Amount: ".number_format($total, 2, '.', '').' - '. $unique_id,
                              'source' => $input['stripeToken'],                    
                              'amount' => (int)(number_format($total, 2, '.', '') * 100), 
                              'currency' => 'USD'
                          ));
                          $data=Order::find($store->id);
                          $data->charges_id=$charge->id;
                          $data->save();
                        }catch (\Exception $e) {
                           Session::flash('message', __('messages_error_success.payment_fail')); 
                            Session::flash('alert-class', 'alert-success');
                            return Redirect::route('checkout');
                      }
                   }
                      $data=array();
                      $data['email']=$setting->email;
                      $data['name']="Shop";
                      $data['customer_name']=$request->get("order_firstname")." ".$request->get("order_lastname");
                      $data['order_amount']=number_format($total, 2, '.', '');
                      try {
                            if(Config::get('mail.username')!=""&&$$setting->admin_order_mail=='1'){
                                     Mail::send('email.orderdetail', ['user' => $data], function($message) use ($data){
                                         $message->to($data['email'],$data['name'])->subject('shop on');
                                     });
                            }
                       } catch (\Exception $e) {

                       }
                      
                DB::commit();
                  Cart::clear();
                    Session::flash('message',__('messages_error_success.order_place_success')); 
                    Session::flash('alert-class', 'alert-success');
                     return redirect("vieworder/".$store->id);
                } catch (\Exception $e) {
                     DB::rollback();
                    Session::flash('message',$e); 
                    Session::flash('alert-class', 'alert-danger');
                    return redirect()->back();
          }
     }
    
     function headreadMoreHelper($story_desc, $chars =35) {
    $story_desc = substr($story_desc,0,$chars);  
    $story_desc = substr($story_desc,0,strrpos($story_desc,' '));  
    $story_desc = $story_desc;  
    return $story_desc;  
} 

     public function getorderjson(){
      $cartCollection = Cart::getContent();
      $total=0;
      $main_array=array();  
        foreach ($cartCollection as $item) {
           $order=array();
           $gettotal=array();
           $subtotal=$item->price*$item->quantity;
           $getlang = FileMeta::where("model_name","Product")->where("meta_key","name")->where("meta_value",$item->name)->first();
            if($getlang){
                     $producttax=Product::find($getlang->model_id);
                     $taxdata=Taxes::find($producttax->tax_class);
                     $a=$taxdata->rate/100;
                     $b=$subtotal*$a;
                     $getlang = FileMeta::where("model_id",$taxdata->id)->where("model_name","Taxes")->where("meta_key","tax_name")->where("lang",Session::Get("locale"))->first();
                     $order["ProductId"]=$producttax->id;
                     $order["ProductQty"]=$item->quantity;
                     $order["ProductAmt"]=$item->price;
                     $order["ProductTotal"]=$item->price*$item->quantity;
                     $order["tax_name"]=isset($getlang->meta_value)?$getlang->meta_value:'';
                     $order["tax_amount"]=number_format((float)$b, 2, '.', '');
                     $order["exterdata"]=$item->attributes[0];
                     $main_array[]=$order;
                     $total=$total+$b;
            }
        }
     return array("order"=>$main_array,"total"=>$total);
   }

  public function notification($act){
      $data=array();
      if($act==1){
         $result=$this->haveOrdersNotification();
           $orderdata=$this->haveOrdersdata();
            if(isset($result)){
               $data = array(
                      "status" => http_response_code(),
                      "request" => "success",
                      "response" => array(
                      "message" => "Request Completed Successfully",
                      "total" => $result,
                      "orderdata"=>$orderdata
               )
             );
           }
           $updatenotify=$this->updatenotify();

      }
      else{
           $result=$this->haveOrdersNotification();
           $orderdata=$this->haveOrdersdata();
            if(isset($result)){
               $data = array(
                      "status" => http_response_code(),
                      "request" => "success",
                      "response" => array(
                      "message" => "Request Completed Successfully",
                      "total" => $result,
                      "orderdata"=>$orderdata
               )
             );
           }
       }
       return $data;
     }

     public function haveOrdersNotification(){
        $user = Sentinel::getuser();
        $order=Notifyuser::where("receiver_id",$user->id)->where("read",'0')->get();
        return count($order);
     }
      public function haveOrdersdata(){
        $user = Sentinel::getuser();
        $order=Notifyuser::where("receiver_id",$user->id)->orderby("id","DESC")->where("read",'0')->get();

//echo "<pre>";print_r($order);exit;
        foreach ($order as $k) {
             if($k->sender_type==4){
                $k->sender_name=(DeliveryBoy::find($k->sender_id))?DeliveryBoy::find($k->sender_id)->name:"";
                $data=DeliveryBoy::find($k->sender_id);
                if(isset($data)&&isset($data->profile)&&$data->profile!=""){
                    $k->image=asset('public/upload/profile').'/'.$data->profile;
                }else{
                    $k->image=asset('public/upload/profile/defaultuser.jpg');
                }
                
             }else{
                $k->sender_name=(User::find($k->sender_id))?User::find($k->sender_id)->name:"";
                 $data=User::find($k->sender_id);
                if(isset($data)&&isset($data->profile_pic)&&$data->profile_pic!=""){
                    $k->image=asset('public/upload/profile').'/'.$data->profile_pic;
                }else{
                    $k->image=asset('public/upload/profile/defaultuser.jpg');
                }
             }
        }
        return $order;
     }

     public function updatenotify(){
      $user = Sentinel::getuser();
      $order=Notifyuser::where("receiver_id",$user->id)->where("read",'0')->get();
      foreach ($order as $k) {
         $k->read='1';
         $k->save();
      }
      return "done";
     }

  

    /*public function sellervieworder($id){
     $data=OrderData::with("Orderdetail","productdata")->find($id);
      if($data->productdata){
          $getlang = FileMeta::where("model_id",$data->product_id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
          $data->productdata->name = isset($getlang)?$getlang->meta_value:'';
                    
      }
     $user=User::find($data->Orderdetail->user_id);
     $data->sellerinfo=User::find($data->seller_id);
     $shipping=Shipping::find($data->Orderdetail->shipping_method);
     $setting=Setting::find(1);
     $res_curr=explode("-",$setting->default_currency);
     $lang = Lang_core::all();
     return view("seller.vieworder")->with("order",$data)->with("user",$user)->with("shipping",$shipping)->with("currency",$res_curr[1])->with("setting",$setting)->with("lang",$lang);
    }
    */
     public function sellervieworder($id){
       $data = Order::with('orderdatals')->find($id);
 
     $item_data=OrderData::with('productdata')->where('order_id',$data->id)->get();
     $user=User::find($data->user_id);
     $data->sellerinfo=User::find($data->seller_id);
     $shipping=Shipping::find($data->shipping_method);
     $setting=Setting::find(1);
     $res_curr=explode("-",$setting->default_currency);
     $lang = Lang_core::all();
     return view("seller.vieworder")->with("order",$data)->with("item_data",$item_data)->with("user",$user)->with("shipping",$shipping)->with("currency",$res_curr[1])->with("setting",$setting)->with("lang",$lang);
  }

}