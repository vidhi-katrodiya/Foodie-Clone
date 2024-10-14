<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Auth;
use Session;
use Response;
use DataTables;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Coupon;
use App\Models\BookTable;
use App\Models\CartData;
use App\Models\ProductOption;
use App\Models\Addresses;
use App\Models\Setting;
use App\Models\Order;
use App\Models\OrderData;
use App\Models\Lang_core;
use App\Models\About;
use Carbon\Carbon;
use \Mpdf\Mpdf as PDF;
use Illuminate\Support\Facades\Storage;
use DB;

class FrontController extends Controller
{
    public function showFront(){
      $data= Categories::where('parent_category',0)->get();
      $setting = Setting::find(1);
       $getrestaurant= User::select('id','first_name','email','phone','address','res_image','delivery_time','review_count','two_person_cost','access_cat')->where("user_type",3)->orderBy('review_count','DESC')->limit(10)->get();

        foreach($getrestaurant as $value){
            $review =Review::where("res_id",$value->id)->get();
            $count = count($review);
            if($count>0){
                $review_count = $count;
            }else{
                $review_count =0;
            }

            $avgStar = Review::where("res_id",$value->id)->avg('ratting');

             if($avgStar != ""){
                $rating = $avgStar;
            }else{
                $rating ="0.0";
            }

            $check_fav=Wishlist::where('user_id',Auth::id())->where('res_id',$value->id)->first();
             if($check_fav)
             {
               $value->is_fav = "1";
             }
             else
             {
              $value->is_fav = "0";
             }

             $get_offer = Coupon::where("user_id",$value->id)->where('is_main_offer',"1")->first();
            
             if($get_offer){
                $value->offer = $get_offer;
             }else{
                $value->offer = "";
             }

             $category_str=  $value->access_cat;
             $cat = explode(",",$category_str);
             $cat_name = array();
              foreach($cat as $val){
                  $cat =Categories::select('cat_name')->where("id",$val)->WHERE('parent_category',0)->first();
                  if($cat)
                  {
                    
                  $cat_name[] =$cat->cat_name;    
                  }
              }
            $value->access_cat = $cat_name;

            $value->rating = $rating;
            $value->review_count = $count;
          }
         
      return view("front.home")->with("data",$data)->with("restaurant",$getrestaurant)->with("setting",$setting);
    }

    public function favourit_data(request $request){

     $type=$request->get('type');

       if($type==1) 
       {
         $fav_list=Wishlist::where('user_id',Auth::id())->orderBy('id','DESC')->limit(6)->get();
       } 
       else
       {
          $fav_list=Wishlist::where('user_id',Auth::id())->orderBy('id','DESC')->get();
       }
      
     
     $user=array();
     foreach ($fav_list as $value) {
        $restaurent= User::where('id',$value->res_id)->where('is_deleted',0)->first();
        if($restaurent)
        {
          $category_str=  $restaurent->access_cat;
          $cat = explode(",",$category_str);
          $cat_name = array();
          foreach($cat as $val)
          {
            $cat =Categories::select('cat_name')->where("id",$val)->WHERE('parent_category',0)->first();
            if($cat)
            {
              $cat_name[] =$cat->cat_name;    
            }
          }
          $restaurent->access_cat = $cat_name;
          $user[]=$restaurent;
        }
      }
      foreach($user as $value)
      {
        $offer=Coupon::select('id','value','discount_type','minmum_spend','code')->where('is_main_offer',"1")->where("user_id",$value->id)->first();
        if($offer)
        {
         $value->offer=$offer;
        }
        else
        {
          $value->offer="";
        }
        $review =Review::where("res_id",$value->id)->get();
        $count = count($review);
        if($count>0){
            $review_count = $count;
        }else{
            $review_count =0;
        }

        $avgStar = Review::where("res_id",$value->id)->avg('ratting');

         if($avgStar != ""){
            $rating = $avgStar;
        }else{
            $rating ="0.0";
        }
        $value->rating = $rating;
        $value->review_count = $count;
        $check_fav=Wishlist::where('user_id',Auth::id())->where('res_id',$value->id)->first();
        if($check_fav)
        {
         $value->is_fav = "1";
        }
        else
        {
        $value->is_fav = "0";

        }
        $category_str=  $value->access_cat;
        
      }
      $auth_id=Auth::id();
      $output = '';
        foreach($user as $restaurant)
          {
            if($type=='1')
            {

              $output.='<div class="col-md-4 col-sm-6 mb-4 pb-2">';
            }
            else
            {
              $output.='<div class="col-md-3 col-sm-6 mb-4 pb-2">';
            }
                  $output.='<div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
                  <div class="list-card-image">
                  <div class="star position-absolute"><span class="badge badge-success"><i class="icofont-star"></i>';

                  if ($restaurant->review_count == 0)
                  {

                      $output .= $restaurant->rating.'('.$restaurant->review_count.')'.' </span>';
                  }
                  else
                  {

                      $output .= $restaurant->rating.'('.$restaurant->review_count .'+'.')'. '</span>';
                  }
                  $output.='</span></div>
                  <input type="hidden" required name="user_id" id="user_id" value="'.$auth_id.'">
                  <input type="hidden" required name="res_id" id="res_id" value="'.$restaurant->id.'">';

                  $output.='<div class="favourite-heart text-danger position-absolute fav_main_box">';

                   $output.='<i class="fa fa-times-circle" aria-hidden="true" onclick="delete_fav_plant('.$restaurant->id.')"></i>';
                       
                  $output.='</div><a href="res_detail/'.$restaurant->id.'">';
                  $res_image='public/upload/restaurant/'.$restaurant->res_image;
                     if(file_exists($res_image) && $restaurant->res_image != null)
                     {
                         $output.='<img class="img-fluid item-img" src="public/upload/restaurant/'.$restaurant->res_image.'" alt="">';
                     }
                     else
                    {
                      $output.='<img class="img-fluid item-img" src="public/upload/restaurant/restaurant.jpg" alt="">';
                    }
                          
                     
                  $output.='</a>
                  </div>
                  <div class="p-3 position-relative">
                  <div class="list-card-body">
                  <h6 class="mb-1"><a href="res_detail/'.$restaurant->id.'" >'.$restaurant->first_name.'</a></h6>
                  <p class="text-gray mb-3">';
                   
                  if(!empty($restaurant->access_cat))
                  {
                      $output.= $cat_str=implode(" • ", $restaurant->access_cat);
                  }
                  else
                  {
                        $output.=" • • •";
                  }
                        
                  $output.='</p>
                  <p class="text-gray mb-3 time"><span class="bg-light text-dark rounded-sm pl-2 pb-1 pt-1 pr-2"><i class="icofont-wall-clock"></i> '.date("i", strtotime($restaurant->delivery_time)).' min</span> <span class="float-right text-black-50">₹'.$restaurant->two_person_cost.' FOR TWO</span></p>
                  </div>
                  <div class="list-card-badge">';
                   if($restaurant->offer !="")
                   {
                     $output.='<span class="badge badge-success">OFFER</span>';
                    if($restaurant->offer->discount_type =='1')
                    {
                        $output.='<small>'.$restaurant->offer->value.'% off | Use Coupon '.$restaurant->offer->code.'</small>';
                    }
                       
                    else
                    {

                         $output.='<small>₹'.$restaurant->offer->value.' off | Use Coupon '.$restaurant->offer->code.'</small>';
                    }
                   
                  }
                  $output.='</div>
                  </div>
                  </div>
              </div>';
          
          }
          if(count($fav_list)==6)
          {
            $output.='<div class="col-md-12 text-center ">
                <a class="btn btn-primary" href="Wishlist">See All</a>
            </div>';
          }
          echo $output;
    }

