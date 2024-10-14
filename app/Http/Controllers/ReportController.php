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
use App\Models\Setting;
use App\Models\Product;
use App\Models\Taxes;
use App\Models\Shipping;
use App\Models\OrderData;
use App\Models\FileMeta;
use App\Models\Lang_core;
use App\Models\User;
use DateTime;
use Image;
use PDF;
use Hash;
use DB;
class ReportController extends Controller {
    
    public function __construct(){
        parent::callschedule();
    }

    public  function index(){
   	    $lang = Lang_core::all();
        return view("admin.report")->with("lang",$lang);
    } 

   	public function couponreport($start_date,$end_date,$order_status,$code){
		if($start_date!="abc"&&$end_date!="abc"){
		   	    	$sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d');
		   	    	$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d');
   	    }
   	  	if($start_date=="abc"&&$order_status=="0"&&$code=="0"){

	   	  	$coupon_ser =Coupon::where('is_deleted','0')->get();
	            return DataTables::of($coupon_ser)		            
	            ->editColumn('date', function ($coupon_ser) {
	            	return $coupon_ser->id;
	                
	            })  
	            ->editColumn('code', function ($coupon_ser) {
	                return $coupon_ser->code;
	            })
	            ->editColumn('name', function ($coupon_ser) {
	                  return $coupon_ser->name;
	            })  
	            ->editColumn('order', function ($coupon_ser) {
	            	$data=Order::where("coupon_code",$coupon_ser->code)->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get();
	                return count($data);
	            })
	            ->editColumn('total', function ($coupon_ser) {
	               		$data=Order::where("coupon_code",$coupon_ser->code)->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get();
		                $total=0;
				        foreach ($data as $k) {
				        	$total=(float)$total+(float)$k->coupon_price;
				        }
				        return $total;
	            })       
	            ->make(true);
   	  	}
   	  	else
   	  	{
   	  		$date_duration=date('d-m-Y',strtotime($sdate)).' to '.date('d-m-Y',strtotime($edate));
   	  		
   	  	   	if($start_date!="abc"&&$order_status=="0"&&$code=="0")
   	  	  	{
	            $data=Order::whereBetween('created_at',[$sdate, $edate])->WhereNotNull('coupon_code')->groupBY('coupon_code')->get();

	            $coupon_ob=array();
	            foreach ($data as $value) {
	            	$coupon=Coupon::where('code',$value->coupon_code)->where('is_deleted','0')->first();

	            	if($coupon){
	            		 $c_data=Order::whereBetween('created_at',[$sdate, $edate])->where("coupon_code",$value->coupon_code)->sum('coupon_price');
	                $count_data=Order::whereBetween('created_at',[$sdate, $edate])->where("coupon_code",$value->coupon_code)->get();
			       $count=count($count_data);

	            		$value->coupon_id=$coupon->id;
	            		$value->coupon_name=$coupon->name;
	            		$value->c_price=$c_data;
	            		$value->count_coupon_use=$count;
	                    $coupon_ob[]=$value;
	                }else{
	                    unset($value);

	                }
	            }
	            
       		}
       		 
       		if($start_date!="abc"&&$order_status!="0"&&$code=="0")
   	  	    {
	            $data=Order::whereBetween('created_at',[$sdate, $edate])->WhereNotNull('coupon_code')->groupBY('coupon_code')->where('status',$order_status)->get();

	            $coupon_ob=array();
	            foreach ($data as $value) {
	            	$coupon=Coupon::where('code',$value->coupon_code)->where('is_deleted','0')->first();

	            	if($coupon){
	            		 $c_data=Order::whereBetween('created_at',[$sdate, $edate])->where("coupon_code",$value->coupon_code)->where('status',$order_status)->sum('coupon_price');
	                $count_data=Order::whereBetween('created_at',[$sdate, $edate])->where('status',$order_status)->where("coupon_code",$value->coupon_code)->get();
			       $count=count($count_data);

	            		$value->coupon_id=$coupon->id;
	            		$value->coupon_name=$coupon->name;
	            		$value->c_price=$c_data;
	            		$value->count_coupon_use=$count;
	                    $coupon_ob[]=$value;
	                }else{
	                    unset($value);

	                }
	            }
	            
       		}

       		if($start_date!="abc"&&$order_status=="0"&&$code!="0")
   	  	    {
	            $data=Order::whereBetween('created_at',[$sdate, $edate])->WhereNotNull('coupon_code')->groupBY('coupon_code')->where('coupon_code',$code)->get();

	            $coupon_ob=array();
	            foreach ($data as $value) {
	            	$coupon=Coupon::where('code',$value->coupon_code)->where('is_deleted','0')->first();

	            	if($coupon){
	            		 $c_data=Order::whereBetween('created_at',[$sdate, $edate])->where("coupon_code",$value->coupon_code)->where('coupon_code',$code)->sum('coupon_price');
	                $count_data=Order::whereBetween('created_at',[$sdate, $edate])->where('coupon_code',$code)->where("coupon_code",$value->coupon_code)->get();
			       $count=count($count_data);

	            		$value->coupon_id=$coupon->id;
	            		$value->coupon_name=$coupon->name;
	            		$value->c_price=$c_data;
	            		$value->count_coupon_use=$count;
	                    $coupon_ob[]=$value;
	                }else{
	                    unset($value);

	                }
	            }
	            
       		}

       		if($start_date!="abc"&&$order_status!="0"&&$code!="0")
   	  	    {
	            $data=Order::whereBetween('created_at',[$sdate, $edate])->WhereNotNull('coupon_code')->where('coupon_code',$code)->where('status',$order_status)->groupBY('coupon_code')->get();

	            $coupon_ob=array();
	            foreach ($data as $value) {
	            	$coupon=Coupon::where('code',$value->coupon_code)->where('is_deleted','0')->first();

	            	if($coupon){
	            		 $c_data=Order::whereBetween('created_at',[$sdate, $edate])->where("coupon_code",$value->coupon_code)->where('coupon_code',$code)->where('status',$order_status)->sum('coupon_price');
	                $count_data=Order::whereBetween('created_at',[$sdate, $edate])->where('coupon_code',$code)->where("coupon_code",$value->coupon_code)->where('status',$order_status)->get();
			       $count=count($count_data);

	            		$value->coupon_id=$coupon->id;
	            		$value->coupon_name=$coupon->name;
	            		$value->c_price=$c_data;
	            		$value->count_coupon_use=$count;
	                    $coupon_ob[]=$value;
	                }else{
	                    unset($value);

	                }
	            }
	            
       		}
            return DataTables::of($coupon_ob)
           
            ->editColumn('date', function ($coupon_ob) {
            	return $coupon_ob->coupon_id;
            })  
            ->editColumn('code', function ($coupon_ob) {
                return $coupon_ob->coupon_code;
            })
            ->editColumn('name', function ($coupon_ob) {
                return $coupon_ob->coupon_name;
            })  
            ->editColumn('order', function ($coupon_ob) {
                
		        return $coupon_ob->count_coupon_use;
            })
            ->editColumn('total', function ($coupon_ob) {
               return $coupon_ob->c_price;
            })       
            ->make(true);
   	    }
    }

