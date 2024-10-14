<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Sentinel;
use Validator;
use App;
use Session;
use App\Models\User;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Seasonaloffer;
use App\Models\Banner;
use App\Models\Deal;
use App\Models\Sepicalcategories;
use App\Models\ContactUs;
use App\Models\Setting;
use App\Models\AttributeSet;
use App\Models\Options;
use App\Models\Optionvalues;
use App\Models\Attributes;
use App\Models\Attributevalues;
use App\Models\Review;
use App\Models\ProductAttributes;
use App\Models\ProductOption;
use App\Models\OrderData;
use App\Models\Taxes;
use App\Models\Order;
use App\Models\FeatureProduct;
use App\Models\Wishlist;
use App\Models\OrderResponse;
use App\Models\PaymentMethod;
use App\Models\ResetPassword;
use App\Models\QueryAns;
use App\Models\QueryTopic;
use App\Models\Token;
use App\Models\FileMeta;
use App\Models\Coupon;
use App\Models\Lang_core;
use DateTimeZone;
use DateTime;
use Image;
use Mail;
use DB;
class productfilterController extends Controller {
    
  public function productfilter(Request $request){
  //dd($request->all());exit;
    $response = array("status" => "0", "msg" => "Validation error");
            $rules = [
                      'category' => 'required',
                      'subcategory'=>'required',
                      'brand'=>'required',
                      'price'=>'required',
                      'discount'=>'required',
                      'ratting'=>'required',
                      'filter'=>'required',
                      'user_id'=>'required',
                      'color'=>'required',
                      'size'=>'required',
                      'lang'=>'required'        
                    ];                    
            $messages = array(
                    'category.required' => "category is required",
                    'subcategory.required' => "subcategory is required",
                    'brand.required' => "brand is required",
                    'price.required' => "price is required",
                    'discount.required' => "discount is required",
                    'ratting.required' => "ratting is required",
                    'filter.required' => "filter is required",
                    'user_id.required'=>"user_id is required",
                    'color.required'=>'color is required',
                    'size.required'=>'size is required',
                    'lang.required'=>'lang is required'
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $message = '';
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message .= $msg[0] . ", ";
                }
                $response['msg'] = $message;
            }else {
              App::setlocale($request->get("lang"));
                        session()->put('locale', $request->get("lang"));
              $input = $request->input();
                 
                $category=$request->get("category");
                $subcategory=$request->get("subcategory");
                $brand=$request->get("brand");
                $price=$request->get("price");
                $discount=$request->get("discount");
                $ratting=$request->get("ratting");
                $filter=$request->get("filter");
                $user_id=$request->get("user_id");
                if($request->get("lang")){
                    $lang = $request->get("lang");
                }else{
                    $lang = "en";
                }
                $color=$input['color'];
                $size=$request->get("size");
                $getbrand=$this->getbrandlist($category,$subcategory,$brand,$lang);
                $getsub=$this->getsublist($category,$subcategory,$brand,$lang);
                $getsize=$this->getsizls($category,$subcategory,$brand,'size');

                $getcolorls=$this->getcolorls($category,$subcategory,$brand,'color');
               
                if(isset($getsub)&&count($getsub)!=0){
                    $category=$getsub[0]->parent_category;
                }
                
                $klist=$this->kls($category,$subcategory,$brand,$filter,$ratting,$discount,$price,$color,$size);
 
                foreach ($klist as $k) {
                         $option=ProductOption::where("product_id",$k->id)->first();
                         $avgStar = Review::where("product_id",$k->id)->avg('ratting');
                         $k->ratting=round($avgStar);
                         $wish=Wishlist::where("product_id",$k->id)->where("user_id",$user_id)->get();
                         $k->wish=count($wish);
                         $re=Review::where("product_id",$k->id)->get();
                         $k->totalreview=count($re);
                         $k->basic_image=asset('public/upload/product/').'/'.$k->basic_image;
                         $k->price=$k->selling_price;
                         $getlang = FileMeta::where("model_id",$k->id)->where("lang",$request->get("lang"))->where("model_name","Product")->where("meta_key","name")->first();
                         $k->name = isset($getlang)?$getlang->meta_value:'';
                         unset($k->selling_price);
                          $k->options=ProductOption::where("product_id",$k->id)->groupBy('option_id')->get();
                            $data = array();
                            $i=0;
                            foreach ($k->options as $p1) {
                                $d1 = ProductOption::where("product_id",$k->id)->where("option_id",$p1->option_id)->where("lang",$request->get("lang"))->first();
                                if($d1){
                                    $data[$i]['optionname'] = $p1->name;
                                    $data[$i]['type'] = $p1->type;
                                    $data[$i]['required'] = $p1->is_required;
                                     $la = explode("#",$p1->label);
                                     $pr = explode("#",$p1->price);
                                     $j = 0;
                                     
                                     foreach ($pr as $p) {
                                         $a = array();
                                        $a['label'] = $la[$j];
                                        $a['price'] = $p;
                                        $data[$i]['optionvalues'][] =  $a;
                                        //$data[$i]['optionvalues'][]['price']=$p;
                                        $j++;
                                     }
                                    $i++;
                                }
                            }
                            $k->options = $data;
                }

                  $price=$this->getpricelist($category,$subcategory,$brand);
                  $data=array("subcategory"=>$getsub,"brand"=>$getbrand,"product"=>(object)$klist,"pricelist"=>$price,"color"=>$getcolorls,"size"=>$getsize);
                  if(count($klist)!=0){
                       
                      $response = array(
                        'status' =>1,
                        "details"=>$data
                      );
                  }
                  elseif(count($klist)!=0){
                     $response = array(
                        'status' =>1,
                        "details"=>$data
                      );
                  }
                  elseif(empty($getsub)&&empty($getbrand)&&empty($klist)&&empty($price)&&empty($getcolorls)&&empty($getsize)){
                     $response = array(
                        'status' =>0,
                        "details"=>$data
                      );
                  }
                  elseif($getsub!=""||$getbrand!=""||$klist!=""||$price!=""||$getcolorls!=""||$getsize!=""){
                     $response = array(
                        'status' =>1,
                        "details"=>$data
                      );
                  }
                  
                  else{
                    $response = array(
                        'status' =>0,
                        "details"=>$data
                      );
                  }
                 
            }
      
