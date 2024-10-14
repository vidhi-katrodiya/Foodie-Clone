<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Crypt;
use Sentinel;
use Session;
use DataTables;
use Redirect;
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
use App\Models\OrderDataApp;
use App\Models\User;
use URL;
use Mail;
use Image;
use PDF;
use Hash;
use DateTimeZone;
use DateTime;
use DB;
use Cart;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use Auth;

class PayPaypalController extends Controller
{
  
  private $_api_context;

      public function __construct()
      {
        parent::callschedule();
        $setting=PaymentMethod::find(1);
        $paypal_conf = \Config::get('paypal');
        if($setting->payment_mode==1){
           $mode="sandbox";
        }
        else{
          $mode="live";
        }
        $this->_api_context = new ApiContext(new OAuthTokenCredential($setting->payment_key,$setting->payment_secret));
        $this->_api_context->setConfig(array('mode' =>$mode,'http.ConnectionTimeOut' => 1000,'log.LogEnabled' => true,'log.FileName' => storage_path() . '/logs/paypal.log','log.LogLevel' => 'FINE'));
    }

    public function payWithpaypal(Request $request)
    {
        // dd($request->get("dec_id"));
        $dec_id=$request->get("dec_id");
     	 $id = $this->decyptstring($request->get("dec_id"));
     	$data=OrderDataApp::find($id);
     	$store=Order::find($data->order_id);
     	/* echo "<pre>";
     	print_r($store->total);
     	die();*/ 
        $setting=Setting::find(1);
        $cartCollection = Cart::getContent();
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();

        $item_1->setName(__('messages.site_name')) 
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice(number_format($store->total, 2, '.', ''));

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal(number_format($store->total, 2, '.', ''));

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription(__('messages.Your transaction description'));

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('getStatus'))
            ->setCancelUrl(URL::route('getStatus'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        try {
               $payment->create($this->_api_context);
           
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {                
                Session::flash('message', __('messages_error_success.connection_timeout')); 
                Session::flash('alert-class', 'alert-danger');
                return Redirect::route('checkout');
              
            } else {
                Session::flash('message', __('messages_error_success.paypal_error_1')); 
                Session::flash('alert-class', 'alert-danger');
                return Redirect::route('checkout');
                
            }
        }
        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();
            }
        }

        Session::put('paypal_payment_id', $payment->getId());

        if(isset($redirect_url)) 
        {
        	$data->payment_id = $payment->getId();
        	$data->save();
        	// print_r($payment->getId());
        	// die("ASS");
            return Redirect::away($redirect_url);
        }

    }

    public function getStatus(Request $request)
    {

        $payment_id = Session::get('paypal_payment_id');
        if (empty($request->get('PayerID')) || empty($request->get('token'))) {
           // fail
        	// die("FFAA");
        	return redirect('payment_fail');
        }
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID'));
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') 
        { 
        	$order_app=OrderDataApp::where("payment_id",$payment_id)->first();
        	

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
		      CURLOPT_POSTFIELDS => array('id'=>$order_app->order_id,'payment_method' =>$order_app->payment_method,'user_address_id' =>$order_app->user_address_id,'lang' =>$order_app->lang,'delivery_time' => $order_app->delivery_time,'delivery_date'=> $order_app->delivery_date,'notes'=> $order_app->notes,'payment_type'=> 'Paypal','pay_pal_paymentId'=> $payment_id),
		    ));

        	$response = curl_exec($curl);
		    $err = curl_error($curl);
		    curl_close($curl);

		    if ($err) {
		      echo "cURL Error #:" . $err;
		    } 
		    else
		    {
		    
		      $data2 = json_decode($response,true);
		      if($data2['data']['status'] == 0)
		      {
		        return redirect('payment_fail');
		      }
		      if($data2['data']['status'] == 1)
		      {
		        return redirect('payment_success');
		      }
		  }
        }
        /*die("FF");
         $order=Order::where("paypal_payment_Id",$payment_id)->first();
         if($order){
             $order->delete();
         }
        Session::flash('message', __('messages_error_success.payment_fail')); 
        Session::flash('alert-class', 'alert-danger');
        return Redirect::route('checkout');*/
    }

  
}
