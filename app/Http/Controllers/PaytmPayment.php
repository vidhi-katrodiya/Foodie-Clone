<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PaytmWallet;
class PaytmPayment extends Controller
{
    public function order()
    {
        $payment = PaytmWallet::with('receive');
        $payment->prepare([
          'order' => "1111",
          'user' => "11",
          'mobile_number' => "9904444091",
          'email' => "redixbit.user10@gmail.com",
          'amount' => "1000",
          'callback_url' => route('paytmstatus')
        ]);
        return $payment->receive();
    }

    /**
     * Obtain the payment information.
     *v
     * @return Object
     */
    public function paymentCallback()
    {
        $transaction = PaytmWallet::with('receive');
        
        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm
        
        if($transaction->isSuccessful()){
          //Transaction Successful
        }else if($transaction->isFailed()){
          //Transaction Failed
        }else if($transaction->isOpen()){
          //Transaction Open/Processing
        }
        $transaction->getResponseMessage(); //Get Response Message If Available
        //get important parameters via public methods
        $transaction->getOrderId(); // Get order id
        $transaction->getTransactionId(); // Get transaction id
    }    
}
