<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Product;
use App\Models\OrderData;
use App\Models\User;
use App\Models\Lang_core;
use App\Models\FileMeta;
use DataTables;
use Sentinel;
use Session;
use Hash;
use Auth;
use DB;
use App;
class Admincontroller extends Controller {
  
   public function showlogin(){
       parent::callschedule();
       $setting=Setting::find(1);
       Session::put("is_demo",$setting->is_demo);
       Session::put("is_rtl",$setting->is_rtl);
       Session::put("is_web",$setting->is_web); 
       return view("admin.login");
   }
   
    public function showlocationchange($locale){
        App::setlocale($locale);
        session()->put('locale', $locale);
        return 1;
    }

    public function languagechange($locale){
        App::setlocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
   
   public function privacy(){
       return view("privacy_policy");
   }

   public function showdoc(){
      return view("document.layout");
   }

   public function adddoc(){
      return view("document.product");
   }
   
   public function dealofferdoc(){
       return view("document.dealoffer");
   }
 
   public function normalofferdoc(){
       return view("document.normaloffer");
   }
   
   public function mainofferdoc(){
       return view("document.mainoffer");
   }
   public function addproductstep(){
      return view("document.addproductstep");
   }
   
   public function addfeadoc(){
      return view('document.addfeadoc');
   }
   public function postlogin(Request $request){
        $checkuser=User::where("email",$request->get("email"))->where("user_type",'2')->get();
        if(count($checkuser)!=0){
             $user=Sentinel::authenticate($request->all());
             if($user){
                 $data=Sentinel::getUser();
                 Session::put("profile_pic",asset("public/upload/profile/"."/".$data->profile_pic));
                 return  redirect("admin/dashboard");    
               } 
              else{
                   
                    Session::flash('message',__('messages_error_success.login_error')); 
                    Session::flash('alert-class', 'alert-danger');
                    return redirect()->back();
              } 
        }
        else{
            Session::flash('message',__('messages_error_success.login_error')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }       
   }

   public function showdashboard(){
        $lang = Lang_core::all();
        if(Session::get('locale')==""){
            Session::put('locale','en');
        }
        $setting=Setting::find(1);
        $res_curr=explode("-",$setting->default_currency);
        $order=Order::all();
        if(count($order)!=0){
             $array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
        }
        else{
             $array_ob["date"]="";
        }
         
          $array_ob["order"]=count($order);
          $subtotal=0;
          $shipping=0;
          $product=array();
          $tax=0;
          $total=0;
          foreach ($order as $k) {
              $total_sell=$total+$k->total;
              }

          $total_sell=isset($res_curr[1])?$res_curr[1]:''."".number_format($total,2,'.','');
          $total_order=Order::count();
          
          $total_product=Product::count();
          $total_users=User::where("user_type",'1')->count();
          
          return view("admin.dashboard",compact('total_order','total_product','total_users','total_sell','lang'));
   }

   public function showlogout(){
            Sentinel::logout();
            return redirect('login');
   }

   public function sellershowlogout(){
       Auth::logout();
       return redirect('sellerlogin');
   }

   public function showuserlogout(){
        Auth::logout();
        Session::forget('user_id');
        return redirect('/');
   }

   public function editprofile(){
        $user=Sentinel::getUser();
         $lang = Lang_core::all();
       return view("admin.updateprofile")->with("data",$user)->with("lang",$lang);
   }

   public function updateprofile(Request $request){      
           $user=Sentinel::getUser();
           if ($request->hasFile('file')) 
              {
                 $file = $request->file('file');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/profile';
                 $picture = "profile_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('file')->move($destinationPath, $picture);
                 $img_url =$picture;
             }else{
                 $img_url = $user->profile_pic;
             }
            $data=User::find($user->id);
            $data->first_name=$request->get("name");
            $data->profile_pic=$img_url;
            $data->save();
            Session::put("profile_pic",asset("public/upload/profile/"."/".$img_url));
            Session::flash('message',__('messages_error_success.profile_sucess_update')); 
            Session::flash('alert-class', 'alert-success');
            return redirect("admin/editprofile");
   }

    public function changepassword(Request $request){
       $lang = Lang_core::all();
      return view("admin.changepassword")->with("lang",$lang);
   }  
   public  function check_password_same($pwd){
    $user=Sentinel::getUser();
     if (Hash::check($pwd, $user->password))
     {
        $data=1;
     }
    else{
        $data=0; 
     }
   return json_encode($data);
   }
   
   public function check_user_password_same($pwd){
       $user=Auth::user();
       if($user->password==$pwd)
       {
          $data=1;
       }
       else{
            $data=0; 
       }
       return json_encode($data);
   }
   public function updatepassword(Request $request){
     $user=Sentinel::getUser();
       if (Hash::check($request->get('cpwd'), $user->password))
        {
            Sentinel::update($user, array('password' => $request->get('npwd')));
            Session::flash('message',__("messages_error_success.password_update_success")); 
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
        }
       else{
          Session::flash('message',__('messages_error_success.error_code')); 
          Session::flash('alert-class', 'alert-danger');
          return redirect()->back();;
       }
       
   }

   public function changeuserpwd(Request $request){
      $user=Auth::user();
      $user->password=$request->get('npwd');
      $user->save();
      return __("messages_error_success.password_update_success");
   }

   public function updateuserprofile(Request $request){
         
            if ($request->hasFile('file')) 
              {
                 $file = $request->file('file');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/profile';
                 $picture = "profile_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('file')->move($destinationPath, $picture);
                 $img_url =$picture;
             }else{
                 $img_url = Auth::user()->profile_pic;
             }
            $data=User::find(Auth::id());
            $data->first_name=$request->get("edit_first_name");
            $data->phone=$request->get("edit_phone");
            $data->address=$request->get("edit_address");
            $data->profile_pic=$img_url;
            $data->save();
            Session::put("profile_pic",asset("public/upload/profile/"."/".$img_url));
            Session::put("name",$data->first_name);
            Session::flash('message',__('messages_error_success.profile_sucess_update')); 
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
   }
 
   public function showsellerlogin(){
      
      return view("seller.login");
   }

   public function postsellerlogin(Request $request){
    //   dd($request->all());
       $checkuser=User::where("email",$request->get("email"))->where("password",$request->get("password"))->where("user_type",'3')->first();

        if($checkuser){
             Auth::login($checkuser, true);
            $data=Auth::User();
             if($data){
                 
                 if($data->profile_pic==""){
                 Session::put("profile_pic",asset("public/upload/profile/defaultuser.jpg"));
                 }else{
                   Session::put("profile_pic",asset("public/upload/profile/"."/".$data->profile_pic));
                 }
                // echo "testseller@gmail.com";exit;
                 return  redirect("seller/dashboard");    
               } 
              else{
                   

                    Session::flash('message',__('messages_error_success.login_error')); 
                    Session::flash('alert-class', 'alert-danger');
                    return redirect()->back();
              } 
        }
        else{
            Session::flash('message',__('messages_error_success.login_error')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }    
   }

   public function showsellerdashboard(){
      $lang = Lang_core::all();
      if(Session::get('locale')==""){
            Session::put('locale','en');
        }
         $setting=Setting::find(1);
        $res_curr=explode("-",$setting->default_currency);
        $order=OrderData::where("seller_id",Auth::id())->get();
        if(count($order)!=0){
             $array_ob["date"]=date('F d,Y',strtotime($order[0]->created_at))."-".date('F d,Y',strtotime($order[count($order)-1]->created_at));
        }
        else{
             $array_ob["date"]="";
        }
         
          $array_ob["order"]=count($order);
          $subtotal=0;
          $shipping=0;
          $product=array();
          $tax=0;
          $total=0;
          foreach ($order as $k) {
              $total=$total+$k->per_product_seller_price;
              }

          $total_sell=isset($res_curr[1])?$res_curr[1]:''."".number_format($total,2,'.','');
          $total_order=Order::where("seller_id",Auth::id())->get();
          $total_product=Product::where("user_id",Auth::id())->get();;
          
            $data = Order::orderBy('seller_id', 'desc')
                ->groupBy('seller_id')
                ->where('seller_id',Auth::id())
                ->select('seller_id', DB::raw('sum(`per_product_seller_price`) as total'))
                ->where("status","!=",'7')
                ->where("status","!=",'6')
                ->whereDate("created_at",date('Y-m-d'))
                ->first();
              //  echo "<pre>";print_r($data);exit;
      return view("seller.dashboard")->with("lang",$lang)->with("total_order",count($total_order))->with("total_product",count($total_product))->with("total_sell",$total)->with("lang",$lang)->with("currency",isset($res_curr[1])?$res_curr[1]:'')->with("total_Current_Sales",isset($data->total)?$data->total:'');
   }
}