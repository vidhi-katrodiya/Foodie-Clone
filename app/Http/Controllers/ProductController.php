<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Models\Categories;
use App\Models\FileMeta;
use App\Models\Brand;
use App\Models\AttributeSet;
use App\Models\Options;
use App\Models\Optionvalues;
use App\Models\Product;
use App\Models\Review;
use App\Models\ProductAttributes;
use App\Models\ProductOption;
use App\Models\Taxes;
use App\Models\Lang_core;
use App\Models\User;
use Image;
use Artisan;
use Hash;
class ProductController extends Controller {
     public function __construct() {
         parent::callschedule();
    }
     public function showproduct(){
        $lang = Lang_core::all();
        return view("admin.product.product")->with("lang",$lang);
     }

     public function showattset(){
        $lang = Lang_core::all();
        return view("admin.product.attributeset")->with("lang",$lang);
     }

     public function checktotalproduct(){
          $category =Product::orderBy('id','DESC')->where('is_deleted','0')->where("status",'1')->get();
          if(count($category)>1){
              return 0;
          }else{
              return 1;
          }
     }

     public function getoptionvalues($id){
         $optionvalues=Options::with("optionlist")->where("id",$id)->first();
         return json_encode($optionvalues);
     }

     public function getallproduct(){
            $data=Product::all();
            foreach($data as $d){
                $getmeta = Filemeta::where("model_id",$d->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
                $d->name = isset($getmeta->meta_value)?$getmeta->meta_value:'';
            }
            return json_encode($data);
     }
     

     public function showaddcatalog($id,$tab){

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
                }
                if($data->subcategory){
                    $brand=Brand::where("category_id",$data->subcategory)->where("is_delete",'0')->get();
                    foreach ($brand as $k) {
                           $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Brand")->where("meta_key","name")->first();
                           $k->brand_name = isset($getlang)?$getlang->meta_value:'';
                    }
                }
                $data->optionls=ProductOption::where("product_id",$id)->first();
                $data->attributels=ProductAttributes::where("product_id",$id)->get();
                $language =Lang_core::all();  
                foreach ($language as $k) {         
                    $getmeta = Filemeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","name")->where("lang",$k->code)->first();
                    $name = "name_".$k->code;
                    if($getmeta){
                        $data->$name = $getmeta->meta_value;
                    }else{
                        $data->$name = "";
                    }

                    $getmeta = Filemeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","description")->where("lang",$k->code)->first();
                    $name = "description_".$k->code;
                    if($getmeta){
                        $data->$name = $getmeta->meta_value;
                    }else{
                        $data->$name = "";
                    }

                    $getmeta = Filemeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","meta_keyword")->where("lang",$k->code)->first();
                    $name = "meta_keyword_".$k->code;
                    if($getmeta){
                        $data->$name = $getmeta->meta_value;
                    }else{
                        $data->$name = "";
                    }

                    $getmeta = Filemeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","colorname")->where("lang",$k->code)->first();
                    $name = "colorname_".$k->code;
                    if($getmeta){
                        $data->$name = $getmeta->meta_value;
                    }else{
                        $data->$name = "";
                    }
                }
                
          }
          $optionvalues=Options::with("optionlist")->where("is_deleted",'0')->get();
           

          $category=Categories::where("parent_category",0)->where("is_delete",'0')->get(); 
          foreach ($category as $k) {
               $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
               $k->name = isset($getlang)?$getlang->meta_value:'';
          }
          $r_id = Auth::user()->id;
          