    public function order_data(request $request){
     $type=$request->get('type');

       if($type==1) 
       {
         $order=Order::with('userdata')->with('orderdatals')->where('user_id',Auth::id())->orderBy('id','DESC')->limit(4)->get();
       } 
       else
       {
          $order=Order::with('userdata')->with('orderdatals')->where('user_id',Auth::id())->orderBy('id','DESC')->get();
       }
       $output="";
      $record = count($order);
       if(count($order) > 0)
       {
          foreach ($order as $value) {

            $order_id = $value->id;
            $order=OrderData::where('order_id',$order_id)->orderBy('id','DESC')->get();

            $elements = array();

            foreach($order as $detail){
                 $pro=Product::where('id',$detail->product_id)->first();
                 $elements[] = $pro->name;
            }

            $res= User::where('id',$value->seller_id)->first();
            $image = asset('public/upload/restaurant/'.$res->res_image);

            $product_id = (implode(' • ', $elements));

            $time = date('d M,Y h:i A', strtotime($value->orderplace_datetime));
            $route = url('order_detail/'.$value->id);
            $invoice_route = url('invoice/'.$value->id);

            $output.='<div class="bg-white card mb-4 order-list shadow-sm">
                <div class="gold-members p-4">
                  <a href="#">
                    <div class="media">
                      <img class="mr-4" src="'.$image.'" alt="Generic placeholder image">
                      <div class="media-body">
                        <span class="float-right text-info">Delivered on Mon, Nov 12, 7:18 PM <i class="icofont-check-circled text-success"></i>
                        </span>
                        <h6 class="mb-2">
                          <a href="detail.html" class="text-black">'.$res->first_name.'</a>
                        </h6>
                        <p class="text-gray mb-1">
                          <i class="icofont-location-arrow"></i> '.$res->address.'
                        </p>
                        <p class="text-gray mb-3">
                          <i class="icofont-list"></i> ORDER '.$value->order_no.' <i class="icofont-clock-time ml-2"></i>'.$time.'
                        </p>
                        <p class="text-dark">'.$product_id.' </p>
                        <hr>
                        <div class="float-right">
                          
                          <a class="btn btn-sm btn-outline-primary" href="'.$invoice_route.'">
                            <i class="icofont-download"></i> Invoice </a>
                          <a class="btn btn-sm btn-primary" href="'.$route.'">
                            <i class="icofont-refresh"></i> View Detail </a>
                        </div>
                        <p class="mb-0 text-black text-primary pt-2">
                          <span class="text-black font-weight-bold"> Total Paid:</span> ₹'.$value->total.'
                        </p>
                      </div>
                    </div>
                  </a>
                </div>
              </div>';
          }

          if($record == 4)
          {
            $output.='<div class="col-md-12 text-center ">
                          <a class="btn btn-primary" href="order_list">See All</a>
                      </div>';
          }
       }
       echo $output;
    }

    public function order_detail($id){
       $user = User::where('id',Auth::id());
       $order_data = OrderData::with('productdata')->where('order_id',$id)->get();
       $order_record = Order::where('id',$id)->first();
       $array = array();
       foreach ($order_data as $value) {
          $total = array_push($array,$value->total_amount);
       }
       
       $item_total = array_sum($array);
       
       return view('front.order-detail',compact('user','order_data','order_record','item_total'));
    }

    public function address_data(request $request){

     $type=$request->get('type');

       if($type==1) 
       {
         $address_list=Addresses::where('user_id',Auth::id())->orderBy('id','DESC')->limit(6)->get();
       } 
       else
       {
          $address_list=Addresses::where('user_id',Auth::id())->orderBy('id','DESC')->get();
       }

       $output="";
       if(count($address_list)>0)
       {
        foreach ($address_list as $value) {
         $output.='<div class="col-md-6">
                  <div class="bg-white card addresses-item mb-4 border border-primary shadow" style="height:176px;">
                    <div class="gold-members p-4">
                      <div class="media">
                        <div class="mr-3">
                          <i class="icofont-location-pin icofont-3x"></i>
                        </div>
                        <div class="media-body">
                          <h6 class="mb-1 text-secondary">'.$value->name.'</h6>
                          <p class="text-black">'.$value->address.'</p>
                          <p class="mb-0 text-black font-weight-bold">
                            <a class="text-primary mr-3"  href="edit_address/'.$value->id.'">
                                  <i class="icofont-ui-edit"></i> EDIT </a>
                           
                            <a class="text-danger" onclick="user_id('.$value->id.')" data-bs-toggle="modal" data-bs-target="#delete-address-modal" href="#">
                              <i class="icofont-ui-delete"></i> DELETE </a>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>';
        }
        if(count($address_list)==6)
          {
            $output.='<div class="col-md-12 text-center ">
                <a class="btn btn-primary" href="address_list">See All</a>
            </div>';
          }
       }
      echo $output;
    }

    public function show_offer(){
      return view("front.offer");
    }

    public function offer_data(request $request){
      $type=$request->get('type');
       if($type==1) 
       {
          $data=Coupon::with('resdata')->orderBy('id','DESC')->orderBy('id','desc')->limit(6)->get();
       } 
       else
       {
          $data=Coupon::with('resdata')->orderBy('id','DESC')->orderBy('id','desc')->get();
       }
      
      $output="";
      if(count($data)>0){
         foreach ($data as  $value) {
          
           $output.='<div class="col-md-4 mb-6" style="margin-bottom: 30px;">
            <div class="card offer-card border-0 shadow-sm">
            <div class="card-body" >
            <h5 class="card-title"> '.$value->code.'</h5>
            <h6 class="card-subtitle mb-2 text-block">Get';
             if($value->discount_type=='1')
             {
                $output.=' '.$value->value.'% ';
             }
             else
             {
                $output.=' ₹'.$value->value.' ';
             }
            $output.='OFF on your order from  </h6>
            <p class="card-text">Use code '.$value->code.' & get';
             if($value->discount_type=='1')
             {
                $output.=' '.$value->value.'% ';
             }
             else
             {
                $output.=' ₹'.$value->value.' ';
             }
             $output.='off on your order from Foodieclone  Website and Mobile site. Manimum spend: ₹'.$value->minmum_spend.' </p>
            <a href="#" class="card-link">COPY CODE</a>
            <a href="#" class="card-link">KNOW MORE</a>
            </div>
            </div>
            </div>';
          
         }
            
      }
      else
      {
        $output.='<div class="col-md-12 text-center load-more">
              <button class="btn btn-primary" type="button" disabled>
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Loading... </button>
            </div>';
      }
      if($type==1)
      {
        if(count($data)==6)
        {
           $output.='<div class="col-md-12 text-center ">
                <a class="btn btn-primary" href="offer">See All</a>
            </div>';
        }
      }
      echo $output;
    }

    public function search_res(request $request){

         $latitude=$request->get("lat"); 
         $longitude=$request->get("lon"); 
         $category= Categories::WHERE('parent_category',0)->get();
         $cat_id=$request->get("cat_id"); 
         $type="search_res";
         if($latitude != "" && $longitude!="")
         {
            return view("front.listing")->with("category",$category)->with("type",$type)->with("lat",$latitude)->with("lan",$longitude)->with("cat_id",$cat_id);
         }
         {
              return redirect()->route("showFront");
         }
    }

    public function express_delivery_res($lat,$lan){
         $category= Categories::where('parent_category',0)->get();
         $latitude=$lat;
        
         $longitude=$lan;
        if($longitude!=''&&$latitude!='')
         {
            $type='express_delivery_res';
           
           return view("front.listing")->with("category",$category)->with("type",$type)->with("lat",$latitude)->with("lan",$longitude);
         }
         else
         {
              return redirect()->route("showFront");
         }
    }

    public function show_register_user(){
        return view("front.register");
    }
    
    public function check_email(Request $request){
      $check=User::where('email',$request->get("email"))->first();
      if($check)
      {
         echo "1";
      }
      else
      {
          echo "0";
      }
    }
    
    public function registed_user(request $request){

        $user=User::where('email',$request->get("email"))->first();
        if($user)
        {
            echo "failed";
        }
        else{
          $register = new User();
          $register->email=$request->get("email"); 
          $register->password=$request->get("password"); 
          $register->first_name=$request->get("name"); 
          $register->gender=$request->get("gender"); 
          $register->dob=$request->get("dob"); 
          $register->phone=$request->get("phone"); 
          $register->user_type="1"; 
          $register->save();
          Auth::login($register,true);
          if($request->get("gender")=="Female")
          {
            Session::put("profile_pic",asset("public/upload/profile/woman_profile.jpg"));
          }
          else
          {
            Session::put("profile_pic",asset("public/upload/profile/man_profile.jpg"));
          }
          
          
          echo"success";
        }
    }

    public function login_user(){
        return view("front.login");
    }

    public function PostLogin(Request $request){

        $email=$request->get('email');
        $password=$request->get("password");
        $user_type="1";
        $user= User::where('email',$email)->where("password",$password)->where('user_type',$user_type)->first();

            if($user)
            {
                Session::put('User', $user);
                Session::put('is_where', 1);
                Auth::login($user,true);
                return redirect('/');
            }
            else
            {
                return redirect()->back()->with('error', 'Email or Password does not exist');
            }
    }
   
    public function userlogout(){
           Auth::logout();
           return redirect('/');
    }
    
    public function listing($type,Request $request){
      $category= Categories::where('parent_category',0)->get();
      return view("front.listing")->with("category",$category)->with("type",$type);
    }

