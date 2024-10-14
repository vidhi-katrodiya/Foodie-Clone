<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use Redirect;
use URL;
use Mail;
use Image;
use PDF;
use Hash;
use DateTimeZone;
use App\Models\User;
use App\Models\Order;
use App\Models\Paymentrecord;
use App\Models\PaymentMethod;
use App\Models\Notifyuser;
use App\Models\CompletePayment;
use App\Models\Lang_core;
use App\Models\OrderData;
use App\Models\Setting;
use App\Models\Shipping;
use Razorpay\Api\Api;
use DateTime;
use DB;
use Cart;
use Auth;
use PaytmWallet;
class PaymentController extends Controller {
  
    public function pendingshowpayment(){
        $lang = Lang_core::all();
        $listofseller=User::where("user_type",'3')->where("is_active",'1')->get();
        return view("admin.payment.pendingdefault")->with("listofseller",$listofseller)->with("lang",$lang);
    }
    
    
    public function currentpayment(){
        $lang = Lang_core::all();
        return view("admin.payment.current")->with("lang",$lang);
    }
   
    public function ordercurrentpaymentTable(){
         $data = Order::orderBy('seller_id', 'desc')
                ->groupBy('seller_id')
                ->where("status","!=",7)
                ->where("status","!=",6)
                ->whereDate("created_at",date('Y-m-d'))
                ->get();
       
        
        return DataTables::of($data)
            ->editColumn('id', function ($data) {
                return $data->seller_id;
            })
            ->editColumn('name', function ($data) {
                $data=User::find($data->seller_id);
                if($data){
                    return $data->first_name;
                }else{
                    return '';
                }
            })
            ->editColumn('payment', function ($data) {
              $total=Order::where("seller_id",$data->seller_id)->sum('per_product_seller_price');
                   return $total;
            })       
            ->make(true);
    }
    public function paymenthistory(){
        $lang = Lang_core::all();
         return view("seller.paymenthistory")->with("lang",$lang);
    }
    public function sellerpaymenthistory(){
        $payment =Paymentrecord::where("seller_id",Auth::id())->orderBy('date', 'desc')->get();
        return DataTables::of($payment)
            ->editColumn('id', function ($payment) {
                return $payment->id;
            })
            ->editColumn('date', function ($payment) {
                return $payment->date;
            }) 
            ->editColumn('amount', function ($payment) {
                return $payment->amount;
            })
           
            ->editColumn('notes', function ($payment) {
                return $payment->note;
            })
                
            ->make(true);
    }

    public function showcompletepayment(){
         $listofseller=User::where("user_type",'3')->where("is_active",'1')->get();
         $lang = Lang_core::all();
         return view("admin.payment.completedefault")->with("listofseller",$listofseller)->with("lang",$lang);
    }
  
