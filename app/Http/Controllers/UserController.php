<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use App\Models\User;
use App\Models\Setting;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\OrderResponse;
use App\Models\OrderData;
use App\Models\Order;
use App\Models\Categories;
use App\Models\Lang_core;
use App\Models\FileMeta;
use DataTables;
Use Image;
Use Mail;
use Hash;
use DB;
use Auth;
class UserController extends Controller {
     
    public function __construct() {
         parent::callschedule();
    }
    
    public function storewishlist(Request $request){
      $checkuser=Wishlist::where("product_id",$request->get("product_id"))->where("user_id",$request->get("user_id"))->get();
      if(count($checkuser)==0){
           $wish=new Wishlist();
           $wish->user_id=$request->get("user_id");
           $wish->product_id=$request->get("product_id");
           $wish->save();
      }
      $totalwish=Wishlist::where("user_id",$request->get("user_id"))->get();
      return count($totalwish);
    }

    public function userdelete($id){
        $user=User::find($id);
        $user->delete();
        $order=Order::where("user_id",$id)->get();
        foreach ($order as $k) {
            $order=OrderResponse::where("order_id",$k->id)->delete();
            $order=OrderData::where("order_id",$k->id)->delete();
            $k->delete();
        }
         $delreview=Review::where("user_id",$id)->delete();
         Session::flash('message',__('messages_error_success.user_del')); 
         Session::flash('alert-class', 'alert-success');
         return redirect()->back();
    }

    public function saveaddress(Request $request){
        $fields=$request->get("fields");
       $store=Auth::user();
       $store->$fields=$request->get('address');
       $store->save();
       return "done";
    }

    public function deletewishlist(Request $request){
       $checkuser=Wishlist::where("product_id",$request->get("product_id"))->where("user_id",$request->get("user_id"))->delete();
       $getwish=Wishlist::with('productdata')->where("user_id",$request->get("user_id"))->get();
     
       $txt='<tr class="pro-heading" style="background:'.Session::get("site_color").' !important"><th>'.__("messages.del").'</th><th>'.__("messages.images").'</th><th>'.__("messages.product").'</th><th>'.__("messages.stock_status").'</th><th>'.__("messages.price").'</th><th></th></tr>';
       if(count($getwish)!=0){
           foreach($getwish as $mw){
                   $txt=$txt.'<tr><td class="Delete-icon"><a href="javascript:;" onclick="deletewish('.$mw->product_id.')"><i class="fa fa-trash-o" aria-hidden="true"></i></a><span>'.__('messages.del').':</span></td><td class="cart-img"><img src='.asset('public/upload/product').'/'.$mw->productdata->basic_image.'><span>'.__('messages.images').' :</span></td><td class="place-text"><div class="text-a"><span>'.__('messages.product').' :</span><h1>'.$mw->productdata->name.'</h1></div></td><td class="Stock-text">';
                   if($mw->productdata->stock=='0'){
                       $txt=$txt.__("messages.outstock");
                   }
                   else{
                       $txt=$txt.__("messages.in_stock");
                   }
                   $txt=$txt.'<span>'.__('messages.stock_status').':</span></td><td class="price">'.Session::get('currency').$mw->productdata->price.'<span>'.__('messages.price').':</span></td><td class="add"><a onclick="addwishtocart('.$mw->product_id.',' . "'" . $mw->productdata->name. "'" . ',1,'.$mw->productdata->price.')" style="border-color:'.Session::get("site_color").'!important">'.__('messages.add_to_cart').'</a></td></tr>';
           }
       }
       else{
           $txt=$txt.'<tr><td colspan="6" class="emptywish">'.__('messages.Your wishlist is currently empty!').'</td></tr>';
       }
       $data=array("content"=>$txt,"total"=>count($getwish));
       return json_encode($data);
    }
    
    public function index(){
       $lang = Lang_core::all();
       return view("admin.user.default")->with("lang",$lang);
    }
    
    public function indexadmin(){
      $lang = Lang_core::all();
      return view("admin.user.admin")->with("lang",$lang);
    }

    public function saveuserreview(Request $request){
        $user=Auth::user();
        $store=new Review();
        $store->product_id=$request->get("product_id");
        $store->user_id=$user->id;
        $store->ratting=$request->get("ratting");
        $store->review=$request->get("review");
        $store->save();
        return __('messages_error_success.review_success');

    }