    public function filter_data(Request $request){

          $cat_data=$request->get('fil_category');
          $fil_del_time=$request->get('fil_del_time');
          $fil_feature=$request->get('fil_feature');
          $type=$request->get('type');
           
          $auth_id=Auth::id();
         //$restaurant= User::where('is_deleted','0');
          if($type=='all')
          {
            $restaurant=DB::table('users')->where("users.user_type",3);
          }
          if($type=="shortby_distance")
          {
            $restaurant= DB::table('users')->where("users.user_type",3)->orderBy('users.delivery_time','asc');
          }
          if($type=="no_of_offers")
          {
            $restaurant=DB::table('coupon')->join('users', 'coupon.user_id', '=', 'users.id')->groupBY('coupon.user_id')-> orderBy(\DB::raw('count(coupon.user_id)'), 'DESC');
          }
          if($type=="rating")
          {
            
              $restaurant=DB::table('users')->where("users.user_type",3)->orderBy('users.review_count','DESC');
          }
          if($type=="all_time_fav_res")
          {

             $restaurant=DB::table('wishlist')->join('users', 'wishlist.res_id', '=', 'users.id')->groupBY('wishlist.res_id')-> orderBy(\DB::raw('count(wishlist.res_id)'), 'DESC');
     
          }
          if($type=="short_offer")
          {
            $restaurant=DB::table('coupon')->join('users', 'coupon.user_id', '=', 'users.id')->where("coupon.is_main_offer","1")->groupBY('coupon.user_id');
          }
          if($type=="express_delivery_res")
          {
            $radius = 50;
           $user_type="3";
           $latitude=$request->get('lat');
           $longitude=$request->get('long');
           $restaurant = \DB::table("users")
                     ->select("users.id","users.first_name","users.access_cat","users.lat","users.long","users.res_image","users.delivery_time","users.two_person_cost",
                     \DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                     * cos(radians(users.lat)) 
                     * cos(radians(users.long) - radians(" . $longitude . ")) 
                     + sin(radians(" .$latitude. ")) 
                     * sin(radians(users.lat))) AS distance"))
                     ->where('user_type',$user_type)
                     ->having('distance', '<', $radius)
                     ->orderBy("distance",'asc');
                     
          }
          if($type=="search_res")
          {
            $radius = 50;
           $user_type="3";
           $latitude=$request->get('lat');
           $longitude=$request->get('long');
           $cat_id=$request->get("cat_id"); 
           if($cat_id !="")
           {
              $restaurant = \DB::table("users")
                   ->select("users.id","users.first_name","users.access_cat","users.lat","users.long","users.res_image","users.delivery_time","users.two_person_cost",
                   \DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                   * cos(radians(users.lat)) 
                   * cos(radians(users.long) - radians(" . $longitude . ")) 
                   + sin(radians(" .$latitude. ")) 
                   * sin(radians(users.lat))) AS distance"))
                   ->whereRaw("find_in_set('".$cat_id."',users.access_cat)")
                   ->where('user_type',$user_type)
                   ->having('distance', '<', $radius)
                   ->orderBy("distance",'asc');
                   
           }
           else
           {
              $restaurant = \DB::table("users")
                     ->select("users.id","users.first_name","users.access_cat","users.lat","users.long","users.res_image","users.delivery_time","users.two_person_cost",
                     \DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                     * cos(radians(users.lat)) 
                     * cos(radians(users.long) - radians(" . $longitude . ")) 
                     + sin(radians(" .$latitude. ")) 
                     * sin(radians(users.lat))) AS distance"))
                     ->where('user_type',$user_type)
                     ->having('distance', '<', $radius)
                     ->orderBy("distance",'asc');
                     
           }
           
          }
          if(!empty($cat_data))
          {
            //print_r($cat_data);
            foreach ($cat_data as $cat_value) {
                   $restaurant->whereRaw("find_in_set('".$cat_value."',users.access_cat)");
            }
          }
           if(!empty($fil_feature))
          {
           
            foreach ($fil_feature as  $fil_value) {
              if($fil_value==0)
              {

              }
              if($fil_value==1)
              {
                if($type=="no_of_offers")
                {
                  $restaurant->where('coupon.free_shipping','1');
                }
                elseif($type=="short_offer")
                {
                  $restaurant->where('coupon.free_shipping','1');
                }
                else
                {
                  $restaurant->leftjoin('coupon', 'coupon.user_id', '=', 'users.id')->groupBY('coupon.user_id')->where('coupon.free_shipping','1');
                }
                
              }
              if($fil_value==2)
              {
                if($type=="no_of_offers")
                {
                  
                }
                elseif($type=="short_offer")
                {
                  $restaurant->orderBy(\DB::raw('count(coupon.user_id)'), 'DESC');
                }
                else
                {
                  $restaurant->leftjoin('coupon', 'coupon.user_id', '=', 'users.id')->groupBY('coupon.user_id')->orderBy(\DB::raw('count(coupon.user_id)'), 'DESC');
                }
                  
              }
            }
          }
          if(!empty($fil_del_time))
          { 
              $del_value=end($fil_del_time);
              if($del_value !=0)
              {

                 $time='00:'.$del_value.':00';
                
                 $restaurant->where('users.delivery_time','<=',$time)->orderBy('users.delivery_time','asc');
              }
              else
              {

                
                 $restaurant->orderBy('users.delivery_time','asc');
              }
          }

          //$user=$restaurant->get();
         
          $user = $restaurant->get();
        
          foreach($user as $value){
            $offer=Coupon::select('id','value','discount_type','minmum_spend','code')->where('is_main_offer',"1")->where("user_id",$value->id)->first();
          if($offer)
          {
           $value->offer=$offer;
          }
          else
          {
            $value->offer="";
          }
            $review =Review::where("res_id",$value->id)->get();
            $count = count($review);
            if($count>0){
                $review_count = $count;
            }else{
                $review_count =0;
            }

            $avgStar = Review::where("res_id",$value->id)->avg('ratting');

             if($avgStar != ""){
                $rating = $avgStar;
            }else{
                $rating ="0.0";
            }
            $value->rating = $rating;
            $value->review_count = $count;
          }
          $user_cat = array();

          foreach($user as $value){
             $check_fav=Wishlist::where('user_id',Auth::id())->where('res_id',$value->id)->first();
             if($check_fav)
             {
               $value->is_fav = "1";
             }
             else
             {
              $value->is_fav = "0";

             }
             $category_str=  $value->access_cat;
             $cat = explode(",",$category_str);
             $cat_name = array();
                foreach($cat as $val){
                    $cat =Categories::select('cat_name')->where("id",$val)->WHERE('parent_category',0)->first();
                    if($cat)
                    {
                      
                    $cat_name[] =$cat->cat_name;    
                    }
                }
                $value->access_cat = $cat_name;
                

          }
          
           $output = '';
          foreach($user as $restaurant)
            {
              $output.='<div class="col-md-4 col-sm-6 mb-4 pb-2">
                    <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
                    <div class="list-card-image">
                    <div class="star position-absolute"><span class="badge badge-success"><i class="icofont-star"></i>';

                    if ($restaurant->review_count == 0)
                    {

                        $output .= round($restaurant->rating,1).'('.$restaurant->review_count.')'.' </span>';
                    }
                    else
                    {

                        $output .=round($restaurant->rating,1).'('.$restaurant->review_count .'+'.')'. '</span>';
                    }
                    $output.='</span></div>
                    <input type="hidden" required name="user_id" id="user_id" value="'.$auth_id.'">
                    <input type="hidden" required name="res_id" id="res_id" value="'.$restaurant->id.'">';

                    $output.='<div class="favourite-heart  position-absolute fav_main_box">';

                     if($restaurant->is_fav =="1")
                     {
                        $output.='<i class="icofont-heart add_fav_icon  fav_icon_'.$restaurant->id.'"  style="color: red;" onclick="add_favourite('.$restaurant->id .')" ></i>';
                     }
                          
                     else
                     {
                       if(Auth::id())  
                       {
                          $output.='<i class="icofont-heart delete_fav_icon fav_icon_'.$restaurant->id.'"style="color: gray;" onclick="add_favourite('.$restaurant->id .')" ></i>';
                       }   
                       else 
                       {
                             $output.='<a data-bs-toggle="modal" data-bs-target="#myModal"><i class="icofont-heart delete_fav_icon fav_icon_'.$restaurant->id.'"   style="color: gray;"></i></a>';
                       } 
                         
                     }
                          
                    if($type=="express_delivery_res" )
                    {    
                    $output.='</div><a href="../../res_detail/'.$restaurant->id.'">';
                    $res_image='public/upload/restaurant/'.$restaurant->res_image;
                       if(file_exists($res_image) && $restaurant->res_image != null)
                       {
                           $output.='<img class="img-fluid item-img" src="../../public/upload/restaurant/'.$restaurant->res_image.'" alt="">';
                       }
                       else
                      {
                        $output.='<img class="img-fluid item-img" src="../../public/upload/restaurant/restaurant.jpg" alt="">';
                      }
                            
                    }
                    elseif($type=="search_res" )
                    {    
                    $output.='</div><a href="res_detail/'.$restaurant->id.'">';
                    $res_image='public/upload/restaurant/'.$restaurant->res_image;
                       if(file_exists($res_image) && $restaurant->res_image != null)
                       {
                           $output.='<img class="img-fluid item-img" src="public/upload/restaurant/'.$restaurant->res_image.'" alt="">';
                       }
                       else
                      {
                        $output.='<img class="img-fluid item-img" src="public/upload/restaurant/restaurant.jpg" alt="">';
                      }
                            
                    }
                    else
                    {
                       $output.='</div><a href="../res_detail/'.$restaurant->id.'">';
                    $res_image='public/upload/restaurant/'.$restaurant->res_image;
                       if(file_exists($res_image) && $restaurant->res_image != null)
                       {
                           $output.='<img class="img-fluid item-img" src="../public/upload/restaurant/'.$restaurant->res_image.'" alt="">';
                       }
                       else
                      {
                        $output.='<img class="img-fluid item-img" src="../public/upload/restaurant/restaurant.jpg" alt="">';
                      }
                          
                    }
                    $output.='</a>
                    </div>
                    <div class="p-3 position-relative">
                    <div class="list-card-body">';
                    if($type=="express_delivery_res" )
                    {
                        $output.='<h6 class="mb-1"><a href="../../res_detail/'.$restaurant->id.'" >'.$restaurant->first_name.'</a></h6>';
                    }
                    elseif($type=="search_res" )
                    {
                        $output.='<h6 class="mb-1"><a href="res_detail/'.$restaurant->id.'" >'.$restaurant->first_name.'</a></h6>';
                    }
                    else
                    {
                        $output.='<h6 class="mb-1"><a href="../res_detail/'.$restaurant->id.'" >'.$restaurant->first_name.'</a></h6>';
                    }
                    
                    $output.='<p class="text-gray mb-3">';
                     
                    if(!empty($restaurant->access_cat))
                    {
                      $cat_name = implode(" • ", $restaurant->access_cat);
                        if(strlen($cat_name) > 38){
                            $output.= $str = substr($cat_name, 0, 38) . '...';
                        }else{
                             $output.= $cat_name;
                        }
                    }
                    else
                    {
                          $output.=" • • •";
                    }
                          
                    $output.='</p>
                    <p class="text-gray mb-3 time"><span class="bg-light text-dark rounded-sm pl-2 pb-1 pt-1 pr-2"><i class="icofont-wall-clock"></i> '.date("i", strtotime($restaurant->delivery_time)).' min</span> <span class="float-right text-black-50">₹'.$restaurant->two_person_cost.' FOR TWO</span></p>
                    </div>
                    <div class="list-card-badge">';
                     if($restaurant->offer !="")
                     {
                       $output.='<span class="badge badge-success">OFFER</span>';
                      if($restaurant->offer->discount_type =='1')
                      {
                          $output.='<small>'.$restaurant->offer->value.'% off | Use Coupon '.$restaurant->offer->code.'</small>';
                      }
                         
                      else
                      {

                           $output.='<small>₹'.$restaurant->offer->value.' off | Use Coupon '.$restaurant->offer->code.'</small>';
                      }
                     
                    }
                    $output.='</div>
                    </div>
                    </div>
                </div>';
            
            }
            echo $output;
    }