    public function pendingpaymentdatatable(){
        $cat = Order::orderBy('seller_id', 'desc')
                ->groupBy('seller_id')
                ->whereNotIn("status",array(7,6))
                ->whereMonth("orderplace_datetime",'>','1')
                ->get();
                // date("m",strtotime('-1 months'));
       // echo "<pre>";print_r($category);exit;
        $data=array();
        foreach ($cat as $k) {
            $paymentlast = Paymentrecord::where("seller_id",$k->seller_id)->whereMonth("date",">",date("m","1"))->first();
            if(empty($paymentlast)){
                $total=0;
               $current=0;
               if(date('d')>15){
                     $current=$this->getlastmonthpayment($k->seller_id);
                }
                $total = $this->getpendingpayment($k->seller_id)+$current;
                $k->total=$total;
                if($total == 0){
                    unset($k);
                }else{
                    $data[]=$k;
                }
            }else{
                unset($k);
            }
               
               
        }
        
        return DataTables::of($data)
            ->editColumn('id', function ($data) {
                return $data->seller_id;
            })
            ->editColumn('brand_name', function ($data) {
                $data=User::find($data->seller_id);
                if($data){
                    return $data->first_name;
                }else{
                    return '';
                }
            })
            ->editColumn('pending_pay', function ($data) {
                   return $this->getpendingpayment($data->seller_id);
            })
            ->editColumn('current_pay', function ($data) {
                if(date('d')>15){
                    return number_format($this->getlastmonthpayment($data->seller_id),2,'.','');
                }else{
                    return 0;
                }
            }) 
            ->editColumn('total_pay', function ($data) {
                $current=0;
               if(date('d')>15){
                     $current=$this->getlastmonthpayment($data->seller_id);
                }
                $total = $this->getpendingpayment($data->seller_id)+$current;
                return number_format($total,2,'.','');
            })         
            ->editColumn('action', function ($data) {
                $current=0;
                if(date('d')>15){
                     $current=$this->getlastmonthpayment($data->seller_id);
                }
                $total= $this->getpendingpayment($data->seller_id)+$current;
                 $payurl=url('admin/payamount',array('seller_id'=>$data->seller_id));
                if($total!=0){ //add payment option
                    return '<a href="'.$payurl.'" class=" btn btn-success" style="color:white !important" >'.__("messages.pay amount").'</a>';
                }else if($this->getpendingpayment($data->seller_id)!=0){
                    $total=$this->getpendingpayment($data->seller_id);
                    return '<a href="'.$payurl.'" class=" btn btn-success" style="color:white !important" >'.__("messages.pay amount").'</a>';
                }else{
                }                  
            })           
            ->make(true);
    }

    public function savechequepayment(Request $request){
       
            $storepayment = new Paymentrecord();
            $storepayment->date = date('Y-m-d');
            $storepayment->seller_id = $request->get("seller_id");
            $storepayment->payment_type =  'cheque';
            $storepayment->amount = $request->get("amount");
            $storepayment->dd_no = '';
            $storepayment->status = 1;
            $storepayment->note=$request->get("payment_note");
            $storepayment->save();
            $user=Sentinel::getuser();
            $message = array("id"=>$storepayment->id,"data" => "Payment Release By Admin");
            Notifyuser::generate($user->id,$request->get("seller_id"),'Payment','1',$message,"seller_payment_release");
            Session::flash('message',__('messages.Payment Send Successfully')); 
            Session::flash('alert-class', 'alert-success');
            return redirect('admin/pendingpayment');
       
    }