      return Response::json($response);
  }

    public function getbrandlist($category,$subcategory,$brand,$lang){
      if($subcategory=="0"&&$brand=="0"){
          $getsubcategory=Categories::where("parent_category",$category)->where("is_active",'1')->where("is_delete",'0')->get();
          $dt=array();
          if(count($getsubcategory)!=0){
              foreach ($getsubcategory as $ke) {
                 $brand=Brand::where("category_id",$ke->id)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
                 foreach ($brand as $b) {
                     $getlang = FileMeta::where("model_id",$b->id)->where("lang",$lang)->where("model_name","Brand")->where("meta_key","name")->first();
                       $b->brand_name = isset($getlang)?$getlang->meta_value:'';
                     $dt[]=$b;
                 }
              }
              return $dt;
          }
      }elseif($subcategory!="0"&&($brand=="0"||$brand!="0")){
           $brand=Brand::where("category_id",$subcategory)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
           foreach ($brand as $k) {
              $getlang = FileMeta::where("model_id",$k->id)->where("lang",$lang)->where("model_name","Brand")->where("meta_key","brand")->first();
               $k->brand_name = isset($getlang)?$getlang->meta_value:'';
            }
           return $brand;
      }elseif($subcategory=="0"&&$brand!="0"){
              $getb=Brand::where("brand_name",$brand)->first();
              $brand = array();
              if($getb){
                  $brand=Brand::where("category_id",$getb->category_id)->select('id','brand_name','category_id')->where("is_delete",'0')->get();
                  foreach ($brand as $k) {
                    $getlang = FileMeta::where("model_id",$k->id)->where("lang",$lang)->where("model_name","Brand")->where("meta_key","brand")->first();
                     $k->brand_name = isset($getlang)?$getlang->meta_value:'';
                  }
              }
              return $brand;
      }else{

      }
   }
   public function getsublist($category,$subcategory,$brand,$lang){
        if($category==0&&$subcategory==0&&$brand!=0){//001
            $brd=Brand::find($brand);
            $getsub=Categories::find($brd->category_id);
            if($getsub){
               $category=Categories::where("parent_category",$getsub->parent_category)->select("id","name","parent_category")->where("is_delete",'0')->where("is_active",'1')->get();
              
            }
        }elseif($category==0&&$subcategory!=0&&$brand==0){//010
            $getsub=Categories::find($subcategory);
            if($getsub){
               $category=Categories::where("parent_category",$getsub->parent_category)->select("id","parent_category")->where("is_delete",'0')->where("is_active",'1')->get();
               
            }
        }elseif($category==0&&$subcategory!=0&&$brand!=0){//011
            $getsub=Categories::find($subcategory);
            if($getsub){
               $category=Categories::where("parent_category",$getsub->parent_category)->select("id","parent_category")->where("is_delete",'0')->where("is_active",'1')->get();
               
            }
        }elseif($category!=0){//100
           $category=Categories::where("parent_category",$category)->select("id","parent_category")->where("is_delete",'0')->where("is_active",'1')->get();
          
        }
        
        foreach ($category as $ke) {  
            $getlang = FileMeta::where("model_id",$ke->id)->where("lang",$lang)->where("model_name","Categories")->where("meta_key","name")->first();
            $ke->name = isset($getlang)?$getlang->meta_value:''; 
          }
          return $category;
       
   }
 


  public function kls($category,$subcategory,$brand,$sort,$ratting,$discount,$price,$color,$size){

           if($sort==2){
                $field="price";
                $orderby="ASC";
           }
           elseif($sort==3){
                $field="price";
                $orderby="DESC";
           }
           elseif($sort==4){
                $field="id";
                $orderby="DESC";
           }else{
                $field="id";
                $orderby="ASC";
           }
          
           $product=array();
           $data=array();
              if($category!="0"&&$subcategory=="0"&&$brand=="0"){//100
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                         $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          
                      }elseif($color!="0"&&$size=="0"){//10

                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("product_color",$color)->paginate(10);
                      }
                      else if($color!="0"&&$size!="0"){//11
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                            $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                            $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->paginate(10);
                          }else{//11
                            $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                           }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->paginate(10);
                           }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                                }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->paginate(10);
                                }else{//11
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);

                             

                        }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                        }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                        }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                            }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                               }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                               }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                      }else{//11
                           $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                             if($color=="0"&&$size=="0"){//00
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->paginate(10);
                             }elseif($color=="0"&&$size!="0"){//01
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                             }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->paginate(10);
                              }elseif($color=="0"&&$size!="0"){//01
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                              }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                              }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                         }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                         }else{//11
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                            if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                            }elseif($color=="0"&&$size!="0"){//01
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                            }else{//11
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                          
                      }
                 }
              }
              if($category!="0"&&$subcategory=="0"&&$brand!="0"){//101
                 

                if($discount=="0"&&$ratting=="0"&&$price=="0"){ //000
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                         $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("product_color",$color)->paginate(10);
                      }
                      else if($color!="0"&&$size!="0"){//11
                           $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                            $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                            $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->paginate(10);
                          }else{//11
                            $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                           }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->paginate(10);
                           }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                                }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->paginate(10);
                                }else{//11
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                        }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                        }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                        }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                            }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                               }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                               }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                      }else{//11
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                             if($color=="0"&&$size=="0"){//00
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->paginate(10);
                             }elseif($color=="0"&&$size!="0"){//01
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                             }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->paginate(10);
                              }elseif($color=="0"&&$size!="0"){//01
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                              }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                              }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                         }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                         }else{//11
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                            if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                            }elseif($color=="0"&&$size!="0"){//01
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                            }else{//11
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                          
                      }
                 }
              }
               
              if($category!="0"&&$subcategory!="0"&&$brand=="0"){//110

                 if($discount=="0"&&$ratting=="0"&&$price=="0"){
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                         $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("product_color",$color)->paginate(10);
                      }
                      else if($color!="0"&&$size!="0"){//11
                           $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->paginate(10);
                          }else{//11
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                           }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->paginate(10);
                           }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                                }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->paginate(10);
                                }else{//11
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010

                        if($color=="0"&&$size=="0"){//00

                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                             
                        }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                        }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                        }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                            }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                               }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                               }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                           $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                      }else{//11
                           $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                              $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                             if($color=="0"&&$size=="0"){//00
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->paginate(10);
                             }elseif($color=="0"&&$size!="0"){//01
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                             }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("subcategory",$subcategory)->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->paginate(10);
                              }elseif($color=="0"&&$size!="0"){//01
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                              }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                              }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                         }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                         }else{//11
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                                 $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                            if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                            }elseif($color=="0"&&$size!="0"){//01
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                            }else{//11
                                $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                          
                      }
                
                 }
              }
              
              if($category!="0"&&$subcategory!="0"&&$brand!="0"){//111
                 if($discount=="0"&&$ratting=="0"&&$price=="0"){//000
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                         $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("product_color",$color)->paginate(10);
                      }
                      else if($color!="0"&&$size!="0"){//11
                           $product=Product::where("category",$category)->where("brand",$brand)->where("subcategory",$subcategory)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 else if($discount=="0"&&$ratting=="0"&&$price!="0"){//001
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("product_color",$color)->paginate(10);
                          }else{//11
                            $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                           }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("product_color",$color)->paginate(10);
                           }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                           }
                           
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                                }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("product_color",$color)->paginate(10);
                                }else{//11
                                    $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                                }
                           
                      }
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price=="0"){//010
                        if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                        }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                        }elseif($color!="0"&&$size=="0"){//10
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                        }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                        }
                    
                 }
                 elseif($discount=="0"&&$ratting!="0"&&$price!="0"){//011
                      $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                           if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                           }elseif($color=="0"&&$size!="0"){//01
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                            }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                          
                      }else{
                               if($color=="0"&&$size=="0"){//00
                                  $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                               }elseif($color=="0"&&$size!="0"){//01
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                               }elseif($color!="0"&&$size=="0"){//10
                                    $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                               }else{//11
                                  $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                               }
                      }
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price=="0"){//100
                      if($color=="0"&&$size=="0"){//00
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->paginate(10);
                      }elseif($color=="0"&&$size!="0"){//01
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                      }elseif($color!="0"&&$size=="0"){//10
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                      }else{//11
                           $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                      }
                    
                 }
                 elseif($discount!="0"&&$ratting=="0"&&$price!="0"){//101
                     $str=explode("-",$price);
                      if($str[0]=="0"){
                          if($color=="0"&&$size=="0"){//00
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                              $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                         
                      }elseif($str[1]=="00"){
                             if($color=="0"&&$size=="0"){//00
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->paginate(10);
                             }elseif($color=="0"&&$size!="0"){//01
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                             }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                             }
                           
                      }else{
                              if($color=="0"&&$size=="0"){//00
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("subcategory",$subcategory)->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->paginate(10);
                              }elseif($color=="0"&&$size!="0"){//01
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                              }elseif($color!="0"&&$size=="0"){//10
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                              }else{//11
                                   $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                              }
                      }
                 }
                 elseif($discount!="0"&&$ratting!="0"&&$price=="0"){//110
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("brand",$brand)->where("status",'1')->where("subcategory",$subcategory)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                         }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                         }else{//11
                             $product=Product::where("category",$category)->where("discount","<=",$discount)->where("subcategory",$subcategory)->where("brand",$brand)->where("status",'1')->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                         }
                 }
                 else{//111
                       $str=explode("-",$price);
                      if($str[0]=="0"){
                         if($color=="0"&&$size=="0"){//00
                             $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->paginate(10);
                         }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("product_color",$color)->paginate(10);
                          }else{//11
                                 $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price","<=",$str[1])->where("discount","<=",$discount)->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                        
                      }elseif($str[1]=="00"){
                            if($color=="0"&&$size=="0"){//00
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                            }elseif($color=="0"&&$size!="0"){//01
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                            }elseif($color!="0"&&$size=="0"){//10
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                            }else{//11
                                $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->where("selling_price",">=",$str[0])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                            }
                            
                      }else{
                          if($color=="0"&&$size=="0"){//00
                               $product=Product::where("category",$category)->where("status",'1')->where("brand",$brand)->orderby($field,$orderby)->where("subcategory",$subcategory)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->paginate(10);
                          }elseif($color=="0"&&$size!="0"){//01
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->paginate(10);
                          }elseif($color!="0"&&$size=="0"){//10
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->where("product_color",$color)->paginate(10);
                          }else{//11
                               $product=Product::where("category",$category)->where("status",'1')->where("subcategory",$subcategory)->where("brand",$brand)->orderby($field,$orderby)->select("id","MRP","price","basic_image","selling_price","discount","product_color")->where("is_deleted",'0')->whereBetween("selling_price",[$str[0],$str[1]])->whereHas('rattingdata', function($q)use($ratting){$q->groupBy('ratting')->havingRaw('round(AVG(ratting)) = '.$ratting);})->where("discount","<=",$discount)->whereHas('optionls', function($q)use($size){$q->where('name', 'like', '%' ."size". '%')->where('label', 'like', '%' .$size. '%');})->where("product_color",$color)->paginate(10);
                          }
                          
                      }
                 }
            }  
              
              
              return $product;
  }
   public function getpricelist($category,$subcategory,$brand){
    $product=array();
       if($category!=0&&$subcategory==0&&$brand==0){
         $product=Product::where("category",$category)->where("is_deleted",'0')->get();
      }else if($category!=0&&$subcategory==0&&$brand!=0){
          $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->get();
      }else if($category!=0&&$subcategory!=0&&$brand==0){
        $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->get();
      }else if($category!=0&&$subcategory!=0&&$brand!=0){
          $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("brand",$brand)->where("is_deleted",'0')->get();
      }
       foreach ($product as $k) {
                    $pricelist[]=$k->selling_price;
                  }
                     $pricels=array();
          if(!empty($pricelist)){
                sort($pricelist);
                $pricelist=array_values(array_unique($pricelist));
                $totaldived=floor(count($pricelist)/4);
              
                if($totaldived==1){
                    $a=strlen($pricelist[$totaldived*1])-2;
                    $pricels[]=ceil(($pricelist[($totaldived)-1*1]/pow(10,$a)))*pow(10,$a)."-00";
                } 
                if($totaldived==0){
                     $a=strlen($pricelist[$totaldived*1])-2;
                     $pricels[]=ceil(($pricelist[($totaldived)*1]/pow(10,$a)))*pow(10,$a)."-00";
                }
                if($totaldived!=1&&$totaldived!=0){
                    $a=strlen($pricelist[$totaldived*1])-2;
                    $pricels[]="0-".ceil(($pricelist[(($totaldived)-1)*1]/pow(10,$a)))*pow(10,$a);
                    $a=strlen($pricelist[$totaldived*2])-2;
                    $b=ceil(($pricelist[(($totaldived)-1)*1]/pow(10,$a)))*pow(10,$a);
                    $pricels[]=$b."-".ceil(($pricelist[(($totaldived)-1)*2]/pow(10,$a)))*pow(10,$a);
                    $a=strlen($pricelist[$totaldived*3])-2;
                    $b=ceil(($pricelist[(($totaldived)-1)*2]/pow(10,$a)))*pow(10,$a);
                    $pricels[]=$b."-".ceil(($pricelist[(($totaldived)-1)*3]/pow(10,$a)))*pow(10,$a);
                    $pricels[]=ceil(($pricelist[(($totaldived)-1)*3]/pow(10,$a)))*pow(10,$a)."-00";
                }
          }
      return $pricels;
   }
  
   public function getcolorls($category,$subcategory,$brand){
       // $color=$request->get("color");
    $product=array();
        $colorls=array();
          if($category!=0&&$subcategory==0&&$brand==0){
         $product=Product::where("category",$category)->where("is_deleted",'0')->select("id","category","product_color")->get();
      }else if($category!=0&&$subcategory==0&&$brand!=0){
          $product=Product::where("category",$category)->where("brand",$brand)->where("status",'1')->select("id","category","product_color")->get();
      }else if($category!=0&&$subcategory!=0&&$brand==0){
        $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->select("id","category","product_color")->get();
      }else if($category!=0&&$subcategory!=0&&$brand!=0){
          $product=Product::where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("brand",$brand)->where("is_deleted",'0')->select("id","category","product_color")->get();
      }
        foreach ($product as $k) {
             if(Session::get("locale")==""){
                Session::put("locale","en");
             }
              $arr=array();
               $getlang = FileMeta::where("model_id",$k->id)->where("lang",Session::get("locale"))->where("model_name","Product")->where("meta_key","colorname")->first();
               $arr["name"] = isset($getlang)?$getlang->meta_value:'';
              $arr["code"]=$k->product_color;
             // $arr["name"]=$k->color_name;
              $colorls[]=$arr;
         }
         $new = [];
         foreach ($colorls as $item) {
             if (empty($new[$item['code']])) {
                 if($item['code']!=null){
                    $new[$item['code']] = ['code' => $item['code'],"name"=>$item['name']]; 
                 }
             }
         }
         $new = array_values($new);
         return $new;
   }

   function getsizls($category,$subcategory,$brand,$fields){
     $colorls=array();
      $product=array();
          if($category!=0&&$subcategory==0&&$brand==0){
         $product=Product::with('optionls')->where("category",$category)->where("is_deleted",'0')->select("id","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory==0&&$brand!=0){
          $product=Product::with('optionls')->where("category",$category)->where("brand",$brand)->where("status",'1')->select("id","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory!=0&&$brand==0){
        $product=Product::with('optionls')->where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->select("id","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }else if($category!=0&&$subcategory!=0&&$brand!=0){
          $product=Product::with('optionls')->where("category",$category)->where("subcategory",$subcategory)->where("status",'1')->where("brand",$brand)->where("is_deleted",'0')->select("id","category")->whereHas('optionls', function($q)use($fields){$q->where('name', 'like', '%' .$fields. '%');})->get();
      }

    
         foreach ($product as $k) {
            $str=explode(",",strtoupper($k->optionls->name));
          foreach ($str as $kt=>$val) {
              if(strstr($val,strtoupper($fields))==true){
                      $name=$kt;
              }
          }
             $value=explode("#",$k->optionls->label);
             $colorarr=explode(",",$value[$name]);
             
             foreach ($colorarr as $co) {
              $colorls[]=$co;
             }
             
         }
       return array_values(array_unique($colorls));
   }
 
   
}

?>