    public function resdetail($id,Request $request){

      $rat=0;
      $data = User::where('id', $id)->first();
      $category_str=  $data->access_cat;
      $cat = explode(",",$category_str);
      $cat_name = array();
      foreach($cat as $val){
          $cat =Categories::select('cat_name')->where("id",$val)->WHERE('parent_category',0)->first();
          if($cat)
          {
            
          $cat_name[] =$cat->cat_name;    
          }
      }
      $data->access_cat = $cat_name;

      $check_fav=Wishlist::where('user_id',Auth::id())->where('res_id',$data->id)->first();
       if($check_fav)
       {
         $data->is_fav = "1";
       }
       else
       {
        $data->is_fav = "0";

       }
      
       $review =Review::where("res_id",$id)->get();
       
       foreach($review as $val){
            $ratt = $val->ratting;
            $total = $rat + $ratt;
            $rat=$total;
       }
       
       $count = count($review);
        if($count>0){
            $review_count = $count;
        }else{
            $review_count =0;
        }

        $avgStar = Review::where("res_id",$id)->avg('ratting');
        if($avgStar != ""){
            $rating = $avgStar;
        }else{
            $rating ="0.0";
        }

        $data->rating = $rating;

        $data->review_count = $review_count;
  
        $res_cat= Categories::WHERE('res_id',$id)->get();

        $pro= Product::WHERE('user_id',$id)->limit(3)->get();

        $offer = Coupon::where('is_main_offer',1)->first();
        
        $user_id = $id=DB::table('users')->where('id',Auth::id())->first();
        
        return view("front.detail")->with("user",$data)->with("total_rat",$rat)->with("res_cat",$res_cat)->with("pro",$pro)->with("review",$review)->with("offer",$offer)->with("id",$user_id);
    }
    
    public function add_favourite(Request $request){
        $user_id=$request->get('user_id');
        $res_id=$request->get('id');
        $check_fav=Wishlist::where('user_id',$user_id)->where('res_id',$res_id)->first();
        if($check_fav){
            $check_fav->delete();
            echo 0;
        }
        else
        {
            $store=new Wishlist();
            $store->user_id=$request->get('user_id');
            $store->res_id=$request->get('id');
            $store->save();
            echo 1;
        }
    }
    
    public function remove_fav_res(Request $request){
       $id=$request->get('id');
       $remove_fav_res=Wishlist::where('user_id',Auth::id())->where("res_id",$id)->first();
       
       $remove_fav_res->delete();
    }
    
    public function Wishlist(){
       $fav_list=Wishlist::where('user_id',Auth::id())->get();
       $data=array();
       foreach ($fav_list as $value) {
          $restaurent= User::where('id',$value->res_id)->where('is_deleted',0)->first();

          if($restaurent)
          {
             $category_str=  $restaurent->access_cat;
             $cat = explode(",",$category_str);
             $cat_name = array();
              foreach($cat as $val){
                  $cat =Categories::select('cat_name')->where("id",$val)->WHERE('parent_category',0)->first();
                  if($cat)
                  {
                    
                  $cat_name[] =$cat->cat_name;    
                  }
              }
              $restaurent->access_cat = $cat_name;
              $data[]=$restaurent;
          }

       }
       
         return view("front.Wishlist")->with("data",$data);
    }
    
    public function add_review(request $request){
        $user = User::where('id',Auth::id())->first();
        $user_id = $user->id;

        $this->validate($request,[
            'review'=>'required',
        ]);
        if(Review::where('user_id',$user_id)->exists()){

            $review = Review::where('user_id',$user_id)->first();
          
          if($request->get("product_id") != ""){
                 $review->product_id=""; 
          }
          if($request->get("res_id") != ""){
                 $review->res_id=$request->get("res_id"); 
          }
          if($request->get("review") != ""){
                 $review->review=$request->get("review"); 
          }
          if($request->get("ratting") != ""){
                 $review->ratting=$request->get("ratting"); 
          }
          if($request->get("user_id") != ""){
                 $review->user_id=$user_id; 
          }
         $review->save();
        }else{
          $review = new Review();
          $review->product_id=""; 
          $review->res_id=$request->get("res_id"); 
          $review->review=$request->get("review"); 
          $review->user_id=$request->get("user_id"); 
          $review->ratting=$request->get("ratting"); 
          
          $review->save();
        }
        
        Session::flash('message',"Review Added Successfully"); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back()->with('error', 'Successfull...');
    }

    public function book_table(request $request){
       $user = User::where('id',Auth::id())->first();
       $user_id = $user->id;

       $this->validate($request,[
            'user_name'=>'required',
            'email'=>'required',
            'phone_no'=>'required',
            'book_date'=>'required',
            'book_time'=>'required'
        ]);

       $book = new BookTable();

         $book->res_id=$request->get("res_id"); 
          $book->user_id=$user_id; 
          $book->user_name=$request->get("user_name"); 
          $book->email=$request->get("email"); 
          $book->phone_no=$request->get("phone_no"); 
          $book->book_date=$request->get("book_date"); 
          $book->book_time=$request->get("book_time"); 
          
          $book->save();
        

        Session::flash('message',"Review Added Successfully"); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back()->with('error', 'Successfull...');
    }