    public function showpaymentform($seller_id){
        $current=100;
        $user=User::find($seller_id);
        $lang = Lang_core::all();
        $paymentmethod=PaymentMethod::find(1);
        if($user){
                if(date('d')>15){
                    $current=$this->getlastmonthpayment($seller_id);
                }
                $total= $this->getpendingpayment($seller_id)+$current;
                if($total>0){
                    return view("admin.payment.addpayment")->with("amount",$total)->with("seller_id",$seller_id)->with("seller_info",$user)->with("paymentmethod",$paymentmethod)->with("lang",$lang);
                }
                else{
                    Session::flash('message',__('messages.No Pending Payment For Seller').' '.$user->brand_name); 
                    Session::flash('alert-class', 'alert-danger');
                    return redirect()->back();
                }
        }else{
            Session::flash('message',__('messages.Seller Not Found')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function getlastmonthpayment($seller_id){
            $lastmonth = date('m', strtotime('-1 months'));
            $total=array();
            $data = Order::where('seller_id',$seller_id)->whereNotIn("status",array(6,7))->whereMonth("orderplace_datetime",$lastmonth)->get();
                
                if(count($data)>0){
                    foreach ($data as $k) {
                        $da = 0;
                        $da = $k->total;
                        $total[]=$da;
                    }
                }
            return array_sum($total);
    }

    public function getpendingpayment($seller_id){
        $getlastmonth=Paymentrecord::where("seller_id",$seller_id)->get()->last();           
        if($getlastmonth){
                $lastpaidmonth = date('m',strtotime($getlastmonth->date));
                $lastpaymentmonth = date('m', strtotime('-3 months'));
                $lastmonth = date('m', strtotime('-1 months'));
                if($lastpaidmonth==$lastpaymentmonth){
                    return 3;
                }
                else{
                       /* $data = Order::where('seller_id',$seller_id)->where("status","!=",2)->where("status","!=",4)->whereMonth("pending_datetime","<=",$lastmonth)->where("pending_datetime",">=",$lastpaidmonth)->get();*/
                        $data = Order::where('seller_id',$seller_id)->where("status","!=",2)->where("status","!=",4)->get();
                        $total=array();
                                if(count($data)>0){
                                    foreach ($data as $k) {
                                         $da = 0;
                                         $da = $k->total;
                                         $total[]=$da;
                                    }
                                }
                                return array_sum($total);
                }
        }
        return 0;
    }

    public function completepaymentdatatable(){
        $category =Paymentrecord::orderBy('seller_id', 'desc')->get();
        
        return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('brand_name', function ($category) {
                $data=User::find($category->seller_id);
                if($data){
                    return $data->first_name;
                }else{
                    return '';
                }
            }) 
            ->editColumn('product_amount', function ($category) {
                return $category->amount;
            })
            ->editColumn('status', function ($category) {
                if($category->status==0){
                    return __('messages.pending');
                }elseif($category->status==1){
                    return __('messages.approve');
                }else{
                    return __('messages.cancel');
                }
            })         
            ->editColumn('action', function ($category) {
                if($category->status==0){
                    return '<select class="form-control" name="change_pay" id="change_pay" onchange="changepaymentstatus(this.value,'.$category->id.')"><option value="">'.__("messages.Select").'</option><option value="1">'.__('messages.approve').'</option><option value="2">'.__("messages.cancel").'</option></select>';
                }         
            })           
            ->make(true);
    }

    public function changepaymentstatus($status,$id){
        $data=Paymentrecord::find($id);
        if($data){
             $data->status=$status;
             $data->save();
             $msg="";
             if($status==1){
                $msg = __('messages.Payment Approve Successfully');
             }
             if($status==3){
                 $data->delete();
                $msg = __('messages.Payment Cancel Successfully');
             }
            Session::flash('message',$msg); 
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
        }else{
            Session::flash('message',__('messages.Something Wrong')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

     public function show_pay_razorpay(Request $request){
        $setting=Setting::find(1);
        $payment_model = PaymentMethod::find(4);
        $shipping=Shipping::all();
        $cartCollection = Cart::getContent();
        $gettimezone=self::gettimezonename($setting->default_timezone);
        date_default_timezone_set($gettimezone);
        $store=Order::find($request->get("id"));
        $store->shipping_method=$request->get("shipping_method");
        $store->payment_method=$request->get("payment_method");
        $store->user_address_id=$request->get("user_address_id");
        $store->delivery_time=$request->get("delivery_time");
        $store->delivery_date=$request->get("delivery_date");
        $store->is_completed = '0';
        $store->notes = $request->get("notes");
        $store->save();
        $amount = $store->total;
     
         return view("razorpay")->with("data",$store)->with("paymentdetail",$payment_model)->with("amount",$amount);
    }
     public static function gettimezonename($timezone_id){
              $getall=self::generate_timezone_list();
              foreach ($getall as $k=>$val) {
                 if($k==$timezone_id){
                     return $val;
                 }
              }
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
       public function razor_payment(Request $request){
   
       $store=Order::find($request->get("id"));
       $amount = $store->total;
       $payment_model = PaymentMethod::find(4);
      
         
         $input = $request->all();        
           $api = new Api($payment_model->payment_key,$payment_model->payment_secret);
           $payment = $api->payment->fetch($request->get('razorpay_payment_id'));
           
           if($request->get('razorpay_payment_id')) 
           {
                
              
                   $response = $api->payment->fetch($request->get('razorpay_payment_id'))->capture(array('amount'=>(int)$amount*100)); 
                   
                     $data=Order::find($store->id);
                     
                     $data->charges_id=$request->get('razorpay_payment_id');
                     $data->is_completed = '1';
                     $data->save();

                     return redirect()->route('payment-success');
                  
                     
           }
       else{
            return redirect()->route('payment-failed');
       }
      
   }

    public function show_paystack_payment(Request $request){     
        $data=Order::find($request->get("id"));

        $amount = (int)$data->total*100;
        $payment_model = PaymentMethod::find(5);
         
       
        $curl = curl_init();
          $email = 'admin@gmail.com';
         // $amount = (int)$data->consultation_fees; 
          $callback_url = route('paystackcallback');
          curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
              'amount'=>$amount,
              'email'=>$email,
              'callback_url' => $callback_url
            ]),
            CURLOPT_HTTPHEADER => [
              "authorization: Bearer ".$payment_model->payment_secret."", 
              "content-type: application/json",
              "cache-control: no-cache"
            ],
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);
          if($err){
            die('Curl returned error: ' . $err);
          }
            $tranx = json_decode($response, true);   
            
            if($tranx['data']['reference']){
                $data->shipping_method=$request->get("shipping_method");

                $data->payment_method=$request->get("payment_method");
                $data->user_address_id=$request->get("user_address_id");
                $data->delivery_time=$request->get("delivery_time");
                $data->delivery_date=$request->get("delivery_date");
                $data->is_completed = '0';
                $data->notes = $request->get("notes");
                $data->payment_method="5";           
                $data->charges_id=$tranx['data']['reference'];
                $data->save();  
                }else{
                die('something getting worng');
            }
           
             if(!$tranx['status']){
               print_r('API returned error: ' . $tranx['message']);
             }
             return Redirect($tranx['data']['authorization_url']);
    }
    public function paystackcallback(Request $request){      
       $payment_model = PaymentMethod::find(5);
       $curl = curl_init();
        $reference = $request->get("reference");
        if(!$reference){
          die('No reference supplied');
        }
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$payment_model->payment_secret."", 
            "cache-control: no-cache"
          ],
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        if($err){
         return redirect()->route('payment-failed');
        }
        $tranx = json_decode($response);
        if(!$tranx->status){
         return redirect()->route('payment-failed');
        }
        if('success' == $tranx->data->status){
            $data = Order::where("charges_id",$reference)->first();
            
            $data->is_completed='1';
            $data->save();
            return redirect()->route('payment-success');
        }else{ //fail
            return redirect()->route('payment-failed');
        }
    }
    
    public function show_braintree_payment(Request $request){
        
        $payment_model = PaymentMethod::all();
        $data=Order::find($request->get("id"));
        $amount = $data->total;

        $update = Order::where('id',$data->id)->update(array(
                         'payment_method'=>4,));
        $token = "";
        $gateway = new \Braintree\Gateway([
              'environment' => env('BRAINTREE_ENV'),
               'merchantId' => env('BRAINTREE_MERCHANT_ID'),
               'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
               'privateKey' => env('BRAINTREE_PRIVATE_KEY')
         ]);
         $token=$gateway->ClientToken()->generate();
         
         return view("braintree")->with("data",$data)->with("paymentdetail",$payment_model)->with("braintree_token",$token)->with("amount",$amount);
    }
    
    public function save_braintree(Request $request){
        $gateway = new \Braintree\Gateway([
                   'environment' => env('BRAINTREE_ENV'),
                   'merchantId' => env('BRAINTREE_MERCHANT_ID'),
                   'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
                   'privateKey' => env('BRAINTREE_PRIVATE_KEY')
        ]);
        $amount = $request->get("amount");
        $nonce = $request->get("payment_method_nonce");

        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
            'options' => [
                'submitForSettlement' => true
            ]
        ]);
       //  echo "<pre>";print_r($result);exit;
        if ($result->success) 
        {
          $transaction = $result->transaction;
        
          $web_user = Session::get('is_where');
         
           if($web_user == 1){
              return redirect()->route('payment-success-web');
           }else{
              return redirect()->route('payment-success');
           }
        } else {
            $errorString = "";

            foreach($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }

            return redirect()->route('payment-failed');
        }    
    }
    
    public function show_rave_payment(Request $request){
        $payment_model = PaymentMethod::find(6);
        $sec_key=$payment_model->payment_secret;
        $data=Order::find($request->get("id"));
        $amount = $data->total;
        $userinfo=User::where('id',$data->user_id)->first();
        $callback_url = route('rave-callback');
        //* Prepare our rave request
        $request = [
            'tx_ref' => time(),
            'amount' => $amount,
            'currency' => 'NGN',
            'payment_options' => 'card',
            'redirect_url' => $callback_url,
            'customer' => [
                'email' => $userinfo->email,
                'name' => $userinfo->first_name
            ],
            'meta' => [
                'price' => $amount
            ],
            'customizations' => [
                'title' => 'Paying for a sample product',
                'description' => 'sample'
            ]
        ];

        //* Ca;; f;iterwave emdpoint
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($request),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer FLWSECK_TEST-758036eb34ab4bf13606f2e05b8ebdc3-X',
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $res = json_decode($response);
        //echo "<pre>";print_r($res);die();
        if($res->status == 'success')
        {
          $link = $res->data->link;
          //return route($link);
            return redirect($link);
        }
        else
        {
            return redirect()->route('payment-failed');
        }
    }


    public function rave_callback(Request $request){
        if($_GET['status'] == 'cancelled')
        {
            echo 'YOu cancel the payment';
            die();
        }
        elseif($_GET['status'] == 'successful')
        {
            $txid = $_GET['transaction_id'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$txid}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                  "Content-Type: application/json",
                  "Authorization: Bearer FLWSECK_TEST-758036eb34ab4bf13606f2e05b8ebdc3-X"
                ),
              ));
              