    public function userlogin(Request $request){
          $setting=Setting::find(1);
          $checkuser=User::where("email",$request->get("email"))->where("password",$request->get("password"))->first();
          if($checkuser){
               
                Auth::login($checkuser, true);
                $data=Auth::user();
                if($request->get("rem_me")==1){
                    setcookie('user_email', $request->get("email"), time() + (86400 * 30), "/");
                    setcookie('password',$request->get("password"), time() + (86400 * 30), "/");
                   setcookie('rem_me',1, time() + (86400 * 30), "/");
               } 
                return "done";
          }
        else{
            return __('messages_error_success.login_error');
        } 
    }

    public function userregister(Request $request){
        $setting=Setting::find(1);    
        $checkemail=User::where("email",$request->get("email"))->first();
        if(empty($checkemail)){
           DB::beginTransaction();
              try {
                    $user=new User();
                    $user->first_name=$request->get("first_name");
                    $user->email=$request->get("email");
                    $user->password=$request->get("password");
                    $user->is_email_verified='1';
                    $user->address=$request->get("address");
                    $user->phone=$request->get("phone");
                    $user->login_type=1;
                    $user->user_type='1';                                    
                    $user->save();
                    try {
                        if($setting->customer_reg_email=='1'){
                            Mail::send('email.register_confirmation', ['user' => $user], function($message) use ($user){
                                                     $message->to($user->email,$user->first_name)->subject('shop on');
                                    });
                        }
                    } catch (\Exception $e) {
                    }
                    DB::commit();
                    return "done";
              }
              catch (\Exception $e) {
                   DB::rollback();
                   return __('messages_error_success.error_code');      
              }          
        }
        else{
            return __('messages_error_success.email_already_error');
        }
    }

    public function userdatatable($id){
         $user =User::where('user_type',$id)->orderBy('id','DESC')->get();
         return DataTables::of($user)
            ->editColumn('id', function ($user) {
                return $user->id;
            })
            ->editColumn('name', function ($user) {
                return $user->first_name;
            })
            ->editColumn('email', function ($user) {
                return $user->email;
            })
            ->editColumn('phone', function ($user) {
                return $user->phone;
            })            
            ->editColumn('action', function ($user) {
               $changestatus=url('admin/changeuserstatus',array('id'=>$user->id));
                $deleteuser=url('admin/userdelete',array('id'=>$user->id));
               if($user->is_active=='1'){
                    $color="green";
                 }
                 else{
                    $color="red";
                 }
                 if(Session::get("is_demo")=='1'){
                      $return = '<a onclick="edituser('.$user->id.')"  rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#edituser"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a>
                             <a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>
                             <a type="button" onclick="disablebtn()" class="m-b-10 m-l-5"  style="margin-right: 10px;"><i class="fa fa-ban f-s-25" style="font-size: x-large;color:'.$color.'"></i></a>';
                     
                 }else{
                      $return = '<a onclick="edituser('.$user->id.')"  rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#edituser"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a>
                             <a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>
                             <a href="'.$changestatus.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-ban f-s-25" style="font-size: x-large;color:'.$color.'"></i></a>';
                     
                 }
                
                 return $return;              
            })           
            ->make(true);
    }