    public function add_cart(request $request, $id){
        
        $user = User::where('id',Auth::id())->first();
        $user_id = $user->id;

        $pro = Product::where('id',$id)->first();
        
        $label_arr = $request->get('option_label');
        $name_arr = $request->get('option_name');
        $option_price = $request->get('option_price');
        
        if($name_arr != 0 || $name_arr != ""){

          $opt_price = array_sum($option_price);
          $label=implode("#", $label_arr);
          $name=implode("#", $name_arr);
          $price=implode("#", $option_price);
          
          if(CartData::where('product_id',$id)->where('user_id',$user_id)->where('res_id',$pro->user_id)->where('label',$label)->where('option_price',$price)->exists())
          {
            $pro = Product::where('id',$id)->first();
           
            $cart = CartData::where('product_id',$id)->where('user_id',$user_id)->where('label',$label)->where('option_price',$price)->first();
            $cart_data = CartData::where('product_id',$id)
                            ->where('user_id',$user_id)
                            ->where('label',$label)
                            ->where('option_price',$price)
                            ->update(array(
                              'qty'=>$cart->qty + 1,
                              'qty_price'=>$cart->qty_price + $opt_price + $pro->price));
              return $cart_data;
          }else{

            if(CartData::where('user_id',$user_id)->exists()){

                if(CartData::where('user_id',$user_id)->where('res_id',$pro->user_id)->exists()){
                  $pro = Product::where('id', $id)->first();

                  $cart = new CartData();
                  $cart->product_id=$id; 
                  $cart->res_id=$pro->user_id; 
                  $cart->user_id=$user_id; 
                  $cart->option=$name;
                  $cart->label=$label;
                  $cart->option_price=$price;
                  $cart->qty=1; 
                  $cart->tax=0.00; 
                  $cart->tax_name=''; 
                  $cart->price_product=$pro->price; 
                  $cart->qty_price= $opt_price + $pro->price; 
                  $cart->save();
                  $product = Product::where('id', $cart->product_id)->first();
                
                }else{

                  $delete = CartData::where('user_id',$user_id)->delete();
                  $opt_name = $request->opt_name;
       
                    $pro = Product::where('id', $id)->first();
                    $cart = new CartData();
                    $cart->product_id=$id; 
                    $cart->res_id=$pro->user_id; 
                    $cart->user_id=$user_id; 
                    $cart->option=$name;
                    $cart->label=$label;
                    $cart->option_price=$price;
                    $cart->qty=1; 
                    $cart->tax=0.00; 
                    $cart->tax_name=''; 
                    $cart->price_product=$pro->price; 
                    $cart->qty_price= $opt_price + $pro->price; 
                    $cart->save();
                    $product = Product::where('id', $cart->product_id)->first();
                    return $cart;
                }

            }else{
              $pro = Product::where('id', $id)->first();
              $cart = new CartData();
              $cart->product_id=$id; 
              $cart->res_id=$pro->user_id; 
              $cart->user_id=$user_id; 
              $cart->option=$name;
              $cart->label=$label;
              $cart->option_price=$price;
              $cart->qty=1; 
              $cart->tax=0.00; 
              $cart->tax_name=''; 
              $cart->price_product=$pro->price; 
              $cart->qty_price= $opt_price + $pro->price; 
              $cart->save();
              $product = Product::where('id', $cart->product_id)->first();
              return $cart;
            }
          }
        }else{

          $opt_price = "";

          $label="";
          $name="";
          $price="";

          if(CartData::where('product_id',$id)->where('user_id',$user_id)->where('res_id',$pro->user_id)->where('label',$label)->where('option_price',$price)->exists())
          {
            $pro = Product::where('id',$id)->first();
           
            $cart = CartData::where('product_id',$id)->where('user_id',$user_id)->where('label',$label)->where('option_price',$price)->first();
            $cart_data = CartData::where('product_id',$id)
                            ->where('user_id',$user_id)
                            ->where('label',$label)
                            ->where('option_price',$price)
                            ->update(array(
                              'qty'=>$cart->qty + 1,
                              'qty_price'=>$cart->qty_price + $pro->price));
              return $cart_data;
          }else{
             $pro = Product::where('id', $id)->first();
              $cart = new CartData();
              $cart->product_id=$id; 
              $cart->res_id=$pro->user_id; 
              $cart->user_id=$user_id; 
              $cart->option=$name;
              $cart->label=$label;
              $cart->option_price=$price;
              $cart->qty=1; 
              $cart->tax=0.00; 
              $cart->tax_name=''; 
              $cart->price_product=$pro->price; 
              $cart->qty_price= $pro->price; 
              $cart->save();
              $product = Product::where('id', $cart->product_id)->first();
              return $cart;
            }
        }   
    }
    
    public function product_option($id){

      $txt="";
      $data = ProductOption::where('product_id',$id)->get();

      if(CartData::where('product_id',$id)->where('user_id',Auth::id())->exists())
      {

        $cart = CartData::where('product_id',$id)->where('user_id',Auth::id())->orderBy('id','desc')->first();

        $cart_opt = explode("#",$cart->label);

        if(count($data)>0){
          $newArray = array();
          
            foreach($data as $value){
              $txt = $txt.'<div class="form-group col-md-12">
                                <label for="inputUserName" style="margin-left:40px; font-size:20px;">'.$value->name.'</label>
                                  <p style="float:right">minimum select '.$value->min_item_selection. ' items <br>maximum select '. $value->max_item_selection. ' items
                                  </p>
                                  <hr>
                                  <ul>';
                        
              $label = explode("#",$value->label);
              $price = explode("#",$value->price);
              $temp_array=array("label"=>$label,"price"=>$price);

              $j = 0;
              foreach ($label as $p){
                
                
                $txt = $txt.' 
                         <div class="custom-control form-check"  style="display:flex; justify-content:space-between">
                              <input  type="checkbox" name="'.$value->name.'" data-id="'.$price[$j].'" data-action="'.$value->name.'" id="'.$p.'"  class="form-check-input option_data common_selector"  value="'.$p.'" >
                              <label class="form-check-label" for="'.$p.'">'.$p.'
                              </label>
                              <div>
                              
                              <label style="margin-right:50px;"> ₹'.$price[$j].'
                              </label>
                             </div>
                            </div>';
                            $newArray[] = $p;
                            $j++;
              }
              $txt = $txt.'</ul>
                     </div>';
              $newArray[] = $value->name;
            }
        }
        $txt = $txt.'<div class="row" id="error_sts"> 
                </div> ';
        $new = implode(",",$newArray);

        if(Auth::id())
        {
          $txt = $txt.'<input type="hidden" name="option" id="name_opt" value="'.$new.'">
                      <div class="row" style="padding:10px;">
                          <div class="col-md-6 col-sm-6 mb-6" ">
                            <button type="button" class="btn btn-secondary btn-block btn-lg btn-gradient" data-bs-dismiss="modal">No</button>
                          </div>

                          <div class="col-md-6 col-sm-6 mb-6 " style="margin-left:248px; margin-top:-49px;">
                            <button type="" onclick="click_data('.$id.')"   class="btn btn-primary btn-block btn-lg btn-gradient" href="#" >Add</button>
                          </div>
                      </div>';
        } 
        else 
        {  
          $txt = $txt.'<div class="row">
                          <div class="col-md-6 col-sm-6 mb-6 ">
                            <button type="button" class="btn btn-secondary btn-block btn-lg btn-gradient" data-bs-dismiss="modal">No</button>
                          </div>
                          <div class="col-md-6 col-sm-6 mb-6 ">
                            <button data-bs-toggle="modal" data-bs-target="#add_cart" class="btn btn-primary btn-block btn-lg btn-gradient" type="submit" style="color:white;">ADD</button>
                          </div>
                         
                        </div>'; 
        }
      }
      else
      {
          if(count($data)>0)
          {
            $newArray = array();
             $opt_id_arr = array();

             if (Session::has('error')){

                  $txt = $txt.' <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show" style="margin: 20px; font-weight:500">
                                  <ul>
                                      <li>';
                          
                  Session::get('error');

                  $txt = $txt.'</li>
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>';
                }

              foreach($data as $value)
              {
                $txt = $txt.' <div class="form-group col-md-12">
                                <label for="inputUserName" style="margin-left:40px; font-size:20px;">'.$value->name.'</label>
                                  <p style="float:right">minimum select '.$value->min_item_selection. ' items <br>maximum select '. $value->max_item_selection. ' items
                                  </p>
                                  <hr>
                                  <ul>';
                          
                $label = explode("#",$value->label);
                $price = explode("#",$value->price);
                $temp_array=array("label"=>$label,"price"=>$price);

                $j = 0;
                foreach ($label as $p)
                {
                  $txt = $txt.' 
                            <div class="custom-control form-check"  style="display:flex; justify-content:space-between">
                                <input  type="checkbox" name="'.$value->name.'" data-id="'.$price[$j].'" data-action="'.$value->name.'" id="'.$p.'"  class="form-check-input option_data common_selector"  value="'.$p.'">
                                <label class="form-check-label" for="'.$p.'">'.$p.'
                                </label>
                                <div>
                                
                                <label style="margin-right:50px;"> ₹'.$price[$j].'
                                </label>
                               </div>
                              </div>';
                            
                              $j++;
                }
                $txt = $txt.'</ul>
                        </div>';
                $newArray[] = $value->name;
                $opt_id_arr[] = $value->id ;
              }
          }
           $txt = $txt.'<div class="row" id="error_sts"> 
                </div> ';
          $new = implode(",",$newArray);
          $id_arr = implode(",", $opt_id_arr);
          if(Auth::id())
          {
            $txt = $txt.' <input type="hidden" name="option" id="name_opt" value="'.$new.'">
            <input type="hidden" name="option" id="opt_id" value="'.$id_arr.'">
                  <div class="row" style="padding:10px;">
                    <div class="col-md-6 col-sm-6 mb-6 ">
                      <button type="button" class="btn btn-secondary btn-block btn-lg btn-gradient" data-bs-dismiss="modal">No</button>
                    </div>
                    <div class="col-md-6 col-sm-6 mb-6 " style="margin-left:250px; margin-top:-48px;">
                      <button type="button" onclick="click_data('.$id.')"   class="btn btn-primary btn-block btn-lg btn-gradient" type="submit" href="#" >ADD</button>
                    </div>
                  </div>';
          } 
          else 
          {  
            $txt = $txt.'<div class="row">
                          <div class="col-md-6 col-sm-6 mb-6 ">
                            <button type="button" class="btn btn-secondary btn-block btn-lg btn-gradient" data-bs-dismiss="modal">No</button>
                          </div>
                          <div class="col-md-6 col-sm-6 mb-6 ">
                            <button data-bs-toggle="modal" data-bs-target="#add_cart" class="btn btn-primary btn-block btn-lg btn-gradient" type="submit" style="color:white;">ADD</button>
                          </div>
                         
                        </div>'; 
          }
      }
      return  $txt;
    }