              $response = curl_exec($curl);
              
              curl_close($curl);
              
              $res = json_decode($response);
              
              if($res->status)
              {
                $amountPaid = $res->data->charged_amount;
                $amountToPay = $res->data->meta->price;
                if($amountPaid >= $amountToPay)
                {
                    return redirect()->route('payment-success');
                    //* Continue to give item to the user
                }
                else
                {
                    return redirect()->route('payment-failed');
                }
              }
              else
              {
                  return redirect()->route('payment-failed');
              }
        }
    }
    public function store_paytm_data(Request $request){
      $data1 = PaymentMethod::all();
      $arr = array(); 
      
       $data=Order::find($request->get("id"));
       $amount = $data->total;
       $payment = PaytmWallet::with('receive');

            $payment->prepare([
          'order' => $request->get('id'),
          'user' => 'redixbit',
          'mobile_number' => '9904444091',
          'email' => 'redixbit.user10@gmail.com',
          'amount' => $amount,
          'callback_url' => route('paytmstatus')
        ]);   


        
        return $payment->receive();
    }

    public function paymentpaytmCallback(Request $request){
        $transaction = PaytmWallet::with('receive');
        $response = $transaction->response();
        $order_id = $transaction->getOrderId();
        
        if($transaction->isSuccessful()){
             $data=Order::find($order_id);
             $data->is_completed='1';
             $data->save();
            return redirect()->route('payment-success');
        }else if($transaction->isFailed()){
            return redirect()->route('payment-failed');
        }
    }
    public function payment_success(){
       return view('payment_success');
    }

    public function payment_success_web(){
       return view('payment_success_web');
    }
    
    public function payment_failed(){
        return view('payment_failed');
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
                      $add->total_amount=$k["ProductTotal"]-$k["exterdata"]["couponprice"]+$k["tax_amount"]+$totalcharge;
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
}