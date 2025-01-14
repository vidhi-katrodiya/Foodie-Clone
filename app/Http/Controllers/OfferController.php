<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Sentinel;
use Session;
use DataTables;
use App\Models\FileMeta;
use App\Models\Offer;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Seasonaloffer;
use App\Models\Deal;
use App\Models\Lang_core;
use App\Models\Coupon;
Use Image;
use Hash;

class OfferController extends Controller {
     public function __construct() {
         parent::callschedule();
    }
     public function showoffer(){
         $lang = Lang_core::all();
         $data=Deal::with('offer')->orderby('id')->get();
        $imagearray=array();
        foreach ($data as $k) {
            if($k->offer){
                 $imagearray[]=$k->offer->banner;
            }
           
        }
         return view("admin.offer.default")->with("data",$imagearray)->with("lang",$lang);
     }

     public function showaddoffer(){  
         $lang = Lang_core::all();       
         return view("admin.offer.addoffer")->with("lang",$lang);
     }

     public function getofferon($id){
        if($id==0){
            $data=Categories::where("parent_category",0)->where('is_deleted','0')->get();
            foreach ($data as $k) {
                $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                $k->name = isset($getlang)?$getlang->meta_value:$k->name;
             }
        }else{
            $data=Product::all();
        }
        return json_encode($data);
     }