    public function check_option_data(request $request){
       
        $arr = $request->get('count_arr');
        $characters = json_decode($arr);

        $option_name = $request->option_name;
        $name_arr =(explode(",",$option_name));

        $pro_id = $request->pro_id;
        $name_arr =(explode(",",$option_name));
        
       $success_arr=array();
        foreach($characters as $key => $value){

          $option_data=ProductOption::where('product_id',$pro_id)->where('name',$key)->first();
           
           if($option_data){

            if($value == 0 ){
              $success_arr[$key]=$value;
            }
            else
            {
                if($option_data->min_item_selection <= $value && $option_data->max_item_selection >= $value){

                    $success_arr[$key]=$value;
                    
                  }else{
                    $error =  "Plese select Min = ".$option_data->min_item_selection." and max = ".$option_data->max_item_selection." item of ".$key."\r\n";
                    $success_arr="";
                    break;
                  }
              }
           }
        }
        $arr_sts=array();
        if($success_arr == ""){
           $arr_sts["status"]=0;
           $arr_sts["data"]=$success_arr;
           $arr_sts["error"]=$error;
        }else{
          $arr_sts["status"]=1;
          $arr_sts["data"]=$success_arr;
        }
        return json_encode($arr_sts);
    }

    public function remove_cart(request $request, $id){

        $user = User::where('id',Auth::id())->first();
       
        $cart = CartData::where('id',$id)->first();
        
        $product = Product::where('id',$cart->product_id)->first();

        if($cart->qty == 1){
            $data = CartData::where('id',$id)->delete();
        }else{
            $cart = CartData::where('id',$id)->first();

            $opt=(explode("#",$cart->option_price));
            $opt_array = implode(",",$opt);
            $opt_price = array_sum( explode( ',', $opt_array ) );

            $data = CartData::where('id',$id)->update(array(
                         'qty'=>$cart->qty - 1,
                          'qty_price'=>$cart->qty_price - $opt_price - $product->price));
        }
        
       return $data; 
    }

    public function add(request $request, $id){

        $user = User::where('id',Auth::id())->first();
        $cart = CartData::where('id',$id)->first();

        $opt=(explode("#",$cart->option_price));
        $opt_array = implode(",",$opt);
        $opt_price = array_sum( explode( ',', $opt_array ) );
        $total_price = $opt_price + $cart->price_product;

        $product = Product::where('id',$cart->product_id)->first();

        $data = CartData::where('id',$id)->update(array(
                         'qty'=>$cart->qty + 1,
                         'qty_price'=>$cart->qty_price + $opt_price + $cart->price_product));  
        return $data;
    }

    public function add_out_opt(request $request, $id){
        
        $user = User::where('id',Auth::id())->first();
        $user_id = $user->id;
        $pro = Product::where('id',$id)->first();

        if(CartData::where('product_id',$id)->where('res_id',$pro->user_id)->where('user_id',$user_id)->exists())
        {
          $pro = Product::where('id',$id)->first();
         
          $cart = CartData::where('product_id',$id)->where('user_id',$user_id)->first();
          $cart_data = CartData::where('product_id',$id)
                          ->where('user_id',$user_id)
                          ->update(array(
                            'qty'=>$cart->qty + 1,
                            'qty_price'=>$cart->qty_price + $pro->price));
          return $cart_data;
        }else{
          if(CartData::where('user_id',$user_id)->exists()){
              if(CartData::where('res_id',$pro->user_id)->where('user_id',$user_id)->exists()){
                $pro = Product::where('id', $id)->first();
                $cart = new CartData();
                $cart->product_id=$id; 
                $cart->res_id=$pro->user_id; 
                $cart->user_id=$user_id; 
                $cart->option="";
                $cart->label="";
                $cart->option_price="";
                $cart->qty=1; 
                $cart->tax=0.00; 
                $cart->tax_name=''; 
                $cart->price_product=$pro->price; 
                $cart->qty_price=$pro->price; 
                $cart->save();
                $product = Product::where('id', $cart->product_id)->first();
               return $cart;
             }else{
               $delete = CartData::where('user_id',$user_id)->delete();
               $pro = Product::where('id', $id)->first();
                $cart = new CartData();
                $cart->product_id=$id; 
                $cart->res_id=$pro->user_id; 
                $cart->user_id=$user_id; 
                $cart->option="";
                $cart->label="";
                $cart->option_price="";
                $cart->qty=1; 
                $cart->tax=0.00; 
                $cart->tax_name=''; 
                $cart->price_product=$pro->price; 
                $cart->qty_price=$pro->price; 
                $cart->save();
                $product = Product::where('id', $cart->product_id)->first();
               return $cart;
             }
          }else{
            $pro = Product::where('id', $id)->first();
            $cart = new CartData();
            $cart->product_id=$id; 
            $cart->res_id=$pro->user_id; 
            $cart->user_id=$user_id; 
            $cart->option="";
            $cart->label="";
            $cart->option_price="";
            $cart->qty=1; 
            $cart->tax=0.00; 
            $cart->tax_name=''; 
            $cart->price_product=$pro->price; 
            $cart->qty_price=$pro->price; 
            $cart->save();
            $product = Product::where('id', $cart->product_id)->first();
            return $cart;
          }
          
          
        } 
    }
    
    public function checkout(){
      $data['cart'] = CartData::where('user_id',Auth::id())->get();

      if(CartData::where('user_id',Auth::id())->groupBy('res_id')->exists()){
        $data['cart_res'] = CartData::where('user_id',Auth::id())->groupBy('res_id')->first();
      }else{
        $data['cart_res'] = Product::first();
      }
      
      $data['user'] = User::where('id',Auth::id())->first();

      return view('front.checkout',$data);
    }

    public function add_address($id){
      $data['address'] = Addresses::where('user_id',$id)->get();
     
      $data['user'] = User::where('id',$id)->first();
      return view('front.add_address',$data);
    }

    public function add_address_data(request $request){
     
     $user = User::where('id',Auth::id())->first();
     $user_id = $user->id;

     $address = new Addresses();
        $address->user_id=$user_id;
        $address->address=$request->address;
        $address->floor=$request->floor;
        $address->lat=$request->lat;
        $address->long=$request->long;
        $address->name=$request->add_name;
        $address->city=$request->city;
        $address->pincode=$request->pincode;
        $address->state=$request->state;
        $address->mobile_no=$request->mobile_no;
        $address->area=$request->area;
        $address->country=$request->country;
        $address->save();
        
        return $address; 
    }

    public function add_current_add(request $request,  $id){
     
      $user = User::where('id',Auth::id())->first();
      $user_id = $user->id;
      $add = Addresses::where('id',$id)->first();
      $address = $add->address;
      
      $user = User::where('id',$user_id)
                      ->update(array(
                            'shipping_address'=>$address,
                            'lat'=>$add->lat,
                            'long'=>$add->long,
                            'area'=>$add->area,
                            'city'=>$add->city,
                            'country'=>$add->country,
                            'pincode'=>$add->pincode,
                          ));
      if($user){
        return 1;
      }else{
        return 2;
      }
    }

    public function add_order(request $request){

      $user = User::where('id',Auth::id())->first();
      $user_id = $user->id;
      
      $shipping_method=$request->shipping_method;
      if($shipping_method == "Home"){
          $shipping_method = 1;
      }else if($shipping_method == "pick_up"){
          $shipping_method = 0;
      }
      $total=$request->total;
      $tax=$request->tax;
      $delivery=$request->delivery;
      $dis=$request->dis;
      $dis_amount=$request->dis_amount;
      $pay=$request->pay;
      $note=$request->note;
      $add_id=$request->add_id;
      $coupon=$request->coupon;
      $cart_res=$request->cart_res;
      
      $order = New Order();
      $order->user_id=$user_id;
      $order->shipping_method=$shipping_method;
      $order->payment_method=0;
      $order->user_id=$user_id;
      $order->notes=$note;
      $order->sub_total=$pay;
      $order->total=$total;
      $order->charges_id="";
      $order->is_completed=0;
      $order->user_address_id=$add_id;
      $order->delivery_charge=$delivery;
      $order->status=0;
      $order->tax=$tax;
      $order->seller_id=$cart_res;
      $order->order_no=$user_id;
      $order->orderplace_datetime=date("Y-m-d h:i:s");
      $order->coupon_code=$coupon;
      $order->coupon_price=$dis_amount;
      $order->save();
      $order_id = $order->id;           
      $order->order_no=$order_id."#".mt_rand(100000, 999999);
      $order->save();
     
      $data = CartData::where('user_id',$user_id)->get();

      foreach($data as $val){
        
        $arr = $val->option;
        $opt = (str_replace("#","-",$arr,$i));

        $exlabel = $val->label;
        $label = (str_replace("#","-",$exlabel,$i));

        $exoption_price = $val->option_price;
        $option_price = (str_replace("#","-",$exoption_price,$i));

          $order_data = New OrderData();
          $order_data->order_id = $order->id;
          $order_data->product_id =$val->product_id;
          $order_data->quantity =$val->qty;
          $order_data->price =$val->price_product;
          $order_data->total_amount =$val->qty_price;
          $order_data->per_product_seller_price =000;
          $order_data->per_product_commission_price =000;
          $order_data->tax_name =$val->tax_name;
          $order_data->tax_charges =$val->tax;
          $order_data->option_name =$opt;
          $order_data->label =$label;
          $order_data->option_price =$option_price;
          $order_data->delivery_charges =$order->delivery_charge;
          $order_data->seller_id =$order->seller_id;
          $order_data->save();
         
        }
        return $order;
    }