    public function customerOrder($start_date,$end_date,$order_status,$name,$email){
   	    $coupon_ob=array();
   	    $date_order=array();
   	    $order_status_arr=array();
   	    $name_arr=array();
   	    $email_arr=array();
   	    $main_array=array();
   	    $sdate="";
   	    $edate="";
   	    $setting=Setting::find(1);
   	    $res_curr=explode("-",$setting->default_currency);

   	    if($start_date!="abc"&&$end_date!="abc"){
   	    	$sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d');
   	    	$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d');
   	    }

	   	if($sdate=="" && $order_status==0 && $name=="0" && $email=="0"){//0000
	   		$order_array=array();
	   		$today_date=date("d-m-Y");
	   		$user=User::where("user_type",'1')->get();
	   		$user_ids=array();
	   		foreach ($user as $k) {
	   		   $user_ids[]=$k->id;
	   		}
	   		foreach ($user_ids as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::where("user_id",$k)->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->orderby("id")->get();
	   			 if(count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(int)$total+(int)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F 01,Y')."-".date('F t,Y');
	   			 }
	   			 else{
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array["order"]=count($getallorder);
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["total"]=0;
	   			 	
	   			 	$order_array["date"]=date('F 01,Y')."-".date('F t,Y');
	   			 }
	   			 $main_array[]=$order_array;
	   			  
	   		}
	   		$coupon_ob= $main_array;
	   	}
	   	else if($sdate!=""&&$order_status=="0"&&$name=="0"&&$email=="0"){//1000
	   	
	   		$user_ids=array();
	   		$da1=Order::whereBetween("created_at",[$sdate, $edate])->get();
	   		foreach ($da1 as $k) {
	   			$user_ids[]=$k->user_id;
	   		}
	   		foreach (array_values(array_unique($user_ids)) as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::whereBetween("created_at",[$sdate, $edate])->where("user_id",$k)->orderby("id")->get();
	   			 if($userdata && count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(float)$total+(float)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F d,Y',strtotime($getallorder[0]->created_at))."-".date('F d,Y',strtotime($getallorder[count($getallorder)-1]->created_at));
	   			 	 $main_array[]=$order_array;
	   			  }
	   		}
	   		$coupon_ob= $main_array;
	   	}
	   	else if($sdate!=""&&$order_status=="0"&&$name=="0"&&$email!="0"){//1001

	   		$user_ids=array();
	   		$da1=Order::whereBetween("created_at",[$sdate, $edate])->get();
	   		foreach ($da1 as $k) {
	   			$userdat=User::find($k->user_id);
	   			if($userdat){
	   			if($userdat->email==$email){
	   				$user_ids[]=$k->user_id;
	   			}}
	   		}
	   		foreach (array_values(array_unique($user_ids)) as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::whereBetween("created_at",[$sdate, $edate])->where("user_id",$k)->orderby("id")->get();
	   			 if($userdata && count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(float)$total+(float)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F d,Y',strtotime($getallorder[0]->created_at))."-".date('F d,Y',strtotime($getallorder[count($getallorder)-1]->created_at));
	   			 	 $main_array[]=$order_array;
	   			  }
	   		}
	   		$coupon_ob= $main_array;
	   	}
	   	else if($sdate!=""&&$order_status=="0"&&$name!="0"&&$email=="0"){//1010
	   		$user_ids=array();
	   		$da1=Order::whereBetween("created_at",[$sdate, $edate])->get();
	   		foreach ($da1 as $k) {
	   			$userdat=User::find($k->user_id);
	   			if($userdat){
	   			$nameu=$userdat->first_name;
	   			if($nameu==$name){
	   				$user_ids[]=$userdat->id;
	   			}
	   		   }
	   		}
	   		foreach (array_values(array_unique($user_ids)) as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::whereBetween("created_at",[$sdate, $edate])->where("user_id",$k)->orderby("id")->get();
	   			 if($userdata && count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(float)$total+(float)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F d,Y',strtotime($getallorder[0]->created_at))."-".date('F d,Y',strtotime($getallorder[count($getallorder)-1]->created_at));
	   			 	 $main_array[]=$order_array;
	   			  }
	   		}
	   		$coupon_ob= $main_array;
	   	}
	   	else if($sdate!=""&&$order_status=="0"&&$name!="0"&&$email!="0"){//1011
	   		$user_ids=array();
	   		$da1=Order::whereBetween("created_at",[$sdate, $edate])->get();
	   		foreach ($da1 as $k) {
	   			$userdat=User::find($k->user_id);
	   			if($userdat){
	   			$nameu=$userdat->first_name;
	   			if($nameu==$name&&$userdat->email==$email){
	   				$user_ids[]=$userdat->id;
	   			}}
	   		}
	   		foreach (array_values(array_unique($user_ids)) as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::whereBetween("created_at",[$sdate, $edate])->where("user_id",$k)->orderby("id")->get();
	   			 if($userdata && count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(float)$total+(float)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F d,Y',strtotime($getallorder[0]->created_at))."-".date('F d,Y',strtotime($getallorder[count($getallorder)-1]->created_at));
	   			 	 $main_array[]=$order_array;
	   			  }
	   		}
	   		$coupon_ob= $main_array;
	   	}
	   	else if($sdate!=""&&$order_status!="0"&&$name=="0"&&$email=="0"){//1100
	   		$user_ids=array();
	   		$da1=Order::whereBetween("created_at",[$sdate, $edate])->where("status",$order_status)->get();
	   		foreach ($da1 as $k) {
	   			$user_ids[]=$k->user_id;
	   		}
	   		foreach (array_values(array_unique($user_ids)) as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::whereBetween("created_at",[$sdate, $edate])->where("status",$order_status)->where("user_id",$k)->orderby("id")->get();
	   			 if($userdata && count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(float)$total+(float)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F d,Y',strtotime($getallorder[0]->created_at))."-".date('F d,Y',strtotime($getallorder[count($getallorder)-1]->created_at));
	   			 	 $main_array[]=$order_array;
	   			  }
	   		}
	   		$coupon_ob= $main_array;
	   	}
	   	else if($sdate!=""&&$order_status!="0"&&$name=="0"&&$email!="0"){//1101
	   		$user_ids=array();
	   		$da1=Order::whereBetween("created_at",[$sdate, $edate])->where("status",$order_status)->get();
	   		foreach ($da1 as $k) {
	   			$userdat=User::find($k->user_id);
	   			if($userdat){
	   			if($userdat->email==$email){
	   				$user_ids[]=$userdat->id;
	   			}}
	   		}
	   		foreach (array_values(array_unique($user_ids)) as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::whereBetween("created_at",[$sdate, $edate])->where("status",$order_status)->where("user_id",$k)->orderby("id")->get();
	   			 if($userdata && count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(float)$total+(float)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F d,Y',strtotime($getallorder[0]->created_at))."-".date('F d,Y',strtotime($getallorder[count($getallorder)-1]->created_at));
	   			 	 $main_array[]=$order_array;
	   			  }
	   		}
	   		$coupon_ob= $main_array;
	   	}
	   	else if($sdate!=""&&$order_status!="0"&&$name!="0"&&$email=="0"){//1110
	   		$user_ids=array();
	   		$da1=Order::whereBetween("created_at",[$sdate, $edate])->where("status",$order_status)->get();
	   		foreach ($da1 as $k) {
	   			$userdat=User::find($k->user_id);
	   			$nameu=$userdat->first_name;
	   			if($nameu==$name){
	   				$user_ids[]=$userdat->id;
	   			}
	   		}
	   		foreach (array_values(array_unique($user_ids)) as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::whereDate("orderplace_datetime",">=",$sdate)->whereDate("orderplace_datetime","<=",$edate)->where("status",$order_status)->where("user_id",$k)->orderby("id")->get();
	   			 if(count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(float)$total+(float)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F d,Y',strtotime($getallorder[0]->created_at))."-".date('F d,Y',strtotime($getallorder[count($getallorder)-1]->created_at));
	   			 	 $main_array[]=$order_array;
	   			  }
	   		}
	   		$coupon_ob= $main_array;
	   	}
	   	else if($sdate!=""&&$order_status!="0"&&$name!="0"&&$email!="0"){//1111
	   		$user_ids=array();
	   		$da1=Order::whereDate("orderplace_datetime",">=",$sdate)->whereDate("orderplace_datetime","<=",$edate)->where("status",$order_status)->get();
	   		foreach ($da1 as $k) {
	   			$userdat=User::find($k->user_id);
	   			if($userdat){
	   			$nameu=$userdat->first_name;
	   			if($nameu==$name&&$userdat->email==$email){
	   				$user_ids[]=$userdat->id;
	   			}}
	   		}
	   		foreach (array_values(array_unique($user_ids)) as $k) {
	   			 $userdata=User::find($k);
	   			 $getallorder=Order::whereDate("orderplace_datetime",">=",$sdate)->whereDate("orderplace_datetime","<=",$edate)->where("status",$order_status)->where("user_id",$k)->orderby("id")->get();
	   			 if($userdata && count($getallorder)!=0){
	   			 	$order_array["name"]=$userdata->first_name;
	   			 	$order_array['email']=$userdata->email;
	   			 	$order_array["order"]=count($getallorder);
	   				$total=0;
	   			 	foreach ($getallorder as $k) {
	   			 		$total=(float)$total+(float)$k->total;
	   			 	}
	   				$order_array["total"]=$res_curr[1].$total;
	   			 	$order_array["date"]=date('F d,Y',strtotime($getallorder[0]->created_at))."-".date('F d,Y',strtotime($getallorder[count($getallorder)-1]->created_at));
	   			 	 $main_array[]=$order_array;
	   			  }
	   		}
	   		$coupon_ob= $main_array;
	   	}

        return DataTables::of($coupon_ob)
           
            ->editColumn('date', function ($coupon_ob) {
                return $coupon_ob["date"];
            })  
           
            ->editColumn('name', function ($coupon_ob) {
                return $coupon_ob["name"];
            })  
             ->editColumn('email', function ($coupon_ob) {
                return $coupon_ob["email"];
            })
            ->editColumn('order', function ($coupon_ob) {               
		        return $coupon_ob["order"];
            })
            ->editColumn('total', function ($coupon_ob) {
               return $coupon_ob["total"];
            })       
            ->make(true);
    }

