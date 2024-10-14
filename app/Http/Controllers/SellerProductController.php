<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\AttributeSet;
use App\Models\Options;
use App\Models\Optionvalues;
use App\Models\Product;
use App\Models\Review;
use App\Models\Notifyuser;
use App\Models\ProductAttributes;
use App\Models\ProductOption;
use App\Models\Taxes;
use App\Models\Lang_core;
use App\Models\FileMeta;
use App\Models\OrderData;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\User;
use App\Models\Setting;
use Image;
use Artisan;
use Hash;
use Auth;
class SellerProductController extends Controller {
    public function __construct() {
         parent::callschedule();
    }


    public function showproduct($id){
         $lang = Lang_core::all();
         return view("seller.product.default")->with("lang",$lang)->with("id",$id);
    }
    
    public function getallproduct(){
        $data=Product::where("user_id",Auth::id())->get();
        foreach($data as $d){
            $getmeta = Filemeta::where("model_id",$d->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
            //$d->name = isset($getmeta->meta_value)?$getmeta->meta_value:'';
        }
        return json_encode($data);
     }

    public function productdatatable($id){
        if($id==0)
        {
            $category =Product::orderBy('id','DESC')->where("is_deleted",'0')->where("user_id",Auth::id())->get();
        }
        else
        {
            $category =Product::orderBy('id','DESC')->where("is_deleted",'0')->where("category",$id)->where("user_id",Auth::id())->get();
        }
         
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('thumbnail', function ($category) {
                return asset('public/upload/product')."/".$category->basic_image;
            })
            ->editColumn('name', function ($category) {
                 $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                return isset($getlang)?$getlang->meta_value:'';
            })
            ->editColumn('price', function ($category) {
                return $category->price;
            })
            ->editColumn('status', function ($category) {
                if($category->status=='1'){
                    return __('messages.approve');
                }
                else{
                     return __('messages.not approve');
                }
            })  
            /*->editColumn('attribute', function ($category) {
                return $category->id;
            })*/ 
            ->editColumn('option', function ($category) {
                return $category->id;
            })      
            ->editColumn('action', function ($category)use($id) 
            {                 
                $editoption=url('seller/savecatalog',array('id'=>$category->id,'tab'=>'1','cat_id'=>$id));
                $delete_pro=url('seller/delete_res_product',array('id'=>$category->id)); 
                $return = '<a  href="'.$editoption.'" rel="tooltip"  class="m-b-10 m-l-5 btn btn-success" style="color:#fff !important; " data-original-title="Remove">Edit</a> <a onclick="delete_record('."'".$delete_pro."'".')" rel="tooltip"  class="m-b-10 m-l-5 btn btn-danger" style="color:#fff !important; margin-right:10px" data-original-title="Remove" style="margin-right: 10px;">Deleted</a>';
                return $return;           
            })           
            ->make(true);
    }

    public function delete_res_product($id)
    {
        $res_id=Auth::user()->id;
        $store=Product::where("id",$id)->where("user_id",$res_id)->first();
        $store->is_deleted='1';
        $store->save();
        Session::flash('message',"Product Deleted Successfully"); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    public function getsubcategory($id){
        $data=Categories::where("parent_category",$id)->where("is_delete",'0')->get();
        foreach($data as $d){
            $getlang = FileMeta::where("model_id",$d->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
            $d->name = isset($getlang)?$getlang->meta_value:'';
        }
        return json_encode($data);
     }

   public function saveproduct(Request $request)
   {
    //dd($request);
       if($request->get("product_id")!=0){
            $store=Product::find($request->get("product_id"));
      
        }
        else{
            $store=new Product();
           

        }  
        $cat_id=$request->get("cat_id");   
        // echo $request->get("category");
        // $store->category= $request->category;
        $store->category=$cat_id;
      
        $store->is_veg=$request->get("is_veg");
        //$store->subcategory=$request->get("subcategory");
        //$store->brand=$request->get("brand");
        //$store->tax_class=$request->get("texable");
        $store->name=$request->get("name_en");
        $store->description=$request->get("description_en");
        $store->status='1';
        //$store->product_color=$request->get("colorpro");
        $store->user_id=Auth::id();
        $store->save();
        $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"Product");
            $this->file_meta_update_payment_key($store->id,$k->code,"description",$request->get("description_".$k->code),"Product");
            //$this->file_meta_update_payment_key($store->id,$k->code,"meta_keyword",$request->get("meta_keyword_".$k->code),"Product");
            $this->file_meta_update_payment_key($store->id,$k->code,"colorname",$request->get("colorname_".$k->code),"Product");
        }
        return redirect('seller/savecatalog/'.$store->id.'/2/'.$cat_id.'');
     }
     
    
     public function saveprice(Request $request){

        if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('seller/savecatalog/0/2');
        }
        if($request->get("mrp")<$request->get("price")){ 
            Session::flash('message',__('messages_error_success.selling_mrp_vaildate')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('seller/savecatalog/'.$request->get("product_id").'/2');
        }
       /* else{
            if($request->get("special_price")!=""&&$request->get("special_price")!="0.00"){
                 if($request->get("price")<$request->get("special_price")){
                     Session::flash('message',__('messages_error_success.check_price')); 
                     Session::flash('alert-class', 'alert-danger');
                     return redirect('seller/savecatalog/'.$request->get("product_id").'/2');
                 }
                 if($request->get("spe_pri_start")==""&&$request->get("spe_pri_to")==""){
                     Session::flash('message',__('messages_error_success.sepical_price_vaildate')); 
                     Session::flash('alert-class', 'alert-danger');
                     return redirect('seller/savecatalog/'.$request->get("product_id").'/2');
                 }
            }
        }*/
        $cat_id=$request->get("c_id");
        $store=Product::find($request->get("product_id"));
        $store->price=number_format((float)$request->get("mrp"), 2, '.', '');
        $store->selling_price=number_format((float)$request->get("mrp"), 2, '.', '');
        $store->MRP=number_format((float)$request->get("mrp"), 2, '.', '');
        $store->discount_type=$request->get("discount_type");
        $store->discount_val=$request->get("discount_atm");
                $store->special_price=number_format((float)$request->get("special_price"), 2, '.', '');
                $store->special_price_start=$request->get("spe_pri_start");
                $store->special_price_to=$request->get("spe_pri_to");
        //echo "<pre>";print_r($store);die();
        $store->save();
        
         parent::productupdate();
        return redirect('seller/savecatalog/'.$store->id.'/4/'.$cat_id.'');
     }

     public function saveinventory(Request $request){
        if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('seller/savecatalog/0/3');
        }

        if($request->get("sku")==""){
                $store=Product::find($request->get("product_id"));
                $store->sku=$request->get("sku");
                $store->inventory=$request->get("inventory");
                $store->stock=$request->get("stock");
                $store->save();
                 return redirect('seller/savecatalog/'.$request->get("product_id").'/4');
        }else{
            $checksku=Product::where("sku",$request->get("sku"))->where("id","!=",$request->get("product_id"))->first();
              if(!isset($checksku)){
                $store=Product::find($request->get("product_id"));
                $store->sku=$request->get("sku");
                $store->inventory=$request->get("inventory");
                $store->stock=$request->get("stock");
                $store->save();
                return redirect('seller/savecatalog/'.$store->id.'/4');
             }
             Session::flash('message',__('messages_error_success.sku_already')); 
             Session::flash('alert-class', 'alert-danger');
             return redirect('seller/savecatalog/'.$request->get("product_id").'/4'); 
         }
              
     }

