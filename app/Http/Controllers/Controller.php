<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\CronSchedule;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\FileMeta;
use Twilio\Rest\Client;
use Artisan;
use Auth;
use Cart;
use Exception;
use Illuminate\Support\Facades\Crypt;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function callschedule(){
        $getdata=CronSchedule::where("date",date("Y-m-d"))->first();
        if(empty($getdata)){
        	$storedone=$this->productupdate();
        	if($storedone==1){
        	    $store=CronSchedule::find(1);
            	$store->date=date("Y-m-d");
            	$store->save();
            	return "change";
        	}
        	
        }
       
        return "okay";
    }

    public function encryptstring($val)
    {
      	return Crypt::encryptString($val);
    }

    public function decyptstring($val)
    {
      	return Crypt::decryptString($val);
    }

    public function file_meta_update_payment_key($model_id,$lang,$meta_key,$meta_value,$model_name){
      	$data = Filemeta::where("model_id",$model_id)->where("lang",$lang)->where("meta_key",$meta_key)->where("model_name",$model_name)->first();
      	if($data){
	        $data->meta_value = $meta_value;
	        $data->save();
      	}else{
	        $data = new Filemeta();
	        $data->model_id = $model_id;
	        $data->lang = $lang;
	        $data->meta_key = $meta_key;
	        $data->meta_value = $meta_value;
	        $data->model_name = $model_name;
	        $data->save();
      	}
      	return 1;
    }

    public function sendotpmsg($receiverNumber,$message){
        $account_sid = 'AC00b51901c065bd49f9941d0681d7dbbf';
        $auth_token = '70b53f32b511ffb818d99a404721c9c7';

        $twilio_number = '+18503441856';
        try{
            $client = new Client($account_sid, $auth_token);
            $client->messages->create(
                '+'.$receiverNumber,
                array(
                    'from' => $twilio_number,
                    'body' => $message
                )
            );
            return 1;
        }catch(exception $e){
              return 0;
        }
    }

    public function productupdate(){
      
        $product=Product::all();
        foreach ($product as $k) {
            if($k->special_price!=""){
                $today = date('Y-m-d');
                $start_date = date('Y-m-d', strtotime($k->special_price_start));
                $end_date = date('Y-m-d', strtotime($k->special_price_to));
                if($today>=$start_date&&$today<=$end_date){
                    $k->selling_price=number_format((float)$k->special_price, 2, '.', '');
                    $dis_price=(int)($k->MRP)-(int)($k->special_price);
                    $disper=0;
                    if($dis_price!=0&&$dis_price>0){
                        $disper=((int)$dis_price/(int)$k->MRP)*100;
                    }
                    $k->discount=(int)floor($disper);
            	}else{

                    $dis_price=(int)($k->MRP)-(int)($k->price);
                    $disper=0;
                    if($dis_price!=0&&$dis_price>0){
                            $disper=((int)$dis_price/(int)$k->MRP)*100;
                    }
                    $k->discount=(int)floor($disper);
                    $k->selling_price=number_format((float)$k->price, 2, '.', '');
                }
            	$k->save();
            }
            else
            {
                $dis_price=(int)($k->MRP)-(int)($k->price);
                $disper=0;
                if($dis_price!=0&&$dis_price>0){
                        $disper=((int)$dis_price/(int)$k->MRP)*100;
                }
                $k->discount=(int)floor($disper);
                $k->selling_price=number_format((float)$k->price, 2, '.', '');  
                $k->save();
            }          
         }
        return 1;
    }

    public function verifiedcoupon($coupon_code){
        $date=date("Y-m-d");
        $data=Coupon::where("code",$coupon_code)->where("status",'1')->first();
        if(!$data){
            return 0;
        }
        else
        {
            $start_date=date("Y-m-d",strtotime($data->start_date)); 
            $end_date=date("Y-m-d",strtotime($data->end_date));
            if(($date>=$start_date)&&($date<=$end_date)){
	            $order=Order::where("coupon_code",$coupon_code)->get();
	            $orderuser=Order::where("coupon_code",$coupon_code)->where("user_id",Auth::id())->get();
	            if($data->usage_limit_per_coupon!=""&&($data->usage_limit_per_coupon<count($order))){
	                return 0;
	            }
        		elseif($data->usage_limit_per_customer!=""&&($data->usage_limit_per_customer<=count($orderuser)))
	            {
	                return 0;
	            }
	            elseif($data->minmum_spend!=""&&$data->minmum_spend>=Cart::getTotal())
	            {
	                return 0;
	            }
	            elseif($data->maximum_spend!=""&&$data->maximum_spend<=Cart::getTotal())
	            {
	                return 0;
	            }
	            else
	            {
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
	                else
	                {
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
	        else
	        {
	           return 0;
	        }
	    }    
   	}
  
}
