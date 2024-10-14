<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Brand;
use App\Models\City;
use App\Models\FileMeta;
use App\Models\Lang_core;
use App\Models\Sepicalcategories;
use App\Models\Subcategory;
use Image;
use Hash;
use Auth;
class Categorycontroller extends Controller {
     public function __construct() {
         parent::callschedule();
    }


    public function res_category()
    {  
        $id = Auth::user()->id;
        // die("AAA");
        /* $category=Categories::where("is_delete",'0')->get();
         foreach ($category as $k) {
            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
            $k->name = isset($getlang)?$getlang->meta_value:'';
         }
         $brand=Brand::where("is_delete",'0')->get();  
         foreach ($brand as $k) {
            $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Brand")->where("meta_key","name")->first();
            $k->brand_name = isset($getlang)?$getlang->meta_value:'';
        }
        $lang = Lang_core::all();     
        return view('seller.categories.category')->with("id",$id);*/
        $lang = Lang_core::all(); 
        return view('seller.categories.category')->with("id",$id)->with("lang",$lang);
    }

    public function res_categorydatatable()
    {

        $id = Auth::user()->id;
        $category =Categories::orderBy('id','DESC')->where("is_delete",'0')->where("res_id",$id)->get();
        return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
           
            ->editColumn('cat_name', function ($category) {
                return $category->cat_name;
            })    
            
            ->editColumn('action', function ($category) 
            {
                $update_record=url('seller/update_res_category/'.$category->id);
                //$subcategory=url('seller/sub_category/'.$category->id);
                $addproduct=url('seller/product/'.$category->id);
               
                 $deleteuser=url('seller/delete_res_category',array('id'=>$category->id));
                //$return = '<a href='.$update_record.'  rel="tooltip" class="m-b-10 m-l-5"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record('."'".$deleteuser."'".')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a  href="'.$subcategory.'" rel="tooltip" title="Sub Category" class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-code-fork f-s-25" style="margin-right: 10px;font-size: x-large;color:black"></i></a>';
                $return = '<a href='.$update_record.'  rel="tooltip" class="m-b-10 m-l-5 btn btn-success" style="color:#fff !important; margin-right:10px">Edit</a><a onclick="delete_record('."'".$deleteuser."'".')" rel="tooltip"  class="m-b-10 m-l-5 btn btn-danger" style="color:#fff !important; margin-right:10px" data-original-title="Remove" style="margin-right: 10px;">Delete</a><a  href="'.$addproduct.'" rel="tooltip" title="Add Product" class="btn btn-primary btn-flat m-b-10 m-l-5" style="color: white !important;" data-original-title="Remove">Add Product</a>';
                 return $return;              
            })           
            ->make(true);
    }
    public function update_res_category($id)
    {
        $res_id = Auth::user()->id;
        $category =Categories::where("id",$id)->where("res_id",$res_id)->where("is_delete",'0')->first();
     
        $lang = Lang_core::all(); 
        return view('seller.categories.add_category')->with("category",$category)->with("lang",$lang);
       
    } 