     public function offerdatatable($id){
        
         $category =Offer::orderBy('id','DESC')->where("offer_type",$id)->get();
         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('title', function ($category) {
                   $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Offer")->where("meta_key","title")->first();
                return isset($getlang)?$getlang->meta_value:'';
              //   return $category->title;
            })
              ->editColumn('date', function ($category) {
                return $category->start_date."-".$category->end_date;
            })
            ->editColumn('banner', function ($category) {
                return asset("public/upload/offer/image/")."/".$category->banner;
            }) 
            ->editColumn('offer_on', function ($category) {
                if($category->is_product=='1'){
                     return __('messages.cate_gory');
                }
                elseif($category->is_product=='3'){
                     return __('messages.coupon');
                }
                else{
                     return __('messages.product');
                }
               
            }) 
             ->editColumn('price', function ($category) {
                if($category->is_product=='1'){
                     return __('messages.up_to').$category->fixed."%";
                }
                elseif($category->is_product=='3'){
                    $coupon=Coupon::find($category->coupon_id);
                    if($coupon){
                         if($coupon->discount_type=='1'){
                              $coupon->value=$coupon->value."%";
                         }
                         return $coupon->value;
                    }
                }
                else{
                     return $category->new_price;
                }
               
            }) 
             ->editColumn('offer', function ($category) {
                if($category->is_product=='1'){
                     $getlang = FileMeta::where("model_id",$category->category_id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                          return isset($getlang)?$getlang->meta_value:'';
                }
                elseif($category->is_product=='3'){
                    $data=Coupon::find($category->coupon_id);
                    return $data->name."(".$data->code.")";
                }
                else{
                     $data=Product::find($category->product_id);
                    return $data->name;
                }
               
            })           
            ->editColumn('action', function ($category) {                 
                 $editoption=url('admin/editoffer',array('id'=>$category->id));
                 $deloption=url('admin/deleteoffer',array('id'=>$category->id));
                 
                 $return = '<a href="'.$editoption.'" rel="tooltip" title="active" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-edit f-s-25" style="font-size: x-large;"></i></a><a onclick="delete_record(' . "'" . $deloption. "'" . ')" rel="tooltip" title="Delete Category" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';

                 return $return;              
            })           
            ->make(true);
     }

     public function shownormaloffer(){
          $data=Deal::with('offer')->orderby('id')->get();
          $lang = Lang_core::all();
        $imagearray=array();
        foreach ($data as $k) {
            if($k->offer){
                 $imagearray[]=$k->offer->banner;
            }
        }
        return view("admin.offer.normaloffer")->with("data",$imagearray)->with("lang",$lang);
     }

     public function showsensonaloffer(){
        $lang = Lang_core::all();
         return view("admin.offer.sensonal")->with("lang",$lang);
     }

     public function sensonaldatatable(){
        $category =Seasonaloffer::orderBy('id','DESC')->get();

         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('banner', function ($category) {
                 return asset("public/upload/offer/image/")."/".$category->banner;
            })
            ->editColumn('title', function ($category) {
                  $getlang = FileMeta::where("model_id",$category->id)->where("lang",Session::get('locale'))->where("model_name","Seasonaloffer")->where("meta_key","title")->first();
                          return isset($getlang)?$getlang->meta_value:'';
            })
            ->editColumn('category', function ($category) {
                  if($category->category=='0'){
                     return __('messages.all');
                  }
                  else{
                     
                         $getlang = FileMeta::where("model_id",$category->category)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                          return isset($getlang)?$getlang->meta_value:$k->name;
                         
                      
                  }          
            })           
            ->editColumn('action', function ($category) { 
                 if($category->is_active=='1'){
                    $color="green";
                 }
                 else{
                    $color="red";
                 }
                 $deloption=url('admin/deletesensonaloffer',array('id'=>$category->id));
                 $changeoption=url('admin/changespeofferstatus',array('id'=>$category->id));                 
                 return '<a  onclick="change_record(' . "'" . $changeoption. "'" . ')" rel="tooltip" title="active" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-ban f-s-25" style="font-size: x-large;color:'.$color.'"></i></a><a onclick="delete_record(' . "'" . $deloption. "'" . ')" rel="tooltip" title="Delete Category" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-trash f-s-25" style="font-size: x-large;"></i></a>';              
            })           
            ->make(true);
     }

     public function addsensonal(){
         $category=Categories::where("parent_category",0)->where('is_delete','0')->get();
         foreach ($category as $k) {
                $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                $k->name = isset($getlang)?$getlang->meta_value:$k->name;
             }
             $lang = Lang_core::all();
         return view("admin.offer.addsensonal")->with("category",$category)->with("lang",$lang);
     }

     public function storesensonal(Request $request){
            if ($files = $request->file('banner')) {
                        $file = $request->file('banner');
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension() ?: 'png';
                        $folderName = '/upload/offer/image/';
                        $picture = "offer_".time() . '.' . $extension;
                        $destinationPath = public_path() . $folderName;
                        $request->file('banner')->move($destinationPath, $picture);
                        $img_url =$picture;
            }
            else{
                    $img_url="";
            }
            $store=new Seasonaloffer();
            $store->fixed_form=$request->get("fixed_form");
            $store->fixed_to=$request->get("fixed_to");
            $store->category=$request->get("category");
            $store->banner=$img_url;
            $store->save();
            $language =Lang_core::all();
            foreach ($language as $k) {
                $this->file_meta_update_payment_key($store->id,$k->code,"title",$request->get("title_".$k->code),"Seasonaloffer");
               
            }
            Session::flash('message',__('messages_error_success.sensonal_offer_add_success')); 
            Session::flash('alert-class', 'alert-success');
            return redirect("admin/sensonal_offer");
     }

     public function changeoffer($id){
        $data=Seasonaloffer::all();
        foreach ($data as $k) {
            $k->is_active='0';
            $k->save();
        }
        $change=Seasonaloffer::find($id);
        $change->is_active='1';
        $change->save();
        Session::flash('message',__('messages_error_success.sensonal_offer_change_success')); 
        Session::flash('alert-class', 'alert-success');
        return redirect("admin/sensonal_offer");
     }

     public function addoffersection($id){
        $date=date("Y-m-d");
        $lang = Lang_core::all();
        if($id==1){
            $category=Categories::where("parent_category",'0')->where("is_delete",'0')->get();
        }
        else{
            $category=Categories::where("parent_category","!=",'0')->where("is_delete",'0')->get();
        }
        
        foreach ($category as $k) {
                $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                $k->name = isset($getlang)?$getlang->meta_value:$k->name;
             }
        $product=Product::where('status','1')->where('is_deleted','0')->get();
        $coupon=Coupon::all();
        $couponls=array();
        foreach ($coupon as $k) {
             $start_date=date("Y-m-d",strtotime($k->start_date)); 
             $end_date=date("Y-m-d",strtotime($k->end_date));
              if(($date>=$start_date)&&($date<=$end_date)){
                $couponls[]=$k;
              }
        }
         foreach($product as $d){
                $getmeta = Filemeta::where("model_id",$d->id)->where("model_name","Product")->where("meta_key","name")->where("lang",Session::get('locale'))->first();
                $d->name = isset($getmeta->meta_value)?$getmeta->meta_value:'';
            }

        return view("admin.offer.offeradd")->with("category",$category)->with("product",$product)->with("offer_type",$id)->with("coupon",$couponls)->with("lang",$lang);
     }

     public function storeofferdata(Request $request){
         $mobile_image="";
          if ($files = $request->file('banner')) {
            if($request->get("offer_type")=='1'){
                 $file = $request->file('banner');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/offer/image/';
                 $picture = "offer_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('banner')->move($destinationPath, $picture);
                 $img_url =$picture;

                 $file = $request->file('mobile_banner');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/offer/image/';
                 $picture = "offer_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('mobile_banner')->move($destinationPath, $picture);
                 $mobile_image =$picture;

            }
            else{
                  $file = $request->file('banner');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/offer/image/';
                 $picture = "offer_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('banner')->move($destinationPath, $picture);
                 $img_url =$picture;
            }
          }
          else{
                    $img_url="";
          }
          $store=new Offer();
          $store->offer_type=$request->get("offer_type");
          $store->is_product=$request->get("offer_on");
          $store->banner=$img_url;
          $store->start_date=$request->get("start_date");
          $store->end_date=$request->get("end_date");
          $store->category_id=$request->get("category_id");
          $store->coupon_id=$request->get("coupon_id");
          $store->fixed=$request->get("fixed");
          $store->product_id=$request->get("product_id");
          $store->new_price=$request->get("offer_price");
          $store->coupon_id=$request->get("coupon_id");
          $store->mobile_banner=$mobile_image;
          $store->save();

           $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"title",$request->get("title_".$k->code),"Offer");
            $this->file_meta_update_payment_key($store->id,$k->code,"main_title",$request->get("main_title_".$k->code),"Offer");
        }
            Session::flash('message',__('messages_error_success.Offer_add_success')); 
            Session::flash('alert-class', 'alert-success');
            if($request->get("offer_type")==1){
                return redirect("admin/offer");
            }
            else{
                return redirect("admin/normaloffer");
            }
            
     }

     public function getcoupondata($id){
        $getcoupon=Coupon::find($id);
        if($getcoupon->discount_type=='1'){
            $getcoupon->value=$getcoupon->value."%";
        }
       
        return json_encode($getcoupon);
     }
     public function deals(){
        $data=Deal::with('offer')->orderby('id')->get();
        $imagearray=array();
        foreach ($data as $k) {
          if($k->offer){
                 $imagearray[]=$k->offer->banner;
            }
        }
        $lang = Lang_core::all();

        return view("admin.offer.deals")->with("data",$imagearray)->with("lang",$lang);
     }

     public function editdeal($id){
        $data=Deal::find($id);
        if($data->offer_type=='1'){
            $offerdata=Offer::all();
        }
        else{
           $offerdata=Offer::where("is_product",'2')->get();
        }
        return view("admin.offer.editdeal")->with("data",$data)->with("offerdata",$offerdata);
        
     }

     public function dealdatatable(){
        $category =Deal::orderBy('id','DESC')->get();

         return DataTables::of($category)
            ->editColumn('id', function ($category) {
                return $category->id;
            })
            ->editColumn('banner', function ($category) {
                if(isset($category->offer)){
                 return asset("public/upload/offer/image/")."/".$category->offer->banner;
                }
            })
            ->editColumn('title', function ($category) {
                if(isset($category->offer)){
                    $getlang = FileMeta::where("model_id",$category->offer->id)->where("lang",Session::get('locale'))->where("model_name","Offer")->where("meta_key","title")->first();
                    $title = isset($getlang)?$getlang->meta_value:$k->name;
                    return $title.",".$category->offer->id;
                }
                else{
                     ",";
                }
            })
            ->editColumn('date', function ($category) {
                if(isset($category->offer)){
                 return $category->offer->start_date."-".$category->offer->end_date; 
                 }         
            })
            ->editColumn('deal', function ($category) {
                  if($category->offer_type=='1'){
                        return __('messages.big_deal');
                  }
                  else{
                        return __('messages.normal_deal');
                  }
                               
            })            
            ->editColumn('action', function ($category) { 
                return '<a onclick="editdeal('.$category->id.','.$category->offer_id.')" rel="tooltip" title="active" class="m-b-10 m-l-5" data-original-title="Remove" style="margin-right: 10px;"><i class="fa fa-edit f-s-25" style="font-size: x-large;"></i></a>';             
            })           
            ->make(true);
     }

     public function getofferfordeal($deal_id){
            $data=Deal::find($deal_id);
            if($data->offer_type=='1'){
                $ls=Offer::where("offer_type",'1')->get();
                foreach ($ls as $l) {
                    $getlang = FileMeta::where("model_id",$l->id)->where("lang",Session::get('locale'))->where("model_name","Offer")->where("meta_key","title")->first();
                $l->title = isset($getlang)?$getlang->meta_value:$k->name;
                }
            }
            else{
                $ls=Offer::where("offer_type",'2')->get();
                foreach ($ls as $l) {
                    $getlang = FileMeta::where("model_id",$l->id)->where("lang",Session::get('locale'))->where("model_name","Offer")->where("meta_key","title")->first();
                $l->title = isset($getlang)?$getlang->meta_value:$k->name;
                }
            }
            return json_encode($ls);
     }

     public function updatedeal($deal_id,$offer_id){
        $dat=Deal::find($deal_id);
        $dat->offer_id=$offer_id;
        $dat->save();
        return "done";
     }

     public function editoffer($id){
       $product=Product::where('status','1')->where('is_deleted','0')->get();
        $data=Offer::find($id);
        $lang = Lang_core::all();
        if($data->offer_type=='1'){
            $category=Categories::where("parent_category",0)->get();
        }else{
            $category=Categories::where("parent_category","!=",0)->get();
        }

        foreach ($category as $k) {
                $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get('locale'))->where("model_name","Categories")->where("meta_key","name")->first();
                $k->name = isset($getlang)?$getlang->meta_value:$k->name;
             }
        if($data->is_product=='2'){
            $pro=Product::find($data->product_id);
            $mrp=$pro->MRP;
            $price=$pro->price;
        }
        else{
            $mrp="";
            $price="";
        }

        foreach ($lang as $k) {         
                $getmeta = Filemeta::where("model_id",$id)->where("model_name","Offer")->where("meta_key","title")->where("lang",$k->code)->first();
                $title = "title_".$k->code;
                $main_title = "main_title_".$k->code;
                if($getmeta){
                   $data->$title= $getmeta->meta_value;
                }else{
                    $data->$title = "";
                }
                $getmeta = Filemeta::where("model_id",$id)->where("model_name","Offer")->where("meta_key","main_title")->where("lang",$k->code)->first();
                if($getmeta){
                   $data->$main_title= $getmeta->meta_value;
                }else{
                    $data->$main_title = "";
                }
          }
         $coupon=Coupon::all();
         $coupondata=array();
         if($data->coupon_id!=""){
                 $coupondata=Coupon::find($data->coupon_id);
         }
         
         
        return view("admin.offer.editoffer")->with("category",$category)->with("product",$product)->with("data",$data)->with("mrp",$mrp)->with("price",$price)->with("coupon",$coupon)->with("coupondata",$coupondata)->with("lang",$lang);
     }

     public function updateofferdata(Request $request){
         $mobile_image="";
         if ($files = $request->file('banner')) {
           
            if($request->get("offer_type")=='1'){                
                 $file = $request->file('banner');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/offer/image/';
                 $picture = "offer_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('banner')->move($destinationPath, $picture);
                 $img_url =$picture;
            }
            else{
                  $file = $request->file('banner');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/offer/image/';
                 $picture = "offer_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('banner')->move($destinationPath, $picture);
                 $img_url =$picture;
            }
          }
          else{
                    $img_url=$request->get("real_image");
          }

            if ($files = $request->file('mobile_banner')) {
                 $file = $request->file('mobile_banner');
                 $filename = $file->getClientOriginalName();
                 $extension = $file->getClientOriginalExtension() ?: 'png';
                 $folderName = '/upload/offer/image/';
                 $picture = "offer_".time() . '.' . $extension;
                 $destinationPath = public_path() . $folderName;
                 $request->file('mobile_banner')->move($destinationPath, $picture);
                 $mobile_image =$picture;
            }else{
                  $mobile_image=$request->get("real_mobile_image");
            }
         
          $store=Offer::find($request->get("id"));
          $mimg=$store->mobile_banner;
          $bimg=$store->banner;
          $store->banner=$img_url;
          $store->start_date=$request->get("start_date");
          $store->end_date=$request->get("end_date");
          $store->category_id=$request->get("category_id");
          $store->fixed=$request->get("fixed");
          $store->product_id=$request->get("product_id");
          $store->new_price=$request->get("offer_price");
          $store->coupon_id=$request->get("coupon_id");
          $store->mobile_banner=$mobile_image;
          $store->save();
          if($bimg!=$img_url){
            $image_path="";
            if($bimg!=""){
                $image_path = public_path() ."/upload/offer/image/".$bimg;
            }
            if(file_exists($image_path)) {
                  try{
                           unlink($image_path);
                        }catch(\Exception $e){
                                }
            }
        }
         if($mimg!=$mobile_image){
            $image_path="";
            if($mimg!=""){
                $image_path = public_path() ."/upload/offer/image/".$mimg;
            }
            if(file_exists($image_path)) {
                  try{
                           unlink($image_path);
                        }catch(\Exception $e){
                                }
            }
        }
         $language =Lang_core::all();
        foreach ($language as $k) {
            $this->file_meta_update_payment_key($store->id,$k->code,"title",$request->get("title_".$k->code),"Offer");
            $this->file_meta_update_payment_key($store->id,$k->code,"main_title",$request->get("main_title_".$k->code),"Offer");
        }
            Session::flash('message',__('messages_error_success.offer_update_success')); 
            Session::flash('alert-class', 'alert-success');
             if($store->offer_type==1){
                return redirect("admin/offer");
            }
            else{
                return redirect("admin/normaloffer");
            }
           
     }


     public function deleteoffer($id){
        $del=Offer::find($id);
        $img1=$del->banner;
        $img2=$del->mobile_banner;
        $del->delete();
            $image_path1="";
            $image_path2="";
            $image_path1 = public_path() ."/upload/offer/image/".$img1;
            $image_path2 = public_path() ."/upload/offer/image/".$img2;
            if(file_exists($image_path1)) {
                 try{
                           unlink($image_path);
                        }catch(\Exception $e){
                                }
            }
            if(file_exists($image_path2)) {
                  try{
                           unlink($image_path);
                        }catch(\Exception $e){
                                }
            }
        Session::flash('message',__('messages_error_success.offer_delete')); 
        Session::flash('alert-class', 'alert-success');
        return redirect()->back();
     }

     public function deletesensonaloffer($id){
        $del=Seasonaloffer::find($id);
        $idm=$del->banner;
        $del->delete();
        $image_path1 = public_path() ."/upload/offer/image/".$idm;
        if(file_exists($image_path1)) {
                unlink($image_path1);
            }
        Session::flash('message',__('messages_error_success.offer_delete')); 
        Session::flash('alert-class', 'alert-success');
        return redirect("admin/sensonal_offer");
     }

}