          $tax=Taxes::all(); 
          foreach ($tax as $k) {
                $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Taxes")->where("meta_key","tax_name")->first();
                $k->tax_name = isset($getlang)?$getlang->meta_value:'';
          }   
          $lang = Lang_core::all();   
          //echo "<pre>";print_r($data);exit;  
          return view("admin.product.addproduct")->with("category",$category)->with("product_id",$id)->with("tab",$tab)->with("taxes",$tax)->with("data",$data)->with("subcategory",$subcategory)->with("brand",$brand)->with("optionvalues",$optionvalues)->with("lang",$lang);
     }

     public function getallsearchproduct(Request $request){
         if($request->get("id")==0){
              $data=Product::all();
              return json_encode($data);
         }
         else{
             $data=Product::where("category",$request->get("id"))->get();
              return json_encode($data);
         }
     }
     public function getproductprice($id){
        $data=Product::find($id);
        return json_encode($data);
     }

     public function productdatatable(){
         $category =Product::orderBy('id','DESC')->where('is_deleted','0')->where("status",'1')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('thumbnail', function ($category) {
                return asset('public/upload/product')."/".$category->basic_image;
            })
              ->editColumn('name', function ($category) {
                $getmeta = Filemeta::where("model_id",$category->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
                    return isset($getmeta->meta_value)?$getmeta->meta_value:'';
            })
            ->editColumn('price', function ($category) {
                return $category->selling_price;
            })           
            ->editColumn('action', function ($category) {                 
                  $editoption=url('admin/savecatalog',array('id'=>$category->id,'tab'=>'1')); 
                  $changestaus=url('admin/changeproductstatus',array('id'=>$category->id)); 
                  $deletecatlog=url('admin/deletecatlog',array('id'=>$category->id)); 
                   if($category->status=='1'){
                         $return = '<a onclick="delete_record(' . "'" . $deletecatlog. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5 btn btn-danger" style="color:#fff !important; margin-right:10px" data-original-title="Remove" style="margin-right: 10px;">Delete</a><a style="color:#fff !important; margin-right:10px" href="'.$changestaus.'" rel="tooltip" class="m-b-10 m-l-5 btn btn-success" data-original-title="Remove" style="margin-right: 10px;">Approve</a>';
                    }   
                    else{
                        $return = '<a onclick="delete_record(' . "'" . $deletecatlog. "'" . ')" rel="tooltip" style="color:#fff !important; margin-right:10px" class="m-b-10 m-l-5 btn btn-danger" style="color:#fff !important; margin-right:10px" data-original-title="Remove" style="margin-right: 10px;">Delete</a><a style="color:#fff !important; margin-right:10px" href="'.$changestaus.'" rel="tooltip" class="m-b-10 m-l-5 btn btn-danger" data-original-title="Remove" style="margin-right: 10px;">Reject</a>';
                    }              
                 
                 return $return;              
            })           
            ->make(true);
     }

     public function changeproductstatus($id){        
        if(Session::get('is_demo')=='1'){
            Session::flash('message','This function is currently disable as it is only a demo website, in your admin it will work perfect');
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
        $store=Product::find($id);
        if($store->status=='0'){
            $store->status='1';
        }
        else{
            $store->status='0';
        }
        $store->save();
        Session::flash('message',__('messages_error_success.product_status_update')); 
        Session::flash('alert-class', 'alert-success');
       return redirect()->back();
     }
      public function editproduct($id){
        $product=Product::find($id);        
        $language =Lang_core::all();  
        foreach ($language as $k) {         
            $getmeta = Filemeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","name")->where("lang",$k->code)->first();
            $name = "name_".$k->code;
            if($getmeta){
                $product->$name = $getmeta->meta_value;
            }else{
                $product->$name = "";
            }

            $getmeta = Filemeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","description")->where("lang",$k->code)->first();
            $name = "description_".$k->code;
            if($getmeta){
                $product->$name = $getmeta->meta_value;
            }else{
                $product->$name = "";
            }

            $getmeta = Filemeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","meta_keyword")->where("lang",$k->code)->first();
            $name = "meta_keyword_".$k->code;
            if($getmeta){
                $product->$name = $getmeta->meta_value;
            }else{
                $product->$name = "";
            }

            $getmeta = Filemeta::where("model_id",$id)->where("model_name","Product")->where("meta_key","colorname")->where("lang",$k->code)->first();
            $name = "colorname_".$k->code;
            if($getmeta){
                $product->$name = $getmeta->meta_value;
            }else{
                $product->$name = "";
            }
        }
        $category=Categories::where("parent_category",0)->where("is_delete",'0')->get();
        $subcategory=Categories::where("parent_category",$product->category)->where("is_delete",'0')->get();
        foreach ($subcategory as $k) {
                           $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                           $k->name = isset($getlang)?$getlang->meta_value:'';
        }
        $brand=Brand::where("category_id",$product->subcategory)->where("is_delete",'0')->get();
        foreach ($brand as $k) {
            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Brand")->where("meta_key","brand_name")->first();
            $k->brand_name = isset($getlang)?$getlang->meta_value:'';
        }
        $attribute=ProductAttributes::where("product_id",$id)->get();
        $optionvalue=ProductOption::where("product_id",$id)->first();
        $attributedrop=AttributeSet::whereHas('attributelist', function($q)use($product) {$q->where("is_delete",'0')->where("category",$product->category);})->where("is_deleted",'0')->get();
       foreach ($attributedrop as $k) {
            $getdata=Attributes::where("att_set_id",$k->id)->where("is_delete",'0')->where("category",$product->category)->get();
            $k->attributelist=$getdata;
        } 
        $optionvalues=Options::with("optionlist")->get();
        $tax=Taxes::all();
        $lang = Lang_core::all();

        return view("admin.product.edit.default")->with("product",$product)->with("product_attribute",$attribute)->with("product_option",$optionvalue)->with("attributedrop",$attributedrop)->with("optionvalues",$optionvalues)->with("category",$category)->with("subcategory",$subcategory)->with("brand",$brand)->with("tax",$tax)->with("lang",$lang);
     }
  
     public function productlist($id,$pro_id){
         if($pro_id==0){
             $category =Product::orderBy('id','DESC')->where("is_deleted",'0')->where("status",'1')->get();
         }
         else{
             $category =Product::orderBy('id','DESC')->where("is_deleted",'0')->where("status",'1')->where("subcategory",$id)->where("id","!=",$pro_id)->get();
         }
         
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('thumbnail', function ($category) {
                return asset('public/upload/product')."/".$category->basic_image;
            })
              ->editColumn('name', function ($category) {
                return $category->name;
            })
            ->editColumn('price', function ($category) {
                return $category->price;
            })           
                       
            ->make(true);
     }

    
    

     public function getsubcategory($id){
        $data=Categories::where("parent_category",$id)->where("is_delete",'0')->get();
        foreach ($data as $k) {
            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get("locale"))->where("model_name","Categories")->where("meta_key","name")->first();
                           $k->name = isset($getlang)?$getlang->meta_value:'';
        }
        return json_encode($data);
     }

   public function saveproduct(Request $request){
       // dd($request->all());
        if($request->get("product_id")!=0){
            $store=Product::find($request->get("product_id"));
        }
        else{
            $store=new Product();
        }      
        $store->category=$request->get("category");
        $store->subcategory=$request->get("subcategory");
        $store->brand=$request->get("brand");
        $store->tax_class=$request->get("texable");
        $store->status='1';
        $store->product_color=$request->get("colorpro");
        $store->save();
        $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"Product");
            $this->file_meta_update_payment_key($store->id,$k->code,"description",$request->get("description_".$k->code),"Product");
            $this->file_meta_update_payment_key($store->id,$k->code,"meta_keyword",$request->get("meta_keyword_".$k->code),"Product");
            $this->file_meta_update_payment_key($store->id,$k->code,"colorname",$request->get("colorname_".$k->code),"Product");
        }
        return redirect('admin/savecatalog/'.$store->id.'/2');
     }
     
    
     public function saveprice(Request $request){
        if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/2');
        }
        if($request->get("mrp")<$request->get("price")){ 
            Session::flash('message',__('messages_error_success.selling_mrp_vaildate')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/'.$request->get("product_id").'/2');
        }
        else{
            if($request->get("special_price")!=""){
                 if($request->get("price")<$request->get("special_price")){
                     Session::flash('message',__('messages_error_success.check_price')); 
                     Session::flash('alert-class', 'alert-danger');
                     return redirect('admin/savecatalog/'.$request->get("product_id").'/2');
                 }
                 if($request->get("spe_pri_start")==""&&$request->get("spe_pri_to")==""){
                     Session::flash('message',__('messages_error_success.sepical_price_vaildate')); 
                     Session::flash('alert-class', 'alert-danger');
                     return redirect('admin/savecatalog/'.$request->get("product_id").'/2');
                 }
            }
        }
        $store=Product::find($request->get("product_id"));
        $store->price=number_format((float)$request->get("price"), 2, '.', '');
        $store->selling_price=number_format((float)$request->get("price"), 2, '.', '');
        $store->weight = $request->get('weight');
        $store->MRP=number_format((float)$request->get("mrp"), 2, '.', '');
        
                $store->special_price=number_format((float)$request->get("special_price"), 2, '.', '');
                $store->special_price_start=$request->get("spe_pri_start");
                $store->special_price_to=$request->get("spe_pri_to");
        
        $store->save();
         parent::productupdate();
        return redirect('admin/savecatalog/'.$store->id.'/3');
     }

     public function saveinventory(Request $request){
        if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/3');
        }

        if($request->get("sku")==""){
            $store=Product::find($request->get("product_id"));
                $store->sku=$request->get("sku");
                $store->inventory=$request->get("inventory");
                $store->stock=$request->get("stock");
                $store->save();
                  return redirect('admin/savecatalog/'.$request->get("product_id").'/3');
        }else{
            $checksku=Product::where("sku",$request->get("sku"))->where("id","!=",$request->get("product_id"))->first();
              if(!isset($checksku)){
                $store=Product::find($request->get("product_id"));
                $store->sku=$request->get("sku");
                $store->inventory=$request->get("inventory");
                $store->stock=$request->get("stock");
                $store->save();
                return redirect('admin/savecatalog/'.$store->id.'/4');
             }
             Session::flash('message',__('messages_error_success.sku_already')); 
             Session::flash('alert-class', 'alert-danger');
             return redirect('admin/savecatalog/'.$request->get("product_id").'/3'); 
         }
              
     }

     public function saveproductimage(Request $request){
         if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/4');
        }
         $add_img=array();
        $store=Product::find($request->get("product_id"));
        $adddata=explode(",",$store->additional_image);
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
        if($request->get("additional_img")!=""){
             $add_img=array();
             $data=$request->get("additional_img");
            
             foreach (array_filter($data) as $k) {
                if(strstr($k,"http")==""){  
                        $data1 =$k;                 
                        list($type, $data1) = explode(';', $data1);
                        list(, $data1)      = explode(',', $data1);
                        $folderName = '/upload/product/';
                        $destinationPath = public_path() . $folderName;
                        $file_name=uniqid() . '.png';
                        $file = $destinationPath .$file_name;
                        $data = base64_decode($data1);
                        file_put_contents($file, $data);
                        $add_img[]=$file_name;                        
                }  
                else{
                        $arr=explode("/",$k);
                        $add_img[]=$arr[count($arr)-1];
                }            
             }
             if(!empty(array_filter($add_img))){
                 $store->additional_image=implode(',',$add_img);
             }
              
        }
             
        $store->save();
        if(!empty($adddata)){
            foreach ($adddata as $k) {
                if(!in_array($k,$add_img)){
                    $image_path = public_path() ."/upload/product/".$k;
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
        
        return redirect('admin/savecatalog/'.$store->id.'/5');
     }

   
     
     public function saveproductattibute(Request $request){
      // dd($request->all());exit;
        if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/5');
        }
            $arr=array_values($request->get("attributeset"));
           if(count($arr)==0){
                    return redirect('admin/savecatalog/'+$request->get("product_id")+'/6');
           }
        
        $lang = Lang_core::all();
        foreach ($lang as $l) {
            if(count($arr)!=0){
                $checkproattri=ProductAttributes::where("product_id",$request->get("product_id"))->where("lang",$l->code)->delete();
                for ($i=0; $i <count($arr); $i++) {            
                             $store=new ProductAttributes();
                             $store->product_id=$request->get("product_id");
                             $store->attributeset=$arr[$i][$l->code]['set'];
                             $store->attribute=implode(",",$arr[$i][$l->code]['label']);
                             $store->value=implode(",", $arr[$i][$l->code]['value']);
                             $store->lang = $l->code;
                             $store->save();
                               
                }
            }          
        }
        
         return redirect('admin/savecatalog/'.$request->get("product_id").'/6');
     }

     public function saveproductoption(Request $request){
        
         if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/6');
        }
        if($request->get("totaloption")==0){
            return redirect('admin/savecatalog/'.$request->get("product_id").'/7');
        }
        $arr=array_values($request->get("options"));
        
        $name=array();
        $type=array();
        $required=array();
        $label=array();
        $price=array();
         for ($i=0; $i <count($arr); $i++) {  
             if(isset($arr[$i]['name'])&&isset($arr[$i]['type'])&&isset($arr[$i]['required'])&&isset($arr[$i]['label'])&&isset($arr[$i]['price'])){
                 $name[]=$arr[$i]['name'];
                    $type[]=$arr[$i]['type'];
                    $required[]=$arr[$i]['required'];
                    $label[]=implode(",", $arr[$i]['label']);
                    $price[]=implode(",", $arr[$i]['price']);
             }
                
         }   
         $checkoption=ProductOption::where("product_id",$request->get("product_id"))->delete();
         $store=new ProductOption();    
         $store->product_id=$request->get("product_id");
         $store->name=implode(",",$name);
         $store->type=implode(",",$type);
         $store->is_required=implode(",",$required);
         $store->label=implode("#",$label);
         $store->price=implode("#",$price);
         $store->save();
          return redirect('admin/savecatalog/'.$request->get("product_id").'/7');
        
     }
    

  public function saverealtedprice(Request $request){
   
     if($request->get("product_id")==0){
            Session::flash('message',__('messages_error_success.general_form_msg')); 
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/savecatalog/0/7');
        }
        if($request->get("totaloption")==0){
            Session::flash('message',__('messages_error_success.pro_add')); 
            Session::flash('alert-class', 'alert-success');
            return redirect('admin/product');
        }
        $store=Product::find($request->get("product_id"));
        $store->related_product=implode(",",$request->get('related_id'));
        $store->save();
        
          Session::flash('message',__('messages_error_success.pro_add')); 
          Session::flash('alert-class', 'alert-success');
          return redirect('admin/product');
  }
     public function getattibutevalue($id){
        $data=Attributevalues::where("att_id",$id)->get();
        return json_encode($data);
     }


     public function getbrandbyid($id){
        $data=Brand::where("category_id",$id)->get();
        foreach ($data as $k) {
            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Brand")->where("meta_key","name")->first();
            $k->brand_name = isset($getlang)?$getlang->meta_value:'';
        }
        return json_encode($data);
     }
  
     public function deletecatlog($id){
        $data=Product::find($id);
        $data->is_deleted='1';
        $data->save();
        Session::flash('message',__('messages_error_success.catalog_del')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
     }
     
   
     public function indexoption(){
        $lang = Lang_core::all();
        return view("admin.product.options")->with("lang",$lang);
     }

     public function Optiondatatable(){
       $option =Options::orderBy('id','DESC')->where("is_deleted",'0')->get();
         return DataTables::of($option)
            ->editColumn('id', function ($option) {
                return $option->id;
            })
            ->editColumn('name', function ($option) {
                 $getlang = FileMeta::where("model_id",$option->id)->where("lang",Session::get('locale'))->where("model_name","Options")->where("meta_key","name")->first();
                 return isset($getlang)?$getlang->meta_value:$k->name;
            })
            ->editColumn('type', function ($option) {
                if($option->type==1){
                    $status=__('messages.dropdown');
                }
                else if($option->type==2){
                    $status=__('messages.checkbox');
                }
                else if($option->type==3){
                    $status=__('messages.radiobutton');
                }else{
                    $status=__('messages.multiple_select');
                }
                return $status;
            })            
            ->editColumn('action', function ($option) {   
                 $editoption=url('admin/editoption',array('id'=>$option->id)); 
                 $deloption=url('admin/deleteoption',array('id'=>$option->id));              
                 $return = '<a  href="'.$editoption.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deloption. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';
                 return $return;              
            })           
            ->make(true);
     }

     public function showaddoption(){
        $lang = Lang_core::all();
        return view("admin.product.addoptionvalues")->with("lang",$lang);
     }

     public function saveoption(Request $request){
         if($request->get("id")==0){
                 $store=new Options();
         }else{
             $store=Options::find($request->get("id"));
         }
           $lang = Lang_core::all();
          
           $store->type=$request->get("type");
           $store->is_required=$request->get("is_required")?'1':'0';
           $store->save();
           foreach ($lang as $k) {
                   $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"Options");
           }
           Optionvalues::where("option_id",$store->id)->delete();
           
           foreach ($lang as $k) {
                $label=$request->get('label_'.$k->code);
                $price=$request->get('price_'.$k->code);  
                for($i=0;$i<count($label);$i++){
                      $add=new Optionvalues();
                      $add->option_id=$store->id;
                      $add->label=$label[$i];
                      $add->price=$price[$i];
                      $add->lang = $k->code;
                      $add->save();
                } 
           }          
           Session::flash('message',__('messages_error_success.option_add_success')); 
           Session::flash('alert-class', 'alert-success');
           return redirect('admin/options');
     }

     public function editoption($id){
        $option=Options::find($id);
        $lang = Lang_core::all();
        foreach ($lang as $k) {
            $getlang = FileMeta::where("model_id",$id)->where("lang",$k->code)->where("model_name","Options")->where("meta_key","name")->first();
            if($getlang){
                $name = "name_".$k->code;
                $option->$name = isset($getlang)?$getlang->meta_value:'';
            }
        }
        $optionvalue=Optionvalues::where("option_id",$id)->get();
        return view("admin.product.editoption")->with("option",$option)->with("optionvalue",$optionvalue)->with('lang',$lang);
     }

     public function updateoption(Request $request){
          $label=$request->get('label');
          $price=$request->get('price');       
          $store=Options::find($request->get("option_id"));
          $store->name=$request->get("option_name");
          $store->type=$request->get("option_type");
          $store->is_required=$request->get("option_required");
          $store->save();
          $delrecord=Optionvalues::where("option_id",$request->get("option_id"))->delete();
          for($i=0;$i<count($request->get('label'));$i++){
              $add=new Optionvalues();
              $add->option_id=$request->get("option_id");
              $add->label=$label[$i];
              $add->price=$price[$i];
              $add->save();
          }
          Session::flash('message',__('messages_error_success.option_update_success')); 
          Session::flash('alert-class', 'alert-success');
          return redirect('admin/options');
     }

     //attribute

     

     public function showreview(){
        $lang = Lang_core::all();
         return view("admin.product.review")->with("lang",$lang);
     }

     public function reviewdatatable($id){

        $review=array();
        if($id=="0"){
            $review =Review::with('product','userdata')->orderBy('id','DESC')->get();
        }
        else{
            $review =Review::with('product','userdata')->where("product_id",$id)->orderBy('id','DESC')->get();
        }
         
         return DataTables::of($review)
            ->editColumn('id', function ($review) {
                return $review->id;
            })
            ->editColumn('pro_name', function ($review) {
                 $getmeta = Filemeta::where("model_id",$review->product_id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get("locale"))->first();
                return isset($getmeta->meta_value)?$getmeta->meta_value:''; 
              //  return $review->product->name;
            })
            ->editColumn('rev_name', function ($review) {
               if($review->userdata!=""){
                     return $review->userdata->name;
                }
                else{
                    return "";
                }
            })
             ->editColumn('rating', function ($review) {
                return $review->ratting.'/5';
            })
            ->editColumn('review', function ($review) {
                return $review->review;
            })   
            ->editColumn('action', function ($attribute) { 
                 
                 $deletereview=url('admin/deletereview',array('id'=>$attribute->id));
                 $editoption=url('admin/changereview',array('id'=>$attribute->id));
                 $return = '<a onclick="delete_record(' . "'" . $deletereview. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';
                 return $return;              
            })           
            ->make(true);
     }

     public function changereview($id){
        $store=Review::find($id);
        if($store->is_approved=='1'){
            $store->is_approved='0';
        }
        else{
            $store->is_approved='1';
        }
        $store->save();
         Session::flash('message',__('messages_error_success.review_status_change')); 
         Session::flash('alert-class', 'alert-success');
         return redirect()->back();
     }

     public function deleteoption($id){
         $data=Options::find($id);
         $data->is_deleted='1';
         $data->save();
         Session::flash('message',__('messages_error_success.option_delete')); 
         Session::flash('alert-class', 'alert-success');
         return redirect('admin/options');
     }

   
     public function deletereview($id){
        $data=Review::find($id);
        $data->delete();
        Session::flash('message',__('messages_error_success.review_del_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
     }

     public function unapprove_product(){
         $lang = Lang_core::all();
         return view("admin.product.unapproveproduct")->with("lang",$lang);
     }

     public function unapproveproductdataTable(){
         $category =Product::orderBy('id','DESC')->where('is_deleted','0')->where("status",'0')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
             ->editColumn('addedby', function ($category) {
                $data=User::find($category->user_id);

                return isset($data)?$data->first_name:'';
            })
            ->editColumn('thumbnail', function ($category) {
                return asset('public/upload/product')."/".$category->basic_image;
            })
              ->editColumn('name', function ($category) {
                $getmeta = Filemeta::where("model_id",$category->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
                    return isset($getmeta->meta_value)?$getmeta->meta_value:'';
            })
            ->editColumn('price', function ($category) {
                return $category->price;
            })           
            ->editColumn('action', function ($category) {                 
                 
                  $changestaus=url('admin/changeproductstatus',array('id'=>$category->id)); 
                 
                   if($category->status=='1'){
                        
                        $return = '<a href="'.$changestaus.'" rel="tooltip" class="m-b-10 m-l-5 btn btn-success" data-original-title="Remove" style="color:#fff !important; margin-right: 10px;">Approve</a>';
                    }   
                    else{ 
                        
                        $return = '<a href="'.$changestaus.'" rel="tooltip" class="m-b-10 m-l-5 btn btn-danger" data-original-title="Remove" style="color:#fff !important; margin-right: 10px;">Reject</a>';
                    }              
                 
                 return $return;              
            })           
            ->make(true);
     }
}