    public function product_purchase_report($start_date,$end_date,$order_status,$product,$sku){
        $coupon_ser=array();
   	    $date_order=array();
   	    $order_status_arr=array();
   	    $name_arr=array();
   	    $sku_arr=array();
   	    $main_array=array();
   	    $sdate="";
   	    $edate="";
   	    $setting=Setting::find(1);
   	    $res_curr=explode("-",$setting->default_currency);
   	    if($start_date!="abc"&&$end_date!="abc"){
   	    	$sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('d-m-Y');
   	    	$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('d-m-Y');
   	    }
	   	if($sdate==""&&$order_status=="0"&& $product=="0"&&$sku=="0"){//0000	   		
	   		$product=Product::orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	  $order=OrderData::where("product_id",$pro->id)->get();
	   		 	  $date=date('F d,Y',strtotime($pro->created_at));
	   		 	  $total=0;
	   		 	  if(count($order)!=0){
	   		 	  	$date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
	   		 	  }
                  $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                  $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	  $array_ob["date"]=$date;
	   		 	  $array_ob["product"]=$pro->name;
	   		 	  $array_ob["qty"]=count($order);
	   		 	  $array_ob["sku"]=$pro->sku;
	   		 	  $main_array[]=$array_ob;
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate==""&&$order_status=="0"&&$product=="0"&&$sku!="0"){//0001
	   		 $product=Product::where("sku",$sku)->orderby("id","DESC")->get();
	   		 foreach ($product as $pro) {
	   		 	  $order=OrderData::where("product_id",$pro->id)->get();
	   		 	  $date=date('F d,Y',strtotime($pro->created_at));
	   		 	  $total=0;
	   		 	  if(count($order)!=0){
	   		 	  	$date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
	   		 	  }
	   		 	  
                 $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                  $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	  $array_ob["date"]=$date;
	   		 	  $array_ob["product"]=$pro->name;
	   		 	  $array_ob["qty"]=count($order);
	   		 	   $array_ob["sku"]=$pro->sku;
	   		 	  $main_array[]=$array_ob;
	   		 }
	   		 $coupon_ser=$main_array;
	   	}
	   	else if($sdate==""&&$order_status=="0"&&$product!="0"&&$sku=="0"){//0010
	   	   $product=Product::where("name",$product)->orderby("id","DESC")->get();
	   		 foreach ($product as $pro) {
	   		 	  $order=OrderData::where("product_id",$pro->id)->get();
	   		 	  $date=date('F d,Y',strtotime($pro->created_at));
	   		 	  $total=0;
	   		 	  if(count($order)!=0){
	   		 	  	$date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
	   		 	  }
                 $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                  $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	  $array_ob["date"]=$date;
	   		 	  $array_ob["product"]=$pro->name;
	   		 	  $array_ob["qty"]=count($order);
	   		 	  $array_ob["sku"]=$pro->sku;
	   		 	  $main_array[]=$array_ob;
	   		 }
	   		 $coupon_ser=$main_array;
	   	}
	   	else if($sdate==""&&$order_status=="0"&&$product!="0"&&$sku!="0"){//0011
	   		
  			$product=Product::where("sku",$sku)->where("name",$product)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
   		 	  	if(count($order)!=0){
   		 	  	$date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
   		 		}
				$getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	$array_ob["date"]=$date;
	   		 	$array_ob["product"]=$pro->name;
	   		 	$array_ob["qty"]=count($order);
	   		 	$array_ob["sku"]=$pro->sku;
	   		 	$main_array[]=$array_ob;
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate==""&&$order_status!="0"&&$product=="0"&&$sku=="0"){//0100
	   		$product=Product::orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();	   		 	  
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	foreach ($order as $k) {
	   		 	  		$order_chk=Order::where("id",$k->order_id)->where("order_status",$order_status)->get();
	   		 	  		if(count($order_chk)!=0){
	   		 	  			$total=$total+1;
	   		 	    	}
   		 			}
	   		 	  	$getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                 	$pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	  	if($total!=0){
	   		 	  		 $date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
		   		 	  	  $array_ob["date"]=$date;
			   		 	  $array_ob["product"]=$pro->name;
			   		 	  $array_ob["qty"]=$total;
			   		 	   $array_ob["sku"]=$pro->sku;
			   		 	  $main_array[]=$array_ob;
	   		 	  	}
	   		 	  	 
	   		 	}
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate==""&&$order_status!="0"&&$product=="0"&&$sku!="0"){//0101

	   		$product=Product::where("sku",$sku)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();	   		 	  
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	$getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	if(count($order)!=0){
	   		 	  	foreach ($order as $k) {
	   		 	  		$order_chk=Order::where("id",$k->order_id)->where("order_status",$order_status)->get();
	   		 	  		if(count($order_chk)!=0){
	   		 	  			$total=$total+1;
	   		 	  		}
	   		 	  	}
	   		 	  	if($total!=0){
	   		 	  		 $date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
		   		 	  	  $array_ob["date"]=$date;
			   		 	  $array_ob["product"]=$pro->name;
			   		 	  $array_ob["qty"]=$total;
			   		 	   $array_ob["sku"]=$pro->sku;
			   		 	  $main_array[]=$array_ob;
	   		 	  	}
	   		 	  	 
	   		 	}
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate==""&&$order_status!="0"&&$product!="0"&&$sku=="0"){//0110
	   	    $product=Product::where("name",$product)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();	   
	   		 	$getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	foreach ($order as $k) {
	   		 	  		$order_chk=Order::where("id",$k->order_id)->where("order_status",$order_status)->get();
	   		 	  		if(count($order_chk)!=0){
	   		 	  			$total=$total+1;
	   		 	  		}
	   		 	  	}
	   		 	  	if($total!=0){
	   		 	  		 $date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
		   		 	  	  $array_ob["date"]=$date;
			   		 	  $array_ob["product"]=$pro->name;
			   		 	  $array_ob["qty"]=$total;
			   		 	   $array_ob["sku"]=$pro->sku;
			   		 	  $main_array[]=$array_ob;
	   		 	  	}
	   		 	  	 
	   		 	}
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate==""&&$order_status!="0"&&$product!="0"&&$sku!="0"){//0111
	   		$product=Product::where("name",$product)->where("sku",$sku)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();	   
	   		 	$getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	foreach ($order as $k) {
	   		 	  		$order_chk=Order::where("id",$k->order_id)->where("order_status",$order_status)->get();
	   		 	  		if(count($order_chk)!=0){
	   		 	  			$total=$total+1;
	   		 	  		}
	   		 	  	}
	   		 	  	if($total!=0){
	   		 	  		 $date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
		   		 	  	  $array_ob["date"]=$date;
			   		 	  $array_ob["product"]=$pro->name;
			   		 	  $array_ob["qty"]=$total;
			   		 	   $array_ob["sku"]=$pro->sku;
			   		 	  $main_array[]=$array_ob;
	   		 	  	}
	   		 	}
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate!=""&&$order_status=="0"&&$product=="0"&&$sku=="0"){//1000
	   		$product=Product::orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		    $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	$order=OrderData::whereBetween("created_at",[$sdate, $edate])->where("product_id",$pro->id)->get();
	   		 	 $date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	$date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
	   		 	}

	   		 	$array_ob["date"]=$date;
	   		    $array_ob["product"]=$pro->name;
	   		 	$array_ob["qty"]=count($order);
	   		 	$array_ob["sku"]=$pro->sku;
	   		 	$main_array[]=$array_ob;
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate!=""&&$order_status=="0"&&$product=="0"&&$sku!="0"){//1001
	   		$product=Product::where("sku",$sku)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		    $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	$order=OrderData::whereBetween("created_at",[$sdate, $edate])->where("product_id",$pro->id)->get();
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	$date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
	   		 	}

	   		 	$array_ob["date"]=$date;
	   		 	$array_ob["product"]=$pro->name;
	   		 	$array_ob["qty"]=count($order);
	   		 	$array_ob["sku"]=$pro->sku;
	   		 	$main_array[]=$array_ob;
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate!=""&&$order_status=="0"&&$product!="0"&&$sku=="0"){//1010
	   	    $product=Product::where("name",$product)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		    $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	$order=OrderData::whereBetween("created_at",[$sdate, $edate])->where("product_id",$pro->id)->get();
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	$date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
	   		 	}

	   		 	$array_ob["date"]=$date;
	   		 	$array_ob["product"]=$pro->name;
	   		 	$array_ob["qty"]=count($order);
	   		 	$array_ob["sku"]=$pro->sku;
	   		 	$main_array[]=$array_ob;
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate!=""&&$order_status=="0"&&$product!="0"&&$sku!="0"){//1011
	   		$product=Product::where("name",$product)->where("sku",$sku)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		    $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';
	   		 	$order=OrderData::whereBetween("created_at",[$sdate, $edate])->where("product_id",$pro->id)->get();
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	$date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
	   		 	}

	   		 	$array_ob["date"]=$date;
	   		 	$array_ob["product"]=$pro->name;
	   		 	$array_ob["qty"]=count($order);
	   		 	$array_ob["sku"]=$pro->sku;
	   		 	$main_array[]=$array_ob;
	   		 }
	   		 $coupon_ser=$main_array;
	   	}
	   	else if($sdate!=""&&$order_status!="0"&&$product=="0"&&$sku=="0"){//1100
	   	    $product=Product::orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();	   		  $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';	  
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	foreach ($order as $k) {
	   		 	  		$order_chk=Order::where("id",$k->order_id)->whereBetween("created_at",[$sdate, $edate])->where("order_status",$order_status)->get();
	   		 	  		if(count($order_chk)!=0){
	   		 	  			$total=$total+1;
	   		 	  		}
	   		 	  	}
	   		 	  	if($total!=0){
	   		 	  		 $date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
		   		 	  	  $array_ob["date"]=$date;
			   		 	  $array_ob["product"]=$pro->name;
			   		 	  $array_ob["qty"]=$total;
			   		 	   $array_ob["sku"]=$pro->sku;
			   		 	  $main_array[]=$array_ob;
	   		 	  	}
	   		 	  	 
	   		 	}
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate!=""&&$order_status!="0"&&$product=="0"&&$sku!="0"){//1101
	   	    $product=Product::where("sku",$sku)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();	   	 $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';	 	  
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	foreach ($order as $k) {
	   		 	  		$order_chk=Order::where("id",$k->order_id)->whereBetween("created_at",[$sdate, $edate])->where("order_status",$order_status)->get();
	   		 	  		if(count($order_chk)!=0){
	   		 	  			$total=$total+1;
	   		 	  		}
	   		 	  	}
	   		 	  	if($total!=0){
	   		 	  		 $date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
		   		 	  	  $array_ob["date"]=$date;
			   		 	  $array_ob["product"]=$pro->name;
			   		 	  $array_ob["qty"]=$total;
			   		 	   $array_ob["sku"]=$pro->sku;
			   		 	  $main_array[]=$array_ob;
	   		 	  	}
	   		 	  	 
	   		 	}
	   		 }
	   		 $coupon_ser=$main_array;
	   	}
	   	else if($sdate!=""&&$order_status!="0"&&$product!="0"&&$sku=="0"){//1110
	   		$product=Product::where("name",$product)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();	   	 $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';	 	  
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	foreach ($order as $k) {
	   		 	  		$order_chk=Order::where("id",$k->order_id)->whereBetween("created_at",[$sdate, $edate])->where("order_status",$order_status)->get();
	   		 	  		if(count($order_chk)!=0){
	   		 	  			$total=$total+1;
	   		 	  		}
	   		 	  	}
	   		 	  	if($total!=0){
	   		 	  		 $date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
		   		 	  	  $array_ob["date"]=$date;
			   		 	  $array_ob["product"]=$pro->name;
			   		 	  $array_ob["qty"]=$total;
			   		 	   $array_ob["sku"]=$pro->sku;
			   		 	  $main_array[]=$array_ob;
	   		 	  	}
	   		 	  	 
	   		 	}
	   		}
	   		$coupon_ser=$main_array;
	   	}
	   	else if($sdate!=""&&$order_status!="0"&&$product!="0"&&$sku!="0"){//1111
	   	    $product=Product::where("name",$product)->where("sku",$sku)->orderby("id","DESC")->get();
	   		foreach ($product as $pro) {
	   		 	$order=OrderData::where("product_id",$pro->id)->get();	   	 $getlang = FileMeta::where("model_id",$pro->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                $pro->name = isset($getlang)?$getlang->meta_value:'';	 	  
	   		 	$date=date('F d,Y',strtotime($pro->created_at));
	   		 	$total=0;
	   		 	if(count($order)!=0){
	   		 	  	foreach ($order as $k) {
	   		 	  		$order_chk=Order::where("id",$k->order_id)->whereBetween("created_at",[$sdate, $edate])->where("order_status",$order_status)->get();
	   		 	  		if(count($order_chk)!=0){
	   		 	  			$total=$total+1;
	   		 	  		}
	   		 	  	}
	   		 	  	if($total!=0){
	   		 	  		 $date=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
		   		 	  	  $array_ob["date"]=$date;
			   		 	  $array_ob["product"]=$pro->name;
			   		 	  $array_ob["qty"]=$total;
			   		 	   $array_ob["sku"]=$pro->sku;
			   		 	  $main_array[]=$array_ob;
	   		 	  	}
	   		 	  	 
	   		 	}
	   		}
	   		$coupon_ser=$main_array;
	   	}

        return DataTables::of($coupon_ser)		            
        ->editColumn('date', function ($coupon_ser) {
            return $coupon_ser["date"];
        })  
        ->editColumn('product', function ($coupon_ser) {
            return $coupon_ser["product"];
        })

        ->editColumn('qty', function ($coupon_ser) {
            return $coupon_ser["qty"];
        })
        ->make(true);
	}

   	public function add_coupon_report($start_date,$end_date){
        if($start_date=="abc"&&$end_date=="abc"){
      		        $coupon_ser =Coupon::whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get();
		            return DataTables::of($coupon_ser)
		            ->editColumn('id', function ($coupon_ser) {
		                return $coupon_ser->id;
		            }) 		            
		            ->editColumn('date', function ($coupon_ser) {
		                return $coupon_ser->created_at;
		            })  
		            
		            ->editColumn('name', function ($coupon_ser) {
		                   return $coupon_ser->code;
		            })
		            ->editColumn('code', function ($coupon_ser) {
		                return $coupon_ser->name;
		            })
		            ->editColumn('rate', function ($coupon_ser) {
		                return $coupon_ser->value."%";
		            })  
		                 
		            ->make(true);
      }
      else{
				    $sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
					$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s'); 
      	            $coupon_ser =Coupon::whereBetween("created_at",[$sdate, $edate])->get();  
		            return DataTables::of($coupon_ser)	
		            ->editColumn('id', function ($coupon_ser) {
		                return $coupon_ser->id;
		            }) 		            
		            ->editColumn('date', function ($coupon_ser) {
		                return $coupon_ser->created_at;
		            })  
		            ->editColumn('code', function ($coupon_ser) {
		                return $coupon_ser->name;
		            })
		            ->editColumn('name', function ($coupon_ser) {
		                return $coupon_ser->code;
		            })
		            ->editColumn('rate', function ($coupon_ser) {
		                return $coupon_ser->value."%";
		            })  
		                 
		            ->make(true);
      }
    }

    public function add_customer_report($start_date,$end_date){
      if($start_date=="abc"&&$end_date=="abc"){
      		        $coupon_ser =User::where("user_type","1")->whereMonth('created_at',date('m'))->get();

		            return DataTables::of($coupon_ser)
		             ->editColumn('id', function ($coupon_ser) {
		                return $coupon_ser->id;
		            }) 		            
		            ->editColumn('date', function ($coupon_ser) {
		                return $coupon_ser->created_at;
		            })  
		            ->editColumn('name', function ($coupon_ser) {
		                return $coupon_ser->first_name;
		            })
		            ->editColumn('email', function ($coupon_ser) {
		                return $coupon_ser->email;
		            })
		            ->make(true);
      }
      else{
      	           $sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
				   $edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s'); 
				   $coupon_ser =User::where("user_type","1")->whereBetween("created_at",[$sdate, $edate])->get();
		            return DataTables::of($coupon_ser)	
		             ->editColumn('id', function ($coupon_ser) {
		                return $coupon_ser->id;
		            }) 	            
		            ->editColumn('date', function ($coupon_ser) {
		                return $coupon_ser->created_at;
		            })  
		            ->editColumn('name', function ($coupon_ser) {
		                return $coupon_ser->first_name;
		            })
		            ->editColumn('email', function ($coupon_ser) {
		                return $coupon_ser->email;
		            })
		            ->make(true);
      }
    }

    public function add_product_report($start_date,$end_date){
   	 	if($start_date=="abc"&&$end_date=="abc"){
      		        $coupon_ser =Product::orderby("id","DESC")->get();
		            return DataTables::of($coupon_ser)		            
		            ->editColumn('date', function ($coupon_ser) {
		                return $coupon_ser->created_at;
		            })  
		            ->editColumn('name', function ($coupon_ser) {
		                $getlang = FileMeta::where("model_id",$coupon_ser->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                  return isset($getlang)?$getlang->meta_value:'';	
		            })
		            ->make(true);
      }
      else{
      	           $sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
				   $edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s'); 
				   $coupon_ser =Product::orderby("id","DESC")->whereBetween("created_at",[$sdate, $edate])->get();
		            return DataTables::of($coupon_ser)		            
		            ->editColumn('date', function ($coupon_ser) {
		                return $coupon_ser->created_at;
		            })  
		            ->editColumn('name', function ($coupon_ser) {
		                 return $coupon_ser->name;
		            })
		            
		            ->make(true);
      }
    }

    public function tax_report($start_date,$end_date,$tax_name){
	   	$main_array=array();
	   	$taxes_ls=Taxes::all();
	   	$setting=Setting::find(1);
	   	$res_curr=explode("-",$setting->default_currency);
	    if($start_date=="abc"&&$tax_name=="0"){
       
         	$find_order=Order::whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get();

         	if(count($find_order)!=0){
         	 	  $array_ob=array();
                  $array_ob["date"]=date('F 01,Y',strtotime($find_order[0]->created_at))."-".date('F d,Y',strtotime($find_order[count($find_order)-1]->created_at));
                  $array_ob["order"]=count($find_order);
                  $total=0;
                  foreach ($find_order as $to) {
                     $total=(float)$total+(float)$to->tax;
                  }
                  $array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');
                  $main_array[]=$array_ob;
         	}
         	else{
         	 	  $array_ob=array();
                  $array_ob["date"]=date('F d,Y',strtotime($ts->created_at));
                  $array_ob["order"]=0;
                  $array_ob["total"]=0;
                  $main_array[]=$array_ob;
         	}
         
         	$coupon_ser=$main_array;
		}
	    else if($start_date!="abc"){
	    	 $sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d');
			 $edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d');
	    	 
	         	 $find_order=Order::whereBetween("created_at",[$sdate, $edate])->get();

	         	 if(count($find_order)!=0){
	         	 	  $array_ob=array();
	                  $array_ob["date"]=date('F d,Y',strtotime($find_order[0]->created_at))."-".date('F d,Y',strtotime($find_order[count($find_order)-1]->created_at));
	                 
	                  $array_ob["order"]=count($find_order);
	                  $total=0;
	                  foreach ($find_order as $to) {
	                     $total=(float)$total+(float)$to->tax;
	                  }
	                  $array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');
	                  $main_array[]=$array_ob;
	         	 }
	         	 
	         
	         $coupon_ser=$main_array;

    	}else{//both not empty
            $sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
			$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s');
         	$find_order=OrderData::where("tax_name",$tax_name)->whereBetween("created_at",[$sdate, $edate])->get();
         	if(count($find_order)!=0){
         	 	  $array_ob=array();
                  $array_ob["date"]=date('F d,Y',strtotime($find_order[0]->created_at))."-".date('F d,Y',strtotime($find_order[count($find_order)-1]->created_at));
                  $array_ob["name"]=$tax_name;
                  $array_ob["order"]=count($find_order);
                  $total=0;
                  foreach ($find_order as $to) {
                     $total=(float)$total+(float)$to->total_amount;
                  }
                  $array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');
                  $main_array[]=$array_ob;
         	}
         	$coupon_ser=$main_array;
    	}
        return DataTables::of($coupon_ser)		            
        ->editColumn('date', function ($coupon_ser) {
            return $coupon_ser["date"];
        })   
        ->editColumn('order', function ($coupon_ser) {
            return $coupon_ser["order"];
        })
        ->editColumn('total', function ($coupon_ser) {
            return $coupon_ser["total"];
        })
        ->make(true);
    }

    public function shipping_report($start_date,$end_date,$shipping_method){
	   	$main_array=array();
	    $coupon_ser=array();
	   	$shipping_ls=Shipping::all();
	   	$setting=Setting::find(1);
	   	$res_curr=explode("-",$setting->default_currency);
	    if($start_date=="abc"&&$shipping_method=="0"){
          	foreach ($shipping_ls as $sh) {
          	  	$order=Order::where("shipping_method",$sh->id)->whereMonth('created_at',date('m'))->get();
	          	if(count($order)!=0){
	                    $array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
	                    $array_ob["name"]=$sh->label;
	                    $array_ob["order"]=count($order);
	                    $total=0;
	                    foreach ($order as $k) {
	                      $total=(int)$total+(int)$k->total;
	                    }

	                    $array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');
	                    $main_array[]=$array_ob;
	          	}
	          	else{
	          	  	  $array_ob["date"]=date('F d,Y',strtotime($sh->created_at));
	          	  	  $array_ob["name"]=$sh->label;
	          	  	  $array_ob["order"]=0;
	          	  	  $array_ob["total"]=0;
	          	  	   $main_array[]=$array_ob;
	          	}
          	    $coupon_ser=$main_array;
            }
        }else if($start_date=="abc"&&$shipping_method!="0"){
      	      $order=Order::where("shipping_method",$shipping_method)->get();
      	      $ship=Shipping::find($shipping_method);
          	  if(count($order)!=0){
                    $array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
                    $array_ob["name"]=$ship->label;
                    $array_ob["order"]=count($order);
                    $total=0;
                    foreach ($order as $k) {
                      $total=(float)$total+(float)$k->total;
                    }
                    $array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');
                    $main_array[]=$array_ob;
          	  }
          	   else{
          	  	  $array_ob["date"]=date('F d,Y',strtotime($ship->created_at));
          	  	  $array_ob["name"]=$ship->label;
          	  	  $array_ob["order"]=0;
          	  	  $array_ob["total"]=0;
          	  	   $main_array[]=$array_ob;
          	  }
          	   $coupon_ser=$main_array;

        }else if($start_date!="abc"&&$shipping_method=="0"){
	      	$sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
			$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s');
	      	foreach ($shipping_ls as $sh) {
          	  	$order=Order::where("shipping_method",$sh->id)->whereBetween("created_at",[$sdate, $edate])->get();
          	  	if(count($order)!=0){
                    $array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
                    $array_ob["name"]=$sh->label;
                    $array_ob["order"]=count($order);
                    $total=0;
                    foreach ($order as $k) {
                      $total=(float)$total+(float)$k->total;
                    }
                    $array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');
                    $main_array[]=$array_ob;
          	  	}
            }
            $coupon_ser=$main_array;
        }else{
      	    $sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
		    $edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s'); 
		    $ship=Shipping::find($shipping_method);     	 
            $order=Order::where("shipping_method",$shipping_method)->whereBetween("created_at",[$sdate, $edate])->get();
      	    if(count($order)!=0){
                $array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
                $array_ob["name"]=$ship->label;
                $array_ob["order"]=count($order);
                $total=0;
                foreach ($order as $k) {
                  $total=(float)$total+(float)$k->total;
                }
                $array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');
                $main_array[]=$array_ob;
          	}
            $coupon_ser=$main_array;
        }
        return DataTables::of($coupon_ser)		            
        ->editColumn('date', function ($coupon_ser) {
            return $coupon_ser["date"];
        })  
        ->editColumn('name', function ($coupon_ser) {
            return $coupon_ser["name"];
        })
        ->editColumn('order', function ($coupon_ser) {
            return $coupon_ser["order"];
        })
        ->editColumn('total', function ($coupon_ser) {
            return $coupon_ser["total"];
        })
        ->make(true);
    }

    public function sales_report($start_date,$end_date,$order_status){
        $main_array=array();
        $coupon_ser=array();
        $setting=Setting::find(1);
   	    $res_curr=explode("-",$setting->default_currency);
        if($start_date=="abc"&&$order_status=="0"){
      		$order=Order::all();
      		if(count($order)!=0){
      			$array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
      		}
      		else{
      			$array_ob["date"]="";
      		}
      		
      		$array_ob["order"]=count($order);
      		$subtotal=0;
      		$total=0;
      		$shipping=0;
      		$product=array();
      		$tax=0;
      		$total=0;
      		foreach ($order as $k) {
      			  $subtotal=(float)$subtotal+(float)$k->sub_total;
      			  $total=(float)$total+(float)$k->total;
      			  $shipping=(float)$shipping+(float)$k->delivery_charge;
      			  $tax=(float)$tax+(float)$k->tax;
      			  $findpro=OrderData::where("order_id",$k->id)->get();
      			  foreach ($findpro as $pro) {
      			  	  $product[]=$pro->product_id;
      			  }
      		}
      		$array_ob["product"]=count(array_unique($product));
      		$array_ob["subtotal"]=$res_curr[1].number_format((float)$subtotal, 2, '.', '');
      		$array_ob["shipping"]=$res_curr[1].number_format((float)$shipping, 2, '.', '');
      		$array_ob["tax"]=$res_curr[1].number_format((float)$tax, 2, '.', '');
      		
      		$array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');      		
      		$main_array[]=$array_ob;
      		$coupon_ser=$main_array;
        }
        else if($start_date=="abc"&&$order_status!="0"){
      		$order=Order::where("status",$order_status)->get();
      		$array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
      		$array_ob["order"]=count($order);
      		$subtotal=0;
      		$shipping=0;
      		$product=array();
      		$tax=0;
      		$total=0;
      		foreach ($order as $k) {
      			  $subtotal=(float)$subtotal+(float)$k->subtotal;
      			  $shipping=(float)$shipping+(float)$k->shipping_charge;
      			  $tax=(float)$tax+(float)$k->taxes_charge;
      			  $findpro=OrderData::where("order_id",$k->id)->get();
      			  foreach ($findpro as $pro) {
      			  	  $product[]=$pro->product_id;
      			  }
      		}
      		$array_ob["product"]=count(array_unique($product));
      		$array_ob["subtotal"]=$res_curr[1].number_format((float)$subtotal, 2, '.', '');
      		$array_ob["shipping"]=$res_curr[1].number_format((float)$shipping, 2, '.', '');
      		$array_ob["tax"]=$res_curr[1].number_format((float)$tax, 2, '.', '');
      		$total=$subtotal+$shipping+$tax;
      		$array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');      		
      		$main_array[]=$array_ob;
      		$coupon_ser=$main_array;

        }else if($start_date!="abc"&&$order_status=="0"){
	      	$sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
			$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s'); 
	      	$order=Order::whereBetween("created_at",[$sdate, $edate])->get();
      		$array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
      		$array_ob["order"]=count($order);
      		$subtotal=0;
      		$shipping=0;
      		$product=array();
      		$tax=0;
      		$total=0;
      		foreach ($order as $k) {
      			  $subtotal=(float)$subtotal+(float)$k->sub_total;
      			  $total=(float)$total+(float)$k->total;
      			  $shipping=(float)$shipping+(float)$k->delivery_charge;
      			  $tax=(float)$tax+(float)$k->tax;
      			  $findpro=OrderData::where("order_id",$k->id)->get();
      			  foreach ($findpro as $pro) {
      			  	  $product[]=$pro->product_id;
      			  }
      		}
      		$array_ob["product"]=count(array_unique($product));
      		$array_ob["subtotal"]=$res_curr[1].number_format((float)$subtotal, 2, '.', '');
      		$array_ob["shipping"]=$res_curr[1].number_format((float)$shipping, 2, '.', '');
      		$array_ob["tax"]=$res_curr[1].number_format((float)$tax, 2, '.', '');
      		
      		$array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');    		
      		$main_array[]=$array_ob;
      		$coupon_ser=$main_array;

        }else{
	      	$sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
			$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s'); 
	      	$order=Order::where("status",$order_status)->whereBetween("created_at",[$sdate, $edate])->get();
	      		$array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
      		$array_ob["order"]=count($order);
      		$subtotal=0;
      		$shipping=0;
      		$product=array();
      		$tax=0;
      		$total=0;
      		foreach ($order as $k) {
      			  $subtotal=(float)$subtotal+(float)$k->sub_total;
      			  $total=(float)$total+(float)$k->total;
      			  $shipping=(float)$shipping+(float)$k->delivery_charge;
      			  $tax=(float)$tax+(float)$k->tax;
      			  $findpro=OrderData::where("order_id",$k->id)->get();
      			  foreach ($findpro as $pro) {
      			  	  $product[]=$pro->product_id;
      			  }
      		}
      		$array_ob["product"]=count(array_unique($product));
      		$array_ob["subtotal"]=$res_curr[1].number_format((float)$subtotal, 2, '.', '');
      		$array_ob["shipping"]=$res_curr[1].number_format((float)$shipping, 2, '.', '');
      		$array_ob["tax"]=$res_curr[1].number_format((float)$tax, 2, '.', '');
      		
      		$array_ob["total"]=$res_curr[1].number_format((float)$total, 2, '.', '');      		
      		$main_array[]=$array_ob;
      		$coupon_ser=$main_array;
        }

        return DataTables::of($coupon_ser)		            
        ->editColumn('date', function ($coupon_ser) {
            return $coupon_ser["date"];
        })  
        ->editColumn('order', function ($coupon_ser) {
            return $coupon_ser["order"];
        })
        ->editColumn('product', function ($coupon_ser) {
            return $coupon_ser["product"];
        })
        ->editColumn('subtotal', function ($coupon_ser) {
            return $coupon_ser["subtotal"];
        })
        ->editColumn('shipping', function ($coupon_ser) {
            return $coupon_ser["shipping"];
        })
        ->editColumn('tax', function ($coupon_ser) {
            return $coupon_ser["tax"];
        })
        ->editColumn('total', function ($coupon_ser) {
            return $coupon_ser["total"];
        })
        ->make(true);
	}

    public function product_stock_report($product,$sku,$stock){
	    $main_array=array();
	    $coupon_ser=array();
	    if($product=='0'&& $sku=='0'&& $stock=='3'){//000
	      		$coupon_ser=Product::all();
	    }
	    else if($product=='0'&& $sku=='0'&& $stock!='3'){//001
	      	   $coupon_ser=Product::where("stock",$stock)->get();
	    }
	    else if($product=='0'&& $sku!='0'&& $stock=='3'){//010
	      		$coupon_ser=Product::where("sku",$sku)->get();
	    }
	    else if($product=='0'&& $sku!='0'&& $stock!='3'){//011
	      		$coupon_ser=Product::where("sku",$sku)->where("stock",$stock)->get();
	    }
	    else if($product!='0'&& $sku=='0'&& $stock=='3'){//100
	      		$coupon_ser=Product::where("name",$product)->get();
	    }
	    else if($product!='0'&& $sku=='0'&& $stock!='3'){//101
	      		$coupon_ser=Product::where("name",$product)->where("stock",$stock)->get();
	    }
	    else if($product!='0'&& $sku!='0'&& $stock=='3'){//110
	      		$coupon_ser=Product::where("name",$product)->where("sku",$sku)->get();
	    }
	    else if($product!='0'&& $sku!='0'&& $stock!='3'){//111
	      		$coupon_ser=Product::where("name",$product)->where("sku",$sku)->where("stock",$stock)->get();
	    }
     
        return DataTables::of($coupon_ser)		            
        ->editColumn('product', function ($coupon_ser) {
             $getlang = FileMeta::where("model_id",$coupon_ser->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
            return isset($getlang)?$getlang->meta_value:'';
        }) 
        ->editColumn('sku', function ($coupon_ser) {
            return $coupon_ser->sku;
        })  
        ->editColumn('stock', function ($coupon_ser) {
        	if($coupon_ser->stock=='0'){
        		return __("messages.outstock");
        	}
        	else{
        		return __("messages.in_stock");
        	}
            
        })
        ->make(true);
    }

    public function top_seller_report($start_date,$end_date){
   	    $coupon_ser=array();
   	    $main_array=array();
   		if($start_date=="abc"){
   			$coupon_ser=User::where('user_type','3')->get();
   			foreach ($coupon_ser as $val) {
   			    $seller_order=Order::where('seller_id',$val->id)->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get();
   			    if(count($seller_order))
   			    {
   			    	$val->seller_order=count($seller_order);		
   			    	$val->seller_total=Order::where('seller_id',$val->id)->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->sum('sub_total');
   			    	$val->selling_date=date('F d,Y',strtotime($seller_order[0]->created_at))."-".date('F d,Y',strtotime($seller_order[count($seller_order)-1]->created_at));
   			    }
   			    else
   			    {
   			    	$val->seller_order=0;		
   			    	$val->seller_total=0;
   			    	$val->selling_date='-';
   			    }
   			   	
   			}
   		}
   		else{
   			$sdate = DateTime::createFromFormat('F d,yy',$start_date)->format('Y-m-d h:i:s');
		 	$edate = DateTime::createFromFormat('F d,yy',$end_date)->format('Y-m-d h:i:s'); 
   			$coupon_ser=User::where('user_type','3')->get();
   			foreach ($coupon_ser as $val) {
   			    $seller_order=Order::where('seller_id',$val->id)->whereBetween('created_at',[$sdate, $edate])->get();
   			    if(count($seller_order))
   			    {
   			    	$val->seller_order=count($seller_order);		
   			    	$val->seller_total=Order::where('seller_id',$val->id)->whereBetween('created_at',[$sdate, $edate])->sum('sub_total');
   			    	$val->selling_date=date('F d,Y',strtotime($seller_order[0]->created_at))."-".date('F d,Y',strtotime($seller_order[count($seller_order)-1]->created_at));
   			    }
   			    else
   			    {
   			    	$val->seller_order=0;		
   			    	$val->seller_total=0;
   			    	$val->selling_date='-';
   			    }
   			   	
   			}
   		}
   		return DataTables::of($coupon_ser)
		->editColumn('id', function ($coupon_ser) {
        	return $coupon_ser->id;
        })		            
        ->editColumn('date', function ($coupon_ser) {
            return $coupon_ser->selling_date;
        }) 
         ->editColumn('name', function ($coupon_ser) {
            return $coupon_ser->first_name;
        }) 
        ->editColumn('order', function ($coupon_ser) {
        	return $coupon_ser->seller_order;
        })
        ->editColumn('total', function ($coupon_ser) {
        	return $coupon_ser->seller_order;
        })
        
        ->make(true);
    }

    public function date_duration(request $request){
   	  echo $request->get("s_date");
   	  die();
   	  //$date_duration=date('d-m-Y',strtotime($request->get("s_date"))).' to '.date('d-m-Y',strtotime($request->get("e_date")));
	}

}