    public function add_discount(request $request){
     
      $code=$request->get("code");
      $total=$request->get("total");

      $delivery_charges=$request->get("delivery_charges");
     
      $user = User::where('id',Auth::id())->first();
      $user_id = $user->id;
      $cart = CartData::where('user_id',Auth::id())->get();
      
      $admin = Setting::first();
        $delivery_charges = $admin->delivery_charges;
        $total_amount = $total;

        $pay_amount = $total + $delivery_charges;
      
      $arr = array();
      

      $res_id = CartData::where('user_id',Auth::id())->groupBy('res_id')->first();
      $coupon = coupon::where('code',$code)->first();
      if($coupon){
        
          if($coupon->user_id == $res_id->res_id){

            $order=Order::where("coupon_code",$code)->where('user_id','!=',$user_id)->groupBy('user_id')->get();

            $orderuser=DB::table('order_data')
                           ->select('order_data.id')
                           ->join('order_record', 'order_record.id', '=', 'order_data.order_id')
                           ->where('order_record.coupon_code',$code)
                           ->where('order_record.user_id',$user_id)
                           ->get();
                if($coupon->usage_limit_per_coupon != "" && ($coupon->usage_limit_per_coupon<count($order)))
            {
               $array = json_encode(array("error"=>"Coupon Limit Over"));
              return $array;
            }
            elseif($coupon->usage_limit_per_customer!=""&&($coupon->usage_limit_per_customer<=count($orderuser)))
            {
              $array = json_encode(array("error"=>"Your Coupon Limit Over"));
              return $array;
            }
            elseif($coupon->minmum_spend!="" && $coupon->minmum_spend > $request->get("total"))
            {
              $array = json_encode(array("error"=>"Not Vaild Coupon,total less than minimum amount of coupon"));
              return $array;
            }
            elseif($coupon->maximum_spend != "" && $coupon->maximum_spend <= $request->get("total"))
            {
              $array = json_encode(array("error"=>"Not Valid Coupon,total greater than maximum amount of coupon"));
              return $array;
            }else if($coupon->coupon_on == 0)
            {
                $product_id = $coupon->product;
                $products= explode(",",$product_id);
                
                foreach($cart as $val){
                    if (in_array($val->product_id, $products))
                    {
                      
                      if($coupon->discount_type == 0){
                          $discount = $coupon->value;
                          $total_amount = $total_amount - $discount + $delivery_charges;
                          $discount = $coupon->value.".00";
                          $amount = $total_amount.".00";
                          $array = json_encode(array("discount"=>" ","total_amount"=>$discount,"pay_amount"=>$amount));
                          return $array;
                      }else if($coupon->discount_type == 1){
                          $discount = $coupon->value;
                          
                          $total_dis =$discount * $total_amount / 100;
                          $discount = $coupon->value."%";
                          $amount = $total_dis;
                          $pay_amount = $total_amount - $total_dis + $delivery_charges;
                          $pay = $pay_amount;
                          $array = json_encode(array("discount"=>$discount,"total_amount"=>$amount,"pay_amount"=>$pay));
                          return $array;
                      }
                    }
                    else
                    {
                      echo "Coupon Not Available For This Product..";
                    }
                }
                

             }
             else if($coupon->coupon_on == 1)
             {
                $categories = $coupon->categories;
                $coupon_products= explode(",",$categories);
                foreach($cart as $val){

                  $product = $val->product_id;
                  $product_dtl = Product::where('id',$product)->get();

                  foreach ($product_dtl as $cat) {
                    if (in_array($cat->category, $coupon_products))
                    {
                      if($coupon->discount_type == 0){
                          $discount = $coupon->value;
                          $total_amount = $total_amount - $discount + $delivery_charges;
                          $discount = $coupon->value.".00";
                          $amount = $total_amount.".00";

                          $array = json_encode(array("discount"=>" ","total_amount"=>$discount,"pay_amount"=>$amount));
                          
                          return $array;

                      }else if($coupon->discount_type == 1){
                          $discount = $coupon->value;
                          $total_amount =$discount * $total_amount / 100;
                          $discount = $coupon->value."%";
                          $amount = $total_amount;

                          $array = json_encode(array("discount"=>$discount,"total_amount"=>$amount));
                          return $array;
                      }
                    }
                    else
                    {
                      $array = json_encode(array("error"=>"Coupon Not Available For This Product.."));
                      return $array;
                    }
                  }
                }
             }
          }
      }else{
        $error = "Coupon Is Not Valid...";
        $amount = "0.00";
        $pay_amount = $pay_amount.".00";
          $array = json_encode(array("total_amount"=>$amount,"error"=>$error,"pay_amount"=>$pay_amount));
        return $array;
      }
               
      // return redirect('checkout'); 
    }

    public function repeat_opt($id){
      $user = User::where('id',Auth::id())->first();
      $user_id= $user->id;
      $txt ="";

      $product_id = $id;

      $txt = $txt.'<div class="col-md-6 col-sm-6 mb-1 ">
                      <a  id="add" class="btn btn-primary btn-block btn-lg btn-gradient" onclick="product_option('.$product_id.')"  type="button" data-bs-toggle="modal" data-bs-target="#modal" >Add</a>
                    </div>
                    <div class="col-md-6 col-sm-6 mb-1 ">
                        <a  id="add" class="btn btn-secondary btn-block btn-lg btn-gradient" type="button" onclick="repeat('.$product_id.')" >Repeart</a>
                    </div>';

      return  $txt;
    }

    public function repeat($id){
      $user = User::where('id',Auth::id())->first();
      $user_id = $user->id;
      $pro = Product::where('id',$id)->first();
      $opt = CartData::where('product_id',$id)->where('user_id',$user_id)->orderBy('id','desc')->first();

      $cart = CartData::where('id',$opt->id)->first();
      // $price = $cart->price_product;
      // $opt_price = $cart->qty_price - $price;

      $data = CartData::where('id',$opt->id)->update(array(
                         'qty'=>$cart->qty + 1,
                         'qty_price'=>$cart->qty_price + $cart->qty_price));
      return $data; 
    }

    public function repeat_out_opt($id){
      $data['cart'] = CartData::where('user_id',Auth::id())->get();
      $data['cart_res'] = CartData::where('user_id',Auth::id())->groupBy('res_id')->first();
      $data['user'] = User::where('id',Auth::id())->first();
      return view('front.checkout',$data);
    }

    public function orders(){
        return view("front.orders");
    } 

    public function cod_payment(Request $request){

      $id = $request->id;
      $update = Order::where('id',$id)->update(array(
                         'payment_method'=>3,));
      $delete = CartData::where('user_id',Auth::id())->delete();
      if($update){
         return 1;
      }else{
        return 0;
      }
    }

    public function my_account(){

        $data=User::where('id',Auth::id())->first();
        return view("front.my_account", compact('data'));
    }

    public function delete_address(Request $request){
      $delete = Addresses::where('id',$request->address_id)->delete();

      return redirect('my_account')->with('error', 'Successfull...');
    }

    public function address_list(){
      $user = User::where('id', Auth::id())->first();
      $address = Addresses::where('user_id',auth::id())->get();
      return view('front.address-list',compact('address','user'));
    }

    public function order_list(){
      $user = User::where('id', Auth::id())->first();
      $order = Order::where('user_id',auth::id())->get();
      return view('front.order-list',compact('order','user'));
    }

    public function edit_address($id){
      $address = Addresses::where('id',$id)->first();
      $user = User::where('id',Auth::id())->first();
      return view('front.edit-address',compact('address','user'));
    }
    
    public function edited_address(request $request){
       
      $user = User::where('id',Auth::id())->first();
      $array = array();
      $array['user_id'] = $user->id;
      $array['address'] = $request->address ;
      $array['floor'] = $request->floor ;
      $array['lat'] = $request->lat ;
      $array['long'] = $request->lng ;
      $array['name'] = $request->add_name ;
      $array['city'] = $request->city ;
      $array['pincode'] = $request->pincode ;
      $array['state'] = $request->state ;
      $array['mobile_no'] = $request->mobile_no ;
      $array['area'] = $request->area ;
      $array['country'] = $request->country ;

      $update = Addresses::where('id',$request->address_id)->update($array);
      return $update;
    }

