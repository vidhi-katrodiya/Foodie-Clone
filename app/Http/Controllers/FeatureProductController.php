<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Models\Product;
use App\Models\FeatureProduct;
use App\Models\Lang_core;
use App\Models\FileMeta;
Use Image;
use Hash;
class FeatureProductController extends Controller {
     public function __construct() {
         parent::callschedule();
    }
    public function index(){
         $lang = Lang_core::all();
         $data=Product::where('status','1')->where('is_deleted','0')->get();
         foreach ($data as $k) {
            $getlang =  FileMeta::where("model_id",$k->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get("locale"))->first();
             $k->name = isset($getlang->meta_value)?$getlang->meta_value:'';
         }
         return view("admin.feature.default")->with("product",$data)->with("lang",$lang);
    }

    public function featureproductdatatable(){
         $category =FeatureProduct::with('productdata')->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('image', function ($category) {
                  return asset('public/upload/product')."/".$category->productdata->basic_image;
            })
              ->editColumn('product', function ($category) {
                $getlang =  FileMeta::where("model_id",$category->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get("locale"))->first();
                return isset($getlang->meta_value)?$getlang->meta_value:'';
            })                        
            ->editColumn('action', function ($category) {  
            $delete=url('admin/deletefeature',array('id'=>$category->id));    
                 $return = '<a onclick="delete_record(' . "'" . $delete. "'" . ')" rel="tooltip" title="Delete Category" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';

                 return $return;              
            })           
            ->make(true);
    }

    public function deletefeature($id){
        $store=FeatureProduct::find($id);
        $store->delete();
        Session::flash('message',__('messages_error_success.feature_product_del')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

    public function addfeatureproduct(Request $request){
        $store=new FeatureProduct();
        $store->product_id=$request->get("product_id");
        $store->save();
        Session::flash('message',__('messages_error_success.feature_product_add')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
    }

}
