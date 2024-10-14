<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Paymentrecord;
use App\Models\OrderData;
use App\Models\DeliveryBoy;
use Mail;
use DateTimeZone;
use DateTime;

class Notifyuser extends Model
{
    use HasFactory;
    protected $table = 'notification';
    protected $primaryKey = 'id';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'sender_type',
        'activity',
        'message',
        'web',
        'email',
        'read'
    ];

    public static function generate($sender_id, $receiver_id,$message_hint, $sender_type, $message , $email_type){
        $setting = Setting::find(1);
        $gettimezone=self::gettimezonename($setting->default_timezone);
        date_default_timezone_set($gettimezone);
    	$store = new Notifyuser();
    	$store->sender_id = $sender_id;
    	$store->receiver_id = $receiver_id;
    	$store->sender_type = $sender_type;
    	$store->activity = $message_hint;
        $store->date = date('Y-m-d h:i:s');
    	$store->message = $message['data'];
    	$store->web = '1';
    	$store->email = '1';    	
    	$store->save();	
        if($setting->send_mail=='1'){
            switch ($email_type) {
                case 'admin_product_approve':
                    return self::admin_product_approve($sender_id,$receiver_id,$message);
                    break;
                case 'seller_payment_release':
                    return self::seller_payment_release($receiver_id,$message);
                    break;
                case 'new_order_create':
                    return self::new_order_create($receiver_id,$message);
                    break;
                case 'product_status_change':
                    return self::product_status_change($receiver_id,$message);
                    break;
                case 'order_status_change':
                    return self::order_status_change($receiver_id,$message,$sender_type);
                    break;                        
                default:
                    break;
             } 
        }
    	return 1;
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

   	public static function gettimezonename($timezone_id){
      	$getall=self::generate_timezone_list();
      	foreach ($getall as $k=>$val) {
         	if($k==$timezone_id){
             	return $val;
         	}
      	}
   	}

    public static function admin_product_approve($sender_id,$receiver_id,$message){
        $receiver = User::find($receiver_id);
        $sender=User::find($sender_id);
        $data=array();
        $data['email'] =$receiver->email;
        $data['receiver_name'] = $receiver->name;
        $data['seller_name'] = $sender->brand_name;
        $data['product_name'] = isset(Product::find($message['id'])->name)?Product::find($message['id'])->name:"";
        try {
                Mail::send('email.productapprove', ['user' => $data], function($message) use ($data){
                            $message->to($data['email'],$data['receiver_name'])->subject(__('messages.site_name'));
                });
        
        } catch (\Exception $e) {}
    }

    public static function seller_payment_release($receiver_id,$message){
        $receiver = User::find($receiver_id);
        $paymentinfo=Paymentrecord::find($message['id']);
        $data=array();
        $data['email'] ='redixbit.user10@gmail.com';//$receiver->email;
        $data['receiver_name'] = $receiver->name;
        $data['payment_type'] = $paymentinfo->payment_type;
        $data['payment_amount'] = $paymentinfo->amount;
        $data['payment_date'] = $paymentinfo->date;
        $data['payment_note'] = $paymentinfo->note;
        try {
                Mail::send('email.sellerpayment', ['user' => $data], function($message) use ($data){
                        $message->to($data['email'],$data['receiver_name'])->subject(__('messages.site_name'));
                });
                
        } catch (\Exception $e) {}
    }

    public static function new_order_create($receiver_id,$message){
        $receiver = User::find($receiver_id);
        $orderinfo=OrderData::find($message['id']);
        $data=array();
        $data['email'] ='redixbit.user10@gmail.com';//$receiver->email;
        $data['orderid']=$orderinfo->order_no;
        $data['receiver_name'] = $receiver->first_name;
        try {
                Mail::send('email.new_order', ['user' => $data], function($message) use ($data){
                        $message->to($data['email'],$data['receiver_name'])->subject(__('messages.site_name'));
                });
                
        } catch (\Exception $e) {}
    }

    public static function product_status_change($receiver_id,$message){
        $receiver = User::find($receiver_id);
        $productinfo=Product::find($message['id']);
        $data=array();
        $data['email'] ='redixbit.user10@gmail.com';//$receiver->email;
        $data['receiver_name'] = $receiver->name;
        $data['product_name']=$productinfo->name;
        $data['message']=$message['data'];
        try {
                Mail::send('email.product_approve', ['user' => $data], function($message) use ($data){
                        $message->to($data['email'],$data['receiver_name'])->subject(__('messages.site_name'));
                });
                
        } catch (\Exception $e) {}
    }

    public static function order_status_change($receiver_id,$message,$sender_type){
        if($sender_type==4){
            $receiver = DeliveryBoy::find($receiver_id);
        }else{
            $receiver = User::find($receiver_id);
        }
        $orderinfo=OrderData::find($message['id']);
        $data=array();
        $data['email'] ='redixbit.user10@gmail.com';//$receiver->email;
        $data['receiver_name'] = $receiver->name;
        $data['order_no']=$orderinfo->order_no;
        $data['message']=$message['data'];
        try {
                Mail::send('email.order_status_change', ['user' => $data], function($message) use ($data){
                        $message->to($data['email'],$data['receiver_name'])->subject(__('messages.site_name'));
                });
                
        } catch (\Exception $e) {}

    }
}