    public function invoice($id){

        $data = Order::where('id',$id)->first();
        $user = User::where('id',Auth::id())->first();
        $res = User::where('id',$data->seller_id)->first();
        $order = OrderData::where('order_id',$data->id)->get();
        foreach ($order as  $value) {
          $products = Product::where('id',$value->product_id)->first();
          $value->pro_name = $products->name;
        }
         $documentFileName = "invoice.pdf";
 
        // Create the mPDF document
        $document = new PDF( [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '3',
            'margin_top' => '20',
            'margin_bottom' => '20',
            'margin_footer' => '2',
        ]);     
 
        // Set some header informations for output
        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$documentFileName.'"'
        ];
 
        // Write some simple Content
        $document->WriteHTML('
          <style>
          table{
            width:100%;
          }
          h6{
            font-weight:bold;
            font-size:20px;

          }
           h5{
              background-color: DodgerBlue;
              border: none;
              color: white;
              padding: 5px 15px;
              cursor: pointer;
              font-size: 18px;;
              width:10%;  
              float:right;     
            }
          </style>
          <section class="breadcrumb-osahan pt-5 pb-5 bg-dark position-relative text-center">
            <h1 class="text-white">Invoice</h1>
            
             
            <h6 class="text-white-50">'.$data->order_no.'</h6>
          </section>
          <section class="section pt-5 pb-5">
            <div class="container">
              <div class="row">
                <div class="col-md-8 mx-auto">
                  <div class="p-5 osahan-invoice bg-white shadow-sm">
                    <div class="row mb-5 pb-3 ">
                      <div class="col-md-8 col-10">
                        <h3 class="mt-0">Thanks for choosing <strong class="text-secondary">Osahan Eat</strong>, '.$user->first_name.'! Here are your order details: </h3>
                      </div>
                     
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-black">Order No: <strong>'.$data->order_no.'</strong></p>
                        <p class="mb-1">Order placed at: <strong>'. date("d-m-Y h:i", strtotime($data->orderplace_datetime)).'</strong>
                        </p>
                        <!-- <p class="mb-1">Order delivered at: <strong>'. date("d-m-Y h:i", strtotime($data->complete_datetime)).'</strong>
                        </p> -->
                        <p class="mb-1">Order Status: <strong class="text-success">Delivered</strong>
                        </p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-black">Delivery To:</p>
                        <p class="mb-1 text-primary">
                          <strong>'.$user->first_name.'</strong>
                        </p>
                        <p class="mb-1">'.$user->shipping_address.'</p>
                      </div>
                    </div>
                    <div class="row mt-5">
                      <div class="col-md-12">
                        <p class="mb-1">Ordered from:</p>
                        <h6 class="mb-1 text-black">
                          <strong>'.$res->first_name.'</strong>
                        </h6>
                        <p class="mb-1">'.$res->address.'</p>
                        <table class="table mt-3 mb-0 table-bordered" >
                          <thead class="thead-light">
                            <tr>
                              <th class="text-black font-weight-bold" scope="col" style="border: 1px solid black;">Item Name</th>
                              <th class="text-right text-black font-weight-bold" scope="col" style="border: 1px solid black;">Quantity</th>
                              <th class="text-right text-black font-weight-bold" scope="col" style="border: 1px solid black;">Price</th>
                            </tr>
                          </thead>
                          <tbody>');
                          foreach($order as $val){

                          
                          $document->WriteHTML('
                           
                            <tr >
                              <td style=" border: 1px solid black;">'.$val->pro_name.' <br>('.$val->label.')</td>
                              <td class="text-right" style="border: 1px solid black;">'.$val->quantity.'</td>
                              <td class="text-right" style="border: 1px solid black;">₹'.number_format($val->total_amount,2).'</td>
                            </tr>');
                        }
                            $document->WriteHTML('

                            <tr>
                              <td class="text-right" colspan="2">Item Total:</td>
                              <td class="text-right">₹'.number_format($data->total,2).'</td>
                            </tr>
                            <tr>
                              <td class="text-right" colspan="2">Tax:</td>
                              <td class="text-right"> ₹'.number_format($data->tax,2).'</td>
                            </tr>
                            <tr>
                              <td class="text-right" colspan="2">Delivery Charges:</td>
                              <td class="text-right"> ₹'.number_format($data->delivery_charge,2).'</td>
                            </tr>
                            <tr>
                              <td class="text-right" colspan="2">Discount Applied ('.$data->coupon_code.'):</td>
                              <td class="text-right">₹'.number_format($data->coupon_price,2).'</td>
                            </tr><hr>
                            <tr>
                              <td class="text-right" colspan="2">
                                <h6 class="text-success">Grand Total:</h6>
                              </td>
                              <td class="text-right">
                                <h6 class="text-success"> ₹'.number_format($data->sub_total,2).'</h6>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>');
                  
        // Save PDF on your public storage 
        Storage::disk('public')->put($documentFileName, $document->Output($documentFileName, "S"));
         
        // View Invoice And Then Yopu Can Download Download
        // return Storage::disk('public')->download($documentFileName, 'Request', $header); 

         //Direct Download
        $file= storage_path(). "/app/public/invoice.pdf";
            $headers = array(
                      'Content-Type: application/pdf',
                    );
        return Response::download($file, 'invoice.pdf', $headers);
    } 
    
    public function edit_profile(Request $request){
        $getusers = User::where("email",$request->get("name"))->where("id","!=",Auth::id())->first();
        if($getusers){
            Session::flash('message',__('message.Email Id Already Exist')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }else{
            $store = User::find(Auth::id());
            $img_url=$store->profile_pic;
            $rel_url=$store->profile_pic;
        }
        if ($request->hasFile('image')) 
        {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $folderName = '/front/img/user';
            $picture = "profile_".time() . '.' . $extension;
            $destinationPath = public_path() . $folderName;
            $request->file('image')->move($destinationPath, $picture);
            $img_url =$picture;                
            $image_path = public_path() ."/front/img/user/".$rel_url;
                if(file_exists($image_path)&&$rel_url!="") {
                    try {
                         unlink($image_path);
                    }
                    catch(Exception $e) {
                      
                    }                        
                }
        }
        $store->password = $request->get("password");
        $store->email = $request->get("email");
        $store->profile_pic=$img_url;
        $store->save();
        Session::flash('message',__('message.Profile Update Successfully')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
        
    }
    
    public function privacy_front_app(){
        $data=About::find(1);
        $lang = Lang_core::all();
        if(Session::get('locale')==""){
            Session::put('locale','en');
        }
        $setting=Setting::find(1);
        return view('front.privacy',compact('data','setting','lang'));
    }
    
    public function accountdeletion(){
        $data=About::find(1);
        $lang = Lang_core::all();
        if(Session::get('locale')==""){
            Session::put('locale','en');
        }
        $setting=Setting::find(1);
        return view('front.accountdeletion',compact('data','setting','lang'));
    }
    
    public function about(){
      $data=About::find(1);
       $lang = Lang_core::all();
        if(Session::get('locale')==""){
            Session::put('locale','en');
        }
      $setting=Setting::find(1);
      return view('admin.about',compact('data','setting','lang'));
    }
    
    public function admin_privacy(){
      $data=About::find(1);
      $setting=Setting::find(1);
      $lang = Lang_core::all();
        if(Session::get('locale')==""){
            Session::put('locale','en');
        }
      return view('admin.terms',compact('data','setting','lang'));
    }
    
    public function Privacy(){
      $data=About::find(1);
      $setting=Setting::find(1);
      $lang = Lang_core::all();
        if(Session::get('locale')==""){
            Session::put('locale','en');
        }
      return view('user.terms',compact('data','setting','lang'));
    }
    
    public function app_privacy(){
      $data=About::find(1);
      $setting=Setting::find(1);
      $lang = Lang_core::all();
        if(Session::get('locale')==""){
            Session::put('locale','en');
        }
      return view('admin.privecy-app',compact('data','setting','lang'));
    }
    
    public function data_deletion(){
      $data=About::find(1);
      $setting=Setting::find(1);
      $lang = Lang_core::all();
        if(Session::get('locale')==""){
            Session::put('locale','en');
        }
      return view('admin.data-deletion',compact('data','setting','lang'));
    }
    
   public function edit_about(Request $request){
      $data=About::find(1);
       $setting=Setting::find(1);
       $data->about = $request->get('about');
       $data->save();
      return redirect('admin/about');
    }
    
    public function edit_terms(Request $request){
      $data=About::find(1);
      $setting=Setting::find(1);
      $data->trems = $request->get('trems');
       $data->save();
      return redirect('admin/Terms_condition');
    }
    
    public function edit_app_privacy(Request $request){
      $data=About::find(1);
      $setting=Setting::find(1);
      $data->privacy = $request->get('privacy');
       $data->save();
      return redirect('admin/app_privacy');
    }
    
    public function edit_data_deletion(Request $request){
      $data=About::find(1);
      $setting=Setting::find(1);
      $data->data_deletion = $request->get('data_deletion');
       $data->save();
      return redirect('admin/data_deletion');
    }
    
}