     public function saveproductimage(Request $request){
        $cat_id=$request->get("ct_id");
         if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('seller/savecatalog/0/4');
        }
         $add_img=array();
        $store=Product::find($request->get("product_id"));
        //$adddata=explode(",",$store->additional_image);
        if($request->get("basic_img")!=""){
            if(strstr($request->get("basic_img"),"http")==""){
                $data = $request->get("basic_img");
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $folderName = '/upload/product/';
                $destinationPath = public_path() . $folderName;
                $file_name=uniqid() . '.png';
                $file = $destinationPath .$file_name;
                $data = base64_decode($data);
                file_put_contents($file, $data);              
                $store->basic_image=$file_name;
                if($request->get("real_basic_img")!=$file_name){
                        $image_path="";
                        if($request->get("real_basic_img")!=""){
                            $image_path = public_path() ."/upload/product/".$request->get("real_basic_img");
                        }
                        if(file_exists($image_path)) {
                            try{
                                 unlink($image_path);
                            }
                            catch(\Exception $e)
                            {
                                
                            }
                            
                        }
                }
            }
                       
        }
        
        $store->save();
      

        return redirect('seller/product/'.$cat_id.'');
     }

   
     
     public function saveproductattibute(Request $request){
     //  dd($request->all());exit;
       if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('seller/savecatalog/0/5');
        }
        $arr=array_values($request->get("attributeset"));
        if(count($arr)==0){
                    return redirect('seller/savecatalog/'+$request->get("product_id")+'/6');
        }
        
        $checkproattri=ProductAttributes::where("product_id",$request->get("product_id"))->delete();
        $lang = Lang_core::all();
        
        if(count($arr)!=0){                
            for ($i=0; $i <count($arr); $i++) {  
                foreach ($lang as $l) {          
                    $store=new ProductAttributes();
                    $store->product_id=$request->get("product_id");
                    $store->attributeset=$arr[$i][$l->code]['set'];
                    $store->label=implode(",",$arr[$i][$l->code]['label']);
                    $store->value=implode(",", $arr[$i][$l->code]['value']);
                    $store->lang = $l->code;
                    $store->attribute = $i+1;
                    $store->save();                               
                }
            }          
        }
        
         return redirect('seller/product');
     }

 public function getoptionvalues($id){
         $optionvalues=Options::with("optionlist")->where("id",$id)->first();
         return json_encode($optionvalues);
     }
     public function saveproductoption(Request $request){
        //echo "<pre>";print_r($request);die();

       //dd($request->all());
         if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
        $product=Product::where('id',$request->get("product_id"))->first();
            $cat_id=$product->category;
        if($request->get("options")!="")
        {
            $arr=array_values($request->get("options"));
            //echo "<pre>";print_r($arr);die();
            $name=array();
            $type=array();
            $required=array();
            $label=array();
            $price=array();
            $min_item_selection=array();
            $max_item_selection=array();
            $lang = Lang_core::all();
            
            $checkoption=ProductOption::where("product_id",$request->get("product_id"))->delete();
             for ($i=0; $i <count($arr); $i++) {  
                foreach ($lang as $k) {
                   if(isset($arr[$i][$k->code]['name'])&&isset($arr[$i][$k->code]['label'])&&isset($arr[$i][$k->code]['price'])){                   
                         $store=new ProductOption();    
                         $store->product_id=$request->get("product_id");
                         $store->option_id = $i+1;
                         $store->name=$arr[$i][$k->code]['name'];
                         $store->min_item_selection=$arr[$i][$k->code]['min_item_selection'];
                         $store->max_item_selection=$arr[$i][$k->code]['max_item_selection'];
                         
                         $store->label=implode("#",$arr[$i][$k->code]['label']);
                         $store->price=implode("#",$arr[$i][$k->code]['price']);
                         $store->lang = $k->code;
                         $store->save();
                    }
                }  
             }   
         }
         else
         {
            $checkoption=ProductOption::where("product_id",$request->get("product_id"))->delete();
         }
          Session::flash('message',__('messages.Option Save Successfully')); 
          Session::flash('alert-class', 'alert-success');
           return redirect('seller/product/'.$cat_id.'');
        
     }
    

  public function saverealtedprice(Request $request){
    
     if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('seller/savecatalog/0/7');
        }
       
       if($request->get('related_id')){
           $store=Product::find($request->get("product_id"));
        $store->related_product=implode(",",$request->get('related_id'));
        $store->save();
       }
        
       
          Session::flash('message',__('messages_error_success.pro_add')); 
          Session::flash('alert-class', 'alert-success');
          return redirect('seller/product');
  }

   public function getbrandbyid($id){
        $data=Brand::where("category_id",$id)->get();
        foreach($data as $k){
            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Brand")->where("meta_key","name")->first();
                           $k->brand_name = isset($getlang)?$getlang->meta_value:'';
        }
        return json_encode($data);
     }
 
     
     public function showaddcatalog($id,$tab,$cat_id){
        // echo "hey";exit;
          $data=array();
          $subcategory=array();
          $brand=array();
          $optionls=array();
          if($id!=0){
                $data=Product::find($id);
                if($data->category){
                    $subcategory=Categories::where("parent_category",$data->category)->where("is_delete",'0')->get();
                    foreach ($subcategory as $k) {
                           $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                           $k->name = isset($getlang)?$getlang->meta_value:'';
                    }
                    foreach ($brand as $k) {
                           $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Brand")->where("meta_key","name")->first();
                           $k->brand_name = isset($getlang)?$getlang->meta_value:'';
                    }
                }
                if($data->subcategory){
                    $brand=Brand::where("category_id",$data->subcategory)->where("is_delete",'0')->get();
                }
                $data->optionls=ProductOption::where("product_id",$id)->first();
                
                $language =Lang_core::all();  
                foreach ($language as $k) {         
                    $getmeta = FileMeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","name")->where("lang",$k->code)->first();
                    $name = "name_".$k->code;
                    if($getmeta){
                        $data->$name = $getmeta->meta_value;
                    }else{
                        $data->$name = "";
                    }

                    $getmeta = FileMeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","description")->where("lang",$k->code)->first();
                    $name = "description_".$k->code;
                    if($getmeta){
                        $data->$name = $getmeta->meta_value;
                    }else{
                        $data->$name = "";
                    }

                    $getmeta = FileMeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","meta_keyword")->where("lang",$k->code)->first();
                    $name = "meta_keyword_".$k->code;
                    if($getmeta){
                        $data->$name = $getmeta->meta_value;
                    }else{
                        $data->$name = "";
                    }

                    $getmeta = FileMeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","colorname")->where("lang",$k->code)->first();
                    $name = "colorname_".$k->code;
                    if($getmeta){
                        $data->$name = $getmeta->meta_value;
                    }else{
                        $data->$name = "";
                    }
                    }                
          }
          
           $category=explode(",",Auth::user()->access_cat);
                 $arr=array();
                 foreach ($category as $k) {
                    $getdat=Categories::find($k);
                    if($getdat){
                        $getlang = FileMeta::where("model_id",$k)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                          $getdat->name = isset($getlang)?$getlang->meta_value:'';
                         $arr[]=$getdat;
                    }
                 }
                 $r_id = Auth::user()->id;
         $category=Categories::where("res_id",$r_id)->where("is_delete",'0')->get();

          /*$tax=Taxes::all();   
           foreach ($tax as $k) {
                $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Taxes")->where("meta_key","tax_name")->first();
                $k->tax_name = isset($getlang)?$getlang->meta_value:'';
          } */
          $lang = Lang_core::all();     
        //  echo "hey";exit;
          return view("seller.product.addproduct")->with("category",$category)->with("product_id",$id)->with("tab",$tab)->with("data",$data)->with("subcategory",$subcategory)->with("brand",$brand)->with("lang",$lang)->with('cat_id',$cat_id);
     }

   public function checktotalproduct(){
          $category =Product::orderBy('id','DESC')->where('is_deleted','0')->where("status",'1')->get();
          if(count($category)>1){
              return 0;
          }else{
              return 1;
          }
     }

      public function productlist($id,$pro_id){
         if($pro_id==0){
             $category =Product::orderBy('id','DESC')->where("is_deleted",'0')->where("status",'1')->where("user_id",Auth::id())->get();
         }
         else{
             $category =Product::orderBy('id','DESC')->where("is_deleted",'0')->where("status",'1')->where("subcategory",$id)->where("id","!=",$pro_id)->where("user_id",Auth::id())->get();
         }
         
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('thumbnail', function ($category) {
                return asset('public/upload/product')."/".$category->basic_image;
            })
              ->editColumn('name', function ($category) {
                 $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Product")->where("meta_key","name")->first();
                return isset($getlang)?$getlang->meta_value:'';
            })
            ->editColumn('price', function ($category) {
                return $category->price;
            })           
                       
            ->make(true);
     }


        public function showsales(){
            $lang = Lang_core::all();

         return view("seller.sales")->with("lang",$lang);
     }

    public function vieworder($id){    
           $data=Order::find($id);
           $user=User::find($data->user_id);
           $shipping=Shipping::find($data->shipping_method);
           $order_data=OrderData::with("productdata")->where("order_id",$id)->get(); 
           $setting=Setting::find(1);
           $res_curr=explode("-",$setting->default_currency); 
           $lang = Lang_core::all();
           return view("seller.vieworder")->with("order",$data)->with("orderdata",$order_data)->with("user",$user)->with("shipping",$shipping)->with("currency",$res_curr[1])->with("lang",$lang);
    }


    public function orderdatatable(){
        $order = Order::with('orderdatals')->where("seller_id",Auth::id())->get();
     
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
                 if(isset($order->payment_method) == 1){
                    return __('messages.paypal');
                 }elseif(isset($order->payment_method) == 2){
                    return __('messages.stripe');
                 }elseif(isset($order->payment_method) == 3){
                    return __('messages.case_on_delivery');
                 }elseif(isset($order->payment_method) == 4){
                    return __('messages.braintree');
                 }elseif(isset($order->payment_method) == 0){
                    return __('messages.case_on_delivery');
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
                $accept=url("seller/changeorderstatus",array('id'=>$order->id,"status"=>1));
                $reject=url("seller/changeorderstatus",array('id'=>$order->id,"status"=>2));
                $prepare=url("seller/changeorderstatus",array('id'=>$order->id,"status"=>3));
                 if($order->status==0){//accept,reject,delete
                  $return = '<a href="'. $accept .'" rel="tooltip" title="" class="btn btn-sm btn-success btnorder" data-original-title="Remove" style="color: white !important;margin-right: 5px;">'.__('messages.accept').'</a><a onclick="reject_record(' . "'" . $reject . "'" . ')" rel="tooltip" title="" class="btn btn-sm btn-info" data-original-title btnorder="Remove" style="margin-top: 5px;margin-bottom: 5px;color: white !important;">'.__('messages.reject').'</a>';
               }
               if($order->status==1){
                    $return = '<a href="'.$prepare.'" rel="tooltip" title="" class="btn btn-sm btn-success btnorder" data-original-title="Remove" style="color: white !important;margin-right: 5px;" >'.__('messages.prepare').'</a>';
               }
                 return $return;                
            })           
            ->make(true);
    }

    public function showattribute($id){
      $lang = Lang_core::all(); 
      $getallproduct = Product::where("user_id",Auth::id())->where("id",$id)->first();
      if($getallproduct){
              $product_attributes = ProductAttributes::where("product_id",$id)->groupby('attribute')->get();
              $data = array();
              $i = 0;
              foreach ($product_attributes as $p) {
                   foreach ($lang as $l) {
                       $data[$i][$l->code] = ProductAttributes::where("product_id",$id)->where("attribute",$p->attribute)->where("lang",$l->code)->first();
                   }
                   $i++;
              }
           // echo "<pre>";print_r($data[0]['en']->attributeset);exit;
              return view("seller.product.attribute")->with("product_id",$id)->with("lang",$lang)->with("data",$data);
      }else{

          return redirect()->back();
      }
      
  }
  public function add_customisation(Request $request)
  {
        $check=ProductOption::where('product_id',$request->get('cut_pro_id'))->get();
        $option_id=count($check)+1;
        $store=new ProductOption();
        $store->product_id=$request->get('cut_pro_id');
        $store->name=$request->get('cut_name');
        $store->min_item_selection=$request->get('min_item_selection');
        $store->max_item_selection=$request->get('max_item_selection');
        $store->option_id=$option_id;
        $store->lang='en';
        $store->save();
        Session::flash('message',"Customisation Add Successfully"); 
        Session::flash('alert-class', 'alert-success');
        return redirect('seller/options/'.$request->get('cut_pro_id').'');

  }

  public function showoptions($id){

      $lang = Lang_core::all(); 
      $getallproduct = Product::where("user_id",Auth::id())->where("id",$id)->first();
      if($getallproduct){   
              $product_option = ProductOption::where("product_id",$id)->groupby('option_id')->get();
              //echo "<pre>";print_r($product_option);exit;
              $data = array();
              $i = 0;
              if(count($product_option)>0){
                    foreach ($product_option as $p) {
                           foreach ($lang as $l) {
                               $data[$i][$l->code] = ProductOption::where("product_id",$id)->where("option_id",$p->option_id)->where("lang",$l->code)->first();
                           }
                           $i++;
                      }
              }
              //echo "<pre>";print_r($product_option);die();
              return view("seller.product.option")->with("product_id",$id)->with("lang",$lang)->with("data",$data);
      }else{

          return redirect()->back();
      }
  }

  public function addattributerow($id){
        $lang = Lang_core::all();
        $txt = '<div class="category-wrap1" data-id="'.$id.'" id="mainattr_'.$id.'"><h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small"><div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>'.__('messages.New Attributes').'</h3><div class="uk-accordion-content categories-content " style="margin-top: 0px;padding:0px"><div class="custom-tab"><nav class="col-md-12 tabcatlog"><div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist">';
            $k=0;
            foreach($lang as $l){
                if($k==0){
                    $txt = $txt.'<a class="nav-item nav-link active" id="step_tab_attr'.$l->code.$id.'" data-toggle="tab" href="#stepattr'.$l->code.$id.'" role="tab" aria-controls="stepattr'.$l->code.$id.'" aria-selected="true">'.$l->name.'</a>';
                }else{
                    $txt = $txt.'<a class="nav-item nav-link" id="step_tab_attr'.$l->code.$id.'" data-toggle="tab" href="#stepattr'.$l->code.$id.'" role="tab" aria-controls="stepattr'.$l->code.$id.'" aria-selected="true">'.$l->name.'</a>';
                }
                
                $k++;
            }
            $txt = $txt.'</div></nav><div class="tab-content col-md-12 p-0 " id="nav-tabContent">';
            $k=0;
            foreach($lang as $l){
                if($k==0){
                    $txt = $txt.'<div class="tab-pane fade show active" id="stepattr'.$l->code.$id.'" role="tabpanel" aria-labelledby="step_tab_attr'.$l->code.$id.'" >';
                }else{
                    $txt = $txt.'<div class="tab-pane fade" id="stepattr'.$l->code.$id.'" role="tabpanel" aria-labelledby="step_tab_attr'.$l->code.$id.'" >';
                }
                $txt = $txt.'<table class="table table-striped table-bordered"><tbody><tr><td><input type="text" required name="attributeset['.$id.']['.$l->code.'][set]" class="form-control" placeholder="'.__('messages.Enter Attribute Set').'"><table class="table table-striped table-bordered cmr1"><thead><tr><th>'.__('messages.attribute').'</th><th>'.__('messages.value').'</th><th></th></tr></thead><tbody id="morerow_'.$l->code.'_'.$id.'"><tr id="attrrow_'.$l->code.'_'.$id.'_0"><td><input required class="form-control" type="text" name="attributeset['.$id.']['.$l->code.'][label][]"></td><td><input required class="form-control" type="text" name="attributeset['.$id.']['.$l->code.'][value][]"></td><td><button type="button" onclick="removeattrrow('.$id.',0,\'' . $l->code . '\')" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td></tr></tbody></table><input type="hidden" name="totalattr_'.$l->code.'_'.$id.'" id="totalattr_'.$l->code.'_'.$id.'" value="0"/><button type="button" class="btn btn-primary fleft" onclick="addattrrow('.$id.',\'' . $l->code . '\')"><i class="fa fa-plus"></i>'.__("messages.add_new_row").'</button></td><td><button onclick="removerowmain('.$id.')" type="button" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td></tr></tbody></table></div>';
                $k++;
            }
            $txt = $txt.'</div></div></div></div>';  
            return $txt;
  }

  public function addattributeinnerrow($newrow,$lang,$val){
        $txt = '<tr id="attrrow_'.$lang."_".$val."_".$newrow.'"><td><input class="form-control" type="text" required name="attributeset['.$val.']['.$lang .'][label][]"></td><td><input class="form-control" type="text" required name="attributeset['.$val.']['.$lang .'][value][]"></td><td><button onclick="removeattrrow('.$val.','.$newrow.',\'' .$lang .'\')" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td></tr>';
        return $txt;
  }

  public function addoptionrow($id){
    $lang = Lang_core::all();
    $txt='<div class="category-wrap1" data-id="'.$id.'" id="mainoption'.$id.'"><h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small"><div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>'.__('messages.new_option').'</h3><div class="uk-accordion-content categories-content "><div class="custom-tab"><nav class="col-md-12 tabcatlog"><div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist">';
    $k=0;
    foreach($lang as $l){
        if($k==0){
            $txt = $txt.'<a class="nav-item nav-link active" id="step_tab_attr'.$l->code.$id.'" data-toggle="tab" href="#stepattr'.$l->code.$id.'" role="tab" aria-controls="stepattr'.$l->code.$id.'" aria-selected="true">'.$l->name.'</a>';
        }else{
            $txt = $txt.'<a class="nav-item nav-link" id="step_tab_attr'.$l->code.$id.'" data-toggle="tab" href="#stepattr'.$l->code.$id.'" role="tab" aria-controls="stepattr'.$l->code.$id.'" aria-selected="true">'.$l->name.'</a>';
        }
        
        $k++;
    }
    $txt = $txt.'</div></nav><div class="tab-content col-md-12 p-0 " id="nav-tabContent">';
    $k=0;
    foreach($lang as $l){
            if($k==0){
                $txt = $txt.'<div class="tab-pane fade in show active" id="stepattr'.$l->code.$id.'" role="tabpanel" aria-labelledby="step_tab_attr'.$l->code.$id.'">';
            }else{
                $txt = $txt.'<div class="tab-pane fade" id="stepattr'.$l->code.$id.'" role="tabpanel" aria-labelledby="step_tab_attr'.$l->code.$id.'">';
            }
            $txt = $txt.'<div class="edit-p-list-u"><ul class="ulinine"><li class="ulliinine"><label for="name" class="control-label mb-1">'.__('messages.name').'</label><input required name="options['.$id.']['.$l->code.'][name]" type="text" class="form-control" aria-required="true" aria-invalid="false"></li><li class="ulliinine"><label for="name" class="control-label mb-1">'.__('messages.type').'</label><select name="options['.$id.']['.$l->code.'][type]" required class="form-control" onchange="addoptionvalue('.$id.',\'' .$l->code .'\')"><option value="">'.__('messages.select').' '.__('messages.type').'</option><option value="1">'.__('messages.dropdown').'</option><option value="2">'.__('messages.checkbox').'</option><option value="3">'.__('messages.radiobutton').'</option></select></li><li class="ulliinine3"><input type="checkbox" value="1" name="options['.$id.']['.$l->code.'][required]" class="form-check-input">'.__('messages.required').'</li><li class="ulliinine3"><button type="button" class="btn btn-danger" onclick="removeoption('.$id.',\'' .$l->code .'\')"><i class="fa fa-trash f-s-25"></i></button></li></ul></div><div id="valuesection_'.$id.'_'.$l->code.'"></div></div>';
         $k++;
    }
    
    $txt = $txt.'</div></div></div></div>';
    return $txt;
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
        $order=Notifyuser::where("receiver_id",Auth::id())->where("read",'0')->get();
        return count($order);
    }
      public function haveOrdersdata(){
        $user = Sentinel::getuser();
        $order=Notifyuser::where("receiver_id",Auth::id())->orderby("id","DESC")->where("read",'0')->get();
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
                $k->sender_name=(User::find($k->sender_id))?User::find($k->sender_id)->first_name:"";
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
      $order=Notifyuser::where("receiver_id",Auth::id())->where("read",'0')->get();
      foreach ($order as $k) {
         $k->read='1';
         $k->save();
      }
      return "done";
     }
}