    public function post_update_category(Request $request)
    {
        if($request->get("id")!="0")
        {
            $store = Categories::find($request->get("id"));
            $old_img = $store->image;
            $img_url = $store->image;
        }else{
            $store=new Categories();
        }
        if ($request->file('image')) 
        {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $folderName = '/upload/category/';
            $picture = "category_".time() . '.' . $extension;
            $destinationPath = public_path() . $folderName;
            $request->file('image')->move($destinationPath, $picture);
            $store->image =$picture;
            if($request->get("old_image")!=""){
                $image_path = public_path() ."/upload/category/".$old_img;
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
        $store->cat_name = $request->get("cat_name");
        $store->res_id = Auth::user()->id;
        $store->save();
        $language =Lang_core::all();
        if($request->get("id")!="0")
        {
            Session::flash('message',__('messages_error_success.category_update_success')); 
        }else{
            Session::flash('message',__('messages_error_success.category_add_success')); 
        }    
        Session::flash('alert-class', 'alert-success');
        return redirect()->route('res_category');
       
    } 
     public function delete_res_category($id)
    {
        $res_id=Auth::user()->id;
        $store=Categories::where("id",$id)->where("res_id",$res_id)->first();
        $store->is_delete='1';
        $store->save();
        /*$product=Product::orwhere('category',$id)->orwhere("subcategory",$id)->get();
        foreach ($product as $k) {
            $da=Product::where("id",$k->id)->update(["is_deleted"=>'1']);
        }*/
        Session::flash('message',__('messages_error_success.category_del')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }
    public function sub_category($id)
    {
        $res_id = Auth::user()->id;
        $lang = Lang_core::all(); 
        return view('seller.categories.subcategory')->with("id",$id)->with("lang",$lang);
    }
    public function res_subcategorydatatable($id)
    {

        $res_id = Auth::user()->id;
        $category =Subcategory::with('category')->orderBy('id','DESC')->where('cat_id',$id)->where('res_id',$res_id)->where("is_delete",'0')->get();
        return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
             ->editColumn('image', function ($category) {
                return asset('public/upload/subcategory').'/'.$category->image;
            })
            ->editColumn('cat_id', function ($category)
            {
                return $category->category->cat_name;
            })  
            ->editColumn('sub_cat_name', function ($category) {
                return $category->sub_cat_name;
            })    
            
            ->editColumn('action', function ($category) 
            {
                $update_record=url('seller/update_res_subcategory/'.$category->cat_id.'/'.$category->id);
                $subcategory=url('seller/sub_category/'.$category->id);
                $deleteuser=url('seller/delete_res_subcategory',array('id'=>$category->id));
                $return = '<a href='.$update_record.'  rel="tooltip" class="m-b-10 m-l-5"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record('."'".$deleteuser."'".')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a  href="'.$subcategory.'" rel="tooltip" title="Sub Category" class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-code-fork f-s-25" style="margin-right: 10px;font-size: x-large;color:black"></i></a>';
                 return $return;              
            })           
            ->make(true);
    }
    public function update_res_subcategory($cat_id,$id)
    {
        $res_id = Auth::user()->id;
        $category=Categories::where("res_id",$res_id)->where("is_delete",'0')->get();
        /*echo "<pre>";
        print_r($category);
        die();*/
        $subcategory =Subcategory::where("id",$id)->where("cat_id",$cat_id)->where("res_id",$res_id)->first();
      
        $lang = Lang_core::all(); 
        return view('seller.categories.add_subcategory')->with("category",$category)->with("subcategory",$subcategory)->with("lang",$lang);
        
    }
    public function post_update_subcategory(Request $request)
    {
        if($request->get("id")!="0")
        {
            $store = Subcategory::find($request->get("id"));
            $old_img = $store->image;
        }else{
            $store=new Subcategory();
        }
        if ($request->file('image')) 
        {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $folderName = '/upload/subcategory/';
            $picture = "category_".time() . '.' . $extension;
            $destinationPath = public_path() . $folderName;
            $request->file('image')->move($destinationPath, $picture);
            $store->image =$picture;
            if($request->get("old_image")!=""){
                $image_path = public_path() ."/upload/subcategory/".$old_img;
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
        $store->sub_cat_name = $request->get("sub_cat_name");
        $store->cat_id = $request->get("cat_id");
        $store->res_id = Auth::user()->id;
        $store->save();
        $language =Lang_core::all();
        if($request->get("id")!="0")
        {
            Session::flash('message',__('messages_error_success.subcategory_update_success')); 
        }else{
            Session::flash('message',__('messages_error_success.subcategory_add_success')); 
        }    
        Session::flash('alert-class', 'alert-success');
        return redirect()->route('sub_category',array("id"=>$request->get("cat_id")));
       
    } 
    public function delete_res_subcategory($id)
    {
        $res_id=Auth::user()->id;
        $store=Subcategory::where("id",$id)->where("res_id",$res_id)->first();
        $store->is_delete='1';
        $store->save();
       
        Session::flash('message',__('messages_error_success.category_del')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    
    public function index(){  
         
        $lang = Lang_core::all();     
        return view('admin.categories.category')->with("lang",$lang);
    }

    public function getallsubcategory()
    {
        $res_id=Auth::id();
        $data=Categories::where("res_id",$res_id)->where("is_delete",'0')->get();
        return json_encode($data);
    }

    public function categorydatatable(){
         $category =Categories::orderBy('id','DESC')->where("is_delete",'0')->where('parent_category','0')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('name', function ($category) {
                 $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                return isset($getlang)?$getlang->meta_value:'';
            })    
             ->editColumn('image', function ($category) {
                return asset('public/upload/category').'/'.$category->image;
            }) 
            ->editColumn('delivery_charges', function ($category) {
                return $category->delivery_charges;
            })
            ->editColumn('action', function ($category) {
                 //$subcategory=url('admin/subcategory',array('id'=>$category->id));
                 $deleteuser=url('admin/deletecategory',array('id'=>$category->id));
                // $return = '<a onclick="editcategory('.$category->id.')"   rel="tooltip" class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editcategory"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a  href="'.$subcategory.'" rel="tooltip" title="Sub Category" class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-code-fork f-s-25" style="margin-right: 10px;font-size: x-large;color:black"></i></a>';
                 $return = '<a onclick="editcategory('.$category->id.')" style="color:#fff !important;margin-right: 10px;"  rel="tooltip" class="m-b-10 m-l-5 btn-success btn" data-original-title="Remove" data-toggle="modal" data-target="#editcategory">Edit</a><a onclick="delete_record(' . "'" . $deleteuser. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5 btn-danger btn" style="color:#fff !important; " data-original-title="Remove" >Delete</a>';
                 return $return;              
            })           
            ->make(true);
    } 

    public function addcategory(Request $request){
         if ($files = $request->file('category_image')) {
                        $file = $request->file('category_image');
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension() ?: 'png';
                        $folderName = '/upload/category/';
                        $picture = "category_".time() . '.' . $extension;
                        $destinationPath = public_path() . $folderName;
                        $request->file('category_image')->move($destinationPath, $picture);
                        $img_url =$picture;
        }
        else{
                    $img_url="";
        }
        $store=new Categories();
        $store->image = $img_url;
         $store->parent_category =0;
        $store->delivery_charges = $request->get("delivery_charges");
        $store->save();
        $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"Categories");
        }
       Session::flash('message',__('messages_error_success.category_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/category");
    }

    public function getcategorybyid($id){ 
      $arr = array();
      $data = Categories::find($id);
      $arr['delivery_charges'] = $data->delivery_charges;
      $arr['image']=$data->image;
      $arr['id'] = $id;
     $language =Lang_core::all();  
          foreach ($language as $k) {         
                $getmeta = FileMeta::where("model_id",$id)->where("model_name","Categories")->where("meta_key","name")->where("lang",$k->code)->first();
                if($getmeta){
                   $arr["name_".$k->code] = $getmeta->meta_value;
                }
          }    
        $data = array("data"=>$arr,"language"=>$language);
        return json_encode($data);
       //return json_encode($arr);
    }

    public function addsepicalcategory(){
        $category=Categories::where("parent_category",0)->where("is_delete",'0')->get();
        foreach ($category as $k) {
             $getmeta = FileMeta::where("model_id",$k->id)->where("model_name","Categories")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
                if($getmeta){
                   $k->name = $getmeta->meta_value;
                }else{
                    $k->name = "";
                }
        }
        $lang = Lang_core::all();
        
        return view("admin.sepical.add")->with("category",$category)->with("lang",$lang);
    }

    public function storesepicalcategory(Request $request){
         if ($files = $request->file('image')) {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/category/image/';
                $picture = "category_".time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('image')->move($destinationPath, $picture);
                $img_url =$picture;
            }
            else{
                    $img_url="";
            }
        $store=new Sepicalcategories();
        $store->category_id=$request->get("category");
        $store->image=$img_url;
        $store->save();
         $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"title",$request->get("title_".$k->code),"Sepicalcategories");
             $this->file_meta_update_payment_key($store->id,$k->code,"description",$request->get("description_".$k->code),"Sepicalcategories");
        }
        Session::flash('message',__('messages_error_success.sepcategory_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/sepical_category");
    }

    public function updatecategory(Request $request)
    {
        $store = Categories::find($request->get("id"));
         $old_img = $store->image;
        $img_url = $store->iamge;
        if ($request->file('category_image')) {
                        $file = $request->file('category_image');
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension() ?: 'png';
                        $folderName = '/upload/category/';
                        $picture = "category_".time() . '.' . $extension;
                        $destinationPath = public_path() . $folderName;
                        $request->file('category_image')->move($destinationPath, $picture);
                        $store->image =$picture;
                        if($old_img!=""){
                            $image_path = public_path() ."/upload/category/".$old_img;
                            if(file_exists($image_path)) {
                                try{
                                     unlink($image_path);
                                }
                                catch(\Exception $e)
                                {
                                    
                                }                            
                            }
        }}
        $store->delivery_charges = $request->get("delivery_charges");
        $store->save();
          
        $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($request->get("id"),$k->code,"name",$request->get("name_".$k->code),"Categories");
        }
        Session::flash('message',__('messages_error_success.category_update_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    public function subindex($id){
        $name = "";
        $getlang = FileMeta::where("model_id",$id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
        if($getlang){
           $name = isset($getlang)?$getlang->meta_value:$k->name;
        }     
        $language =Lang_core::all();       
        return view("admin.categories.subcategory")->with("parent_id",$id)->with("parent_name",$name)->with("lang",$language);
    }

    public function subdatatable($id){
      $category =Categories::orderBy('id','DESC')->where("is_delete",'0')->where('parent_category',$id)->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('name', function ($category) {
                $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                return isset($getlang)?$getlang->meta_value:'';
                //return $category->name;
            })           
            ->editColumn('action', function ($category) { 
                 $brand=url('admin/brand',array('id'=>$category->id));
                 $deletesub=url('admin/deletecategory',array('id'=>$category->id));
                 $return = '<a onclick="editcategory('.$category->id.')"  rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editsubcategory"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deletesub. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a  href="'.$brand.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove"><i class="fa fa-code-fork f-s-25" style="font-size: x-large;color:black"></i></a>';
                 return $return;              
            })           
            ->make(true);
    }

    public function subaddcategory(Request $request){
       $store=new Categories();
       $store->parent_category=$request->get("parentid");
       $store->save();
       $language =Lang_core::all();
       foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"Categories");
       }
       Session::flash('message',__('messages_error_success.subcat_add_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/subcategory/".$store->parent_category);
    }

    public function brandindex($id){
        $data=Categories::find($id);
        $parent=Categories::find($data->parent_category);
        $city=City::where("is_delete",'0')->get();
        $getlang = FileMeta::where("model_id",$parent->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
        $parent_name ="";
        if($getlang){
           $parent_name = isset($getlang)?$getlang->meta_value:'';
        }
        $getlang = FileMeta::where("model_id",$id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
        $subcategory ="";
        if($getlang){
           $subcategory = isset($getlang)?$getlang->meta_value:'';
        }
        foreach ($city as $k) {
             $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","City")->where("meta_key","name")->first();
        
           $k->name = isset($getlang)?$getlang->meta_value:'';
        
        }
        $language =Lang_core::all();
        return view("admin.categories.brand")->with("city",$city)->with("subcategoryid",$id)->with("parent_name",$parent_name)->with("subcategory",$subcategory)->with("parent_ids",$parent->id)->with("lang",$language);
    }

    public function branddatatable($id){
       $category =Brand::orderBy('id','DESC')->where("is_delete",'0')->where('category_id',$id)->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('name', function ($category) {
              $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Brand")->where("meta_key","name")->first();
                return isset($getlang)?$getlang->meta_value:'';
            }) 
            ->editColumn('image', function ($category) {
                if($category->image){
                    return asset('public/upload/category/banner').'/'.$category->image;
                }
            }) 
            ->editColumn('city', function ($category) {
                $getlang = FileMeta::where("model_id",$category->city)->where("lang",Session::get('locale'))->where("model_name","City")->where("meta_key","name")->first();
                 return isset($getlang)?$getlang->meta_value:'';
            })
            
                     
            ->editColumn('action', function ($category) { 
                 $brand=url('admin/brand',array('id'=>$category->id));
                 $del_brand=url('admin/deletebrand',array('id'=>$category->id));
                 $addbanner=url('admin/savebanner',array('category_id'=>$category->category_id,"id"=>$category->id));
                 $removebanner=url('admin/removebanner',array("id"=>$category->id));
                 $return = '<a onclick="editbrand('.$category->id.')"  rel="tooltip" class="m-b-10 m-l-5" data-original-title="Remove" data-toggle="modal" data-target="#editbrand"><i class="fa fa-edit f-s-25" style="margin-right: 10px;font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $del_brand. "'" . ')" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a><a  href="'.$addbanner.'" rel="tooltip"  class="btn btn-primary" data-original-title="banner" style="margin-right: 10px;color: white !important;">'.__('messages.Banner').'</a><a onclick="delete_record(' . "'" . $removebanner. "'" . ')" rel="tooltip"  class="btn btn-danger" data-original-title="Remove" style="margin-right: 10px;color:white !important">'.__('messages.removebanner').'</a>';
                 return $return;              
            })           
            ->make(true);
    }

    public function addbrand(Request $request){
         $store=new Brand();
         $store->category_id=$request->get("category_id");
         $store->city=$request->get("city");
         $store->save();
         $language =Lang_core::all();
         foreach ($language as $k) {
              $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"Brand");
         }   

         Session::flash('message',__('messages_error_success.brand_add_success')); 
         Session::flash('alert-class', 'alert-success');
         return redirect("admin/brand/".$store->category_id);
    }

    public function getbrandbyname($id){
       $data=Brand::find($id);
       $arr = array();
       $arr['id'] = $id;
       $arr['city'] = $data->city;
       $language =Lang_core::all(); 
          foreach ($language as $k) {         
                $getmeta = FileMeta::where("model_id",$id)->where("model_name","Brand")->where("meta_key","name")->where("lang",$k->code)->first();
                if($getmeta){
                   $arr["name_".$k->code] = $getmeta->meta_value;
                }
          } 

       $data = array("data"=>$arr,"language"=>$language);
       return json_encode($data);
    }

    public function updatebrand(Request $request){
         $store=Brand::find($request->get("id"));
         $store->city=$request->get("city");
         $store->save();
         $language =Lang_core::all();
         foreach ($language as $k) {
              $this->file_meta_update_payment_key($store->id,$k->code,"name",$request->get("name_".$k->code),"Brand");
         } 
         Session::flash('message',__('messages_error_success.brand_update_success')); 
         Session::flash('alert-class', 'alert-success');
         return redirect("admin/brand/".$store->category_id);
    }

    public function viewcategory(){
       $category=Categories::all();
       $brand=Brand::all();
       return view("admin.Categories.viewcategory")->with("category",$category)->with("brand",$brand);
    }

    public function sepical_category(){
       $language =Lang_core::all();  
        return view("admin.sepical.default")->with("lang",$language);
    }

    public function sepicalcategorytable(){
        $category =Sepicalcategories::orderBy('id','DESC')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('image', function ($category) {
                return asset('public/upload/category/image')."/".$category->image;
            }) 
            ->editColumn('title', function ($category) {
                $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Sepicalcategories")->where("meta_key","title")->first();
                return isset($getlang)?$getlang->meta_value:'';
            })
            ->editColumn('category', function ($category) {
                $getlang = FileMeta::where("model_id",$category->category_id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                return isset($getlang)?$getlang->meta_value:'';
            })
            ->editColumn('description', function ($category) {
                 $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Sepicalcategories")->where("meta_key","description")->first();
                return isset($getlang)?$getlang->meta_value:'';
            })           
            ->editColumn('action', function ($category) { 
             
                $editoption=url('admin/editsepicalcategory',array('id'=>$category->id)); 
                $editchange=url('admin/sepicalchange',array('id'=>$category->id));
                if($category->is_active=='1'){
                    $color="green";
                }   
                else{
                    $color="red";
                }       
                 return '<a href="'.$editoption.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-edit f-s-25" style="font-size: x-large;"></i></a><a href="'.$editchange.'" rel="tooltip"  class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-ban f-s-25" style="font-size: x-large;color:'.$color.'"></i></a>';              
            })           
            ->make(true);
    }

    public function editsepicalcategory($id){
        $data=Sepicalcategories::find($id);

        $category=Categories::where("parent_category",0)->where("is_delete",'0')->get();
        $language =Lang_core::all();
        foreach ($language as $k) {
                $getmeta = FileMeta::where("model_id",$id)->where("model_name","Sepicalcategories")->where("meta_key","title")->where("lang",$k->code)->first();
                $title ="title_".$k->code;
                if($getmeta){
                  $data->$title = $getmeta->meta_value;
                }else{
                  $data->$title = "";
                }
                $getmeta = FileMeta::where("model_id",$id)->where("model_name","Sepicalcategories")->where("meta_key","description")->where("lang",$k->code)->first();
                $description = "description_".$k->code;
                if($getmeta){
                   $data->$description = $getmeta->meta_value;
                }else{
                    $data->$description = "";
                }
        }

       // echo "<pre>";print_r($data);exit;
        foreach ($category as $k) {
             $getmeta = FileMeta::where("model_id",$k->id)->where("model_name","Categories")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
                if($getmeta){
                   $k->name = $getmeta->meta_value;
                }else{
                    $k->name = "";
                }
        }
        return view("admin.sepical.edit")->with("data",$data)->with("category",$category)->with("lang",$language);
    }

    public function updatesepicalcategory(Request $request){
         if ($files = $request->file('image')) {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $folderName = '/upload/category/image/';
                $picture = "category_".time() . '.' . $extension;
                $destinationPath = public_path() . $folderName;
                $request->file('image')->move($destinationPath, $picture);
                $img_url =$picture;
            }
            else{
                    $img_url=$request->get("real_image");
            }
        $store=Sepicalcategories::find($request->get("id"));
        $img=$store->image;
        $store->category_id=$request->get("category");
        $store->image=$img_url;
        $store->save();
         if($img!=$img_url){
            $image_path="";
            if($img!=""){
                $image_path = public_path() ."/upload/category/image/".$img;
            }
            if(file_exists($image_path)) {
                unlink($image_path);
            }
        }

         $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"title",$request->get("title_".$k->code),"Sepicalcategories");
             $this->file_meta_update_payment_key($store->id,$k->code,"description",$request->get("description_".$k->code),"Sepicalcategories");
        }
        Session::flash('message',__('messages_error_success.sepcategory_update_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/sepical_category");
    }

    public function sepicalchange($id){
        $data=Sepicalcategories::all();
        foreach ($data as $ke) {
           $ke->is_active='0';
           $ke->save();
        }
        $store=Sepicalcategories::find($id);
        $store->is_active='1';
        $store->save();
         Session::flash('message',__('messages_error_success.sepcategory_change_success')); 
       Session::flash('alert-class', 'alert-success');
       return redirect("admin/sepical_category");
    }

    public function deletecategory($id)
    {
        $store=Categories::find($id);
        $store->is_delete='1';
        $store->save();
        $product=Product::orwhere('category',$id)->orwhere("subcategory",$id)->get();
        foreach ($product as $k) {
            $da=Product::where("id",$k->id)->update(["is_deleted"=>'1']);
        }
        Session::flash('message',__('messages_error_success.category_del')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    public function deletebrand($id){
       $store=Brand::find($id);
       $store->is_delete='1';
       $store->save();
       $product=Product::where('brand',$id)->get();
        foreach ($product as $k) {
            $da=Product::where("id",$k->id)->update(["is_deleted"=>'1']);
        }
       Session::flash('message',__('messages_error_success.brand_del')); 
       Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    public function savebanner($category_id,$brand_id){
        $data=Brand::find($brand_id);
        $lang = Lang_core::all();
        return view("admin.categories.savebanner")->with("category_id",$category_id)->with("brand_id",$brand_id)->with("data",$data)->with("lang",$lang);
    }

    public function updatebarndbanner(Request $request){
           $data=Brand::find($request->get("brand_id"));
           if($data){
                 $rel_url=$data->image;
                 if ($request->hasFile('banner')) 
                  {
                     $file = $request->file('banner');
                     $filename = $file->getClientOriginalName();
                     $extension = $file->getClientOriginalExtension() ?: 'png';
                     $folderName = '/upload/category/banner/';
                     $picture = time() . '.' . $extension;
                     $destinationPath = public_path() . $folderName;
                     $request->file('banner')->move($destinationPath, $picture);
                     $data->image =$picture;                
                      $image_path = public_path() ."/upload/category/banner/".$rel_url;
                        if(file_exists($image_path)&&$rel_url!="") {
                            try {
                                 unlink($image_path);
                            }
                            catch(Exception $e) {
                              
                            }                        
                      }
                 }
                 $data->save();
                 Session::flash('message',__('messages.Brand Banner Update Successfully')); 
                 Session::flash('alert-class', 'alert-success');
        
           }else{
                  Session::flash('message',__('messages.Brand Not Found')); 
                  Session::flash('alert-class', 'alert-danger');
           }
           return redirect("admin/brand".'/'.$request->get("category_id"));
    }

    public function removebanner($id){
        $data=Brand::find($id);
           if($data){
                    $image_path = public_path() ."/upload/category/banner/".$data->image;
                        if(file_exists($image_path)) {
                            try {
                                 unlink($image_path);
                            }
                            catch(Exception $e) {
                              
                            }                        
                      }
                 $data->image="";
                 $data->save();
                 Session::flash('message',__('messages.Brand Banner Delete Successfully')); 
                 Session::flash('alert-class', 'alert-success');
        
           }else{
                  Session::flash('message',__('messages.Brand Not Found')); 
                  Session::flash('alert-class', 'alert-danger');
           }
           return redirect()->back();
    }
}