    public function adduser(Request $request){  
        $setting=Setting::find(1);    
        $checkemail=User::where("email",$request->get("email"))->first();
        if(empty($checkemail)){
           DB::beginTransaction();
              try {
                    if($request->get("user_type")==1){
                        $user=new User();
                        $user->email=$request->get("email");
                        $user->password=$request->get("password");
                    }
                    else{
                        $user = Sentinel::registerAndActivate($request->input());
                    }
                    
                    $user->first_name=$request->get("first_name");
                    $user->is_email_verified='1';
                    $user->address=$request->get("address");
                    $user->phone=$request->get("phone");
                    $user->login_type=1;
                    $user->user_type=$request->get("user_type");                                    
                    $user->save();
                    
                          DB::commit();
                           Session::flash('message',__('messages_error_success.create_success')); 
                                    Session::flash('alert-class', 'alert-success');
                                    return redirect()->back();
                          
              }
              catch (\Exception $e) {
                   DB::rollback();
                   Session::flash('message',__('messages_error_success.error_code')); 
                   Session::flash('alert-class', 'alert-danger');
                   return redirect()->back();       
              }          
        }
        else{
            Session::flash('message',__('messages_error_success.email_already_error')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
        
    }

    public function changestatus($id){
        $store=User::find($id);
        if($store->is_active=='0'){
            $store->is_active='1';
            $msg=__('messages_error_success.user_active_msg');
        }
        else{
            $store->is_active='0';
            $msg=__('messages_error_success.user_deactive_msg');
        }
        $store->save();
        Session::flash('message',$msg); 
        Session::flash('alert-class', 'alert-success');
         return redirect()->back();
    }

    public function edituser($id){
        $data=User::find($id);
        return json_encode($data);
    }

    public function updateuser(Request $request){

      $data=User::find($request->get("id"));
      $data->first_name=$request->get("first_name");
      $data->email=$request->get("email");
      $data->phone=$request->get("phone");
      $data->address=$request->get("address");
      $data->save();
      Session::flash('message',__('messages_error_success.user_update_success')); 
      Session::flash('alert-class', 'alert-success');
      return redirect()->back();
    }

    public function userrole(){
       $lang = Lang_core::all();
       return view("admin.user.role")->with("lang",$lang);
    }
 
    public function confirmregister($id){
        $store=User::find($id);
        $store->is_email_verified='1';
        $store->save();
        Session::flash('message',__('messages_error_success.email_verified')); 
        Session::flash('alert-class', 'alert-success');
        return view("emailverified");
    }

    public function showseller(){
        $category=Categories::where("is_delete",'0')->where('parent_category','0')->get();
        $lang = Lang_core::all();
         foreach ($category as $k) {
            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
            $k->name = isset($getlang)?$getlang->meta_value:'';
         }
       return view("admin.user.seller")->with("category",$category)->with("lang",$lang);
    }
    
    public function check_email(Request $request){
        /*echo $request->get("email");
        die();*/
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
    
    public function sellerTable(){
          $user =User::where('user_type',3)->orderBy('id','DESC')->get();
         return DataTables::of($user)
            ->editColumn('id', function ($user) {
                return $user->id;
            })
            ->editColumn('name', function ($user) {
                return $user->first_name;
            })
            ->editColumn('email', function ($user) {
                return $user->email;
            })
            ->editColumn('phone', function ($user) {
                return $user->phone;
            })
                     
            ->editColumn('action', function ($user) {
               $changestatus=url('admin/changeuserstatus',array('id'=>$user->id));
                $deleteuser=url('admin/userdelete',array('id'=>$user->id));
               if($user->is_active=='1'){
                   if(Session::get("is_demo")=='1'){
                       $msg= "This function is currently disable as it is only a demo website, in your admin it will work perfect";
                       $return =  '<a onclick="editseller('.$user->id.')" style="color:#fff !important;margin-right: 10px;" rel="tooltip"  class="m-b-10 m-l-5 btn-success btn" data-original-title="Remove" data-toggle="modal" data-target="#edituser">Edit</a>
                                    <a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip" style="color:#fff !important;margin-right: 10px;"  class="m-b-10 m-l-5 btn-danger btn" data-original-title="Remove" style="margin-right: 10px;">Delete</a>
                                    <button type="button" onclick="disablebtn()"  class="m-b-10 m-l-5 btn btn-danger" >
                                   Reject</button>';
                   }else{
                       $return = '<a onclick="editseller('.$user->id.')" style="color:#fff !important;margin-right: 10px;" rel="tooltip"  class="m-b-10 m-l-5 btn-success btn" data-original-title="Remove" data-toggle="modal" data-target="#edituser">Edit</a>
                                    <a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip" style="color:#fff !important;margin-right: 10px;"  class="m-b-10 m-l-5 btn-danger btn" data-original-title="Remove" style="margin-right: 10px;">Delete</a>
                                    <a href="'.$changestatus.'" rel="tooltip" style="color:#fff !important;margin-right: 10px;" class="m-b-10 m-l-5 btn btn-danger" data-original-title="Remove">Reject</a>';
                   }
                   
                 }
                 else{
                     if(Session::get("is_demo")=='1'){
                       $msg= "This function is currently disable as it is only a demo website, in your admin it will work perfect";
                       $return =  '<a onclick="editseller('.$user->id.')" style="color:#fff !important;margin-right: 10px;" rel="tooltip"  class="m-b-10 m-l-5 btn-success btn" data-original-title="Remove" data-toggle="modal" data-target="#edituser">Edit</a>
                                   <a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip" style="color:#fff !important;margin-right: 10px;"  class="m-b-10 m-l-5 btn-danger btn" data-original-title="Remove" style="margin-right: 10px;">Delete</a>
                                   <button type="button" onclick="disablebtn()"  style="color:#fff !important;margin-right: 10px;" class="m-b-10 m-l-5 btn btn-success" >Approve</button>';
                   }else{
                       $return = '<a onclick="editseller('.$user->id.')" style="color:#fff !important;margin-right: 10px;" rel="tooltip"  class="m-b-10 m-l-5 btn-success btn" data-original-title="Remove" data-toggle="modal" data-target="#edituser">Edit</a>
                                   <a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip" style="color:#fff !important;margin-right: 10px;"  class="m-b-10 m-l-5 btn-danger btn" data-original-title="Remove" style="margin-right: 10px;">Delete</a>
                                   <a href="'.$changestatus.'" rel="tooltip" style="color:#fff !important;margin-right: 10px;" class="m-b-10 m-l-5 btn btn-success" data-original-title="Remove" ;">Approve</a>';
                   }
                     
                 }
               
                 return $return;              
            })           
            ->make(true);
    }

    public function updateseller(Request $request){
      if($request->get("id")==0){
          $data=new User();
        $getemail=User::where("email",$request->get("email"))->first();
          if($getemail){
              Session::flash('message',__('messages.email id already exists')); 
              Session::flash('alert-class', 'alert-danger');
              return redirect()->back();
          }
         
           $data->password=$request->get("password");
          $msg=__('messages.Seller Account Add Successfully');
      }else{
           $data=User::find($request->get("id"));
           $msg=__('messages.Seller Account Update Successfully');
      } 
      $data->first_name=$request->get("first_name");
      $data->email=$request->get("email");
      $data->phone=$request->get("phone");
      //$data->address=$request->get("address");
      $data->user_type='3';
      //$data->brand_name = $request->get("brand_name");
      //$data->access_cat=implode(",",$request->get("access_cat"));
      $data->save();
      Session::flash('message',$msg); 
      Session::flash('alert-class', 'alert-success');
      return redirect()->back();
    }

    public function editseller($id){
        $data=User::find($id);
        $getcategory=Categories::where("is_active",'1')->where("is_delete",'0')->where('parent_category','0')->get();
        $txt="";
        $arr=explode(",",$data->access_cat);
        foreach ($getcategory as $k) {
          $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                 $k->name = isset($getlang)?$getlang->meta_value:'';
            if(in_array($k->id,$arr)){
                
                 $txt=$txt.'<option value="'.$k->id.'" selected="selected">'.$k->name.'</option>';
            }else{
                 $txt=$txt.'<option value="'.$k->id.'">'.$k->name.'</option>';
            }
           
        }
        $data->options=$txt;


       
        $lang = Lang_core::all();
        return json_encode($data);
    }
    /*my function for seller(restaurant) edit profile*/
    
    public function show_restaurant_profile(){
      $user=Auth::user();
        $category=Categories::where("is_delete",'0')->where('parent_category','0')->get();
       $lang = Lang_core::all();
       return view("seller.edit_profile")->with("lang",$lang)->with("data",$user)->with("category",$category);
    }
    
    public function edit_restaurant_profile(Request $request){    
          
           $user=Auth::user();
           $old_img = $user->res_image;
           /*echo $old_img = $user->res_image;
           echo $image_path = public_path() ."/upload/restaurant/".$old_img;*/
           if($request->hasFile('file')) 
              {
                 $file = $request->file('file');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/restaurant';
                 $picture = "restaurant_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('file')->move($destinationPath, $picture);
                 $img_url =$picture;
                 if($old_img!=""){
                   $image_path = public_path() ."/upload/restaurant/".$old_img;

                  if(file_exists($image_path)) 
                  {
                     unlink($image_path);
                  }
               }
             }else{
                 $img_url = $user->res_image;
             }
            $data=User::find($user->id);
            $data->access_cat=implode(",",$request->get("access_cat"));
            $data->first_name=$request->get("name");
            $data->res_time=$request->get("open_close_time");
            $data->delivery_time=$request->get("delivery_time");
            $data->address=$request->get("address");
            $data->two_person_cost=$request->get("two_person_cost");
           
            $data->lat=$request->get("latitude");
            $data->long=$request->get("longitude");
            $data->res_image=$img_url;
            $data->save();
            Session::put("profile_pic",asset("public/upload/restaurant/"."/".$img_url));
            Session::flash('message',__('messages_error_success.profile_sucess_update')); 
            Session::flash('alert-class', 'alert-success');
            return redirect("seller/restaurant_profile");
   }
}