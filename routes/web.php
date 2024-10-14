<?php
error_reporting(-1);
ini_set('display_errors', 'On');
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FrontController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaytmPayment;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayPaypalController;
use App\Http\Controllers\Admincontroller;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\sellerCouponController;
use App\Http\Controllers\Categorycontroller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaxesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FeatureProductController;
use App\Http\Controllers\QuestionSupportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ComplainController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('cache-clear', function () {
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');
    echo "done";
});


/*front routes*/
Route::any("map",[FrontController::class,"map"])->name("map");
Route::any("login_user",[FrontController::class,"login_user"])->name("login_user");
Route::any("userlogout",[FrontController::class,"userlogout"])->name("userlogout");
Route::any("PostLogin",[FrontController::class,"PostLogin"])->name("PostLogin");
Route::any("register",[FrontController::class,"show_register_user"])->name("register");
Route::any("my_account",[FrontController::class,"my_account"])->name("my_account");
Route::any("edit_profile",[FrontController::class,"edit_profile"])->name("edit_profile");

Route::any("/",[FrontController::class,"showFront"])->name("showFront");
Route::any("listing_data/{type}",[FrontController::class,"listing"])->name("listing_data");
Route::any("filter_data",[FrontController::class,"filter_data"])->name("filter_data");
Route::any("Wishlist",[FrontController::class,"Wishlist"])->name("Wishlist");
Route::any("add_favourite",[FrontController::class,"add_favourite"])->name("add_favourite");
Route::any("remove_fav_res",[FrontController::class,"remove_fav_res"])->name("remove_fav_res");

Route::any("favourit_data",[FrontController::class,"favourit_data"])->name("favourit_data");
Route::any("order_data",[FrontController::class,"order_data"])->name("order_data");
Route::any("address_data",[FrontController::class,"address_data"])->name("address_data");
Route::any("offer",[FrontController::class,"show_offer"])->name("offer");
Route::any("offer_data",[FrontController::class,"offer_data"])->name("offer_data");


Route::any("res_detail/{id}",[FrontController::class,"resdetail"])->name("res_detail");
Route::any("add_cart/{id}",[FrontController::class,"add_cart"])->name("add_cart");
Route::any("add/{id}",[FrontController::class,"add"])->name("add");
Route::any("product_option/{id}",[FrontController::class,"product_option"])->name("product_option");
Route::any("add_out_opt/{id}",[FrontController::class,"add_out_opt"])->name("add_out_opt");
Route::any("offer",[FrontController::class,"show_offer"])->name("offer");
Route::any("remove_cart/{id}",[FrontController::class,"remove_cart"])->name("remove_cart");
Route::any("add_review",[FrontController::class,"add_review"])->name("add_review");
Route::any("book_table",[FrontController::class,"book_table"])->name("book_table");

Route::any("checkout",[FrontController::class,"checkout"])->name("checkout");
Route::any("add_address/{id}",[FrontController::class,"add_address"])->name("add_address");
Route::any("add_address_data",[FrontController::class,"add_address_data"])->name("add_address_data");
Route::any("add_current_add/{id}",[FrontController::class,"add_current_add"])->name("add_current_add");
Route::any("add_current_add/{id}",[FrontController::class,"add_current_add"])->name("add_current_add");
Route::any("add_discount",[FrontController::class,"add_discount"])->name("add_discount");
Route::any("add_order",[FrontController::class,"add_order"])->name("add_order");
Route::any("check_option_data",[FrontController::class,"check_option_data"])->name("check_option_data");
Route::any("repeat_opt/{id}",[FrontController::class,"repeat_opt"])->name("repeat_opt");
Route::any("rpt_opt/{id}",[FrontController::class,"rpt_opt"])->name("rpt_opt");
Route::any("rpt_out_opt/{id}",[FrontController::class,"rpt_out_opt"])->name("rpt_out_opt");
Route::any("repeat_out_opt/{id}",[FrontController::class,"repeat_out_opt"])->name("repeat_out_opt");
Route::any("repeat/{id}",[FrontController::class,"repeat"])->name("repeat");
Route::any("delete_address",[FrontController::class,"delete_address"])->name("delete_address");
Route::any("address_list",[FrontController::class,"address_list"])->name("address_list");
Route::any("order_list",[FrontController::class,"order_list"])->name("order_list");
Route::any("edit_address/{id}",[FrontController::class,"edit_address"])->name("edit_address");
Route::any("edited_address",[FrontController::class,"edited_address"])->name("edited_address");

Route::any("orders",[FrontController::class,"orders"])->name("orders");
Route::any('cod_payment',[FrontController::class,"cod_payment"])->name("cod_payment");
Route::any('order_detail/{id}',[FrontController::class,"order_detail"])->name("order_detail");
Route::any('invoice/{id}',[FrontController::class,"invoice"])->name("invoice");

Route::any("search_res",[FrontController::class,"search_res"])->name("search_res");
Route::any("highest_rated",[FrontController::class,"highest_rated"])->name("highest_rated");
Route::any("short_offer",[FrontController::class,"short_offer"])->name("short_offer");
Route::any("all_time_fav_res",[FrontController::class,"all_time_fav_res"])->name("all_time_fav_res");
Route::any("express_delivery_res/{lat}/{lan}",[FrontController::class,"express_delivery_res"])->name("express_delivery_res");


Route::get("Privacy-Policy",[FrontController::class,"privacy_front_app"]);
Route::get("accountdeletion",[FrontController::class,"accountdeletion"])->name("accountdeletion");


/*end front routes*/

Route::any('save-braintree',[PaymentController::class,"save_braintree"])->name("save-braintree");
Route::any('braintree-payment',[PaymentController::class,"show_braintree_payment"])->name("braintree-payment");

Route::any('paytm-payment',[PaytmPayment::class,"order"])->name("paytm-payment");
Route::any('paytmstatus',[PaytmPayment::class,"paymentCallback"])->name("paytmstatus");

Route::any('rave_payment',[PaymentController::class,"show_rave_payment"])->name("rave_payment");
Route::any('rave-callback',[PaymentController::class,"rave_callback"])->name("rave-callback");

Route::any('paystack-payment',[PaymentController::class,"show_paystack_payment"])->name("paystack-payment");
Route::any('paystackcallback',[PaymentController::class,"paystackcallback"])->name("paystackcallback");

Route::any('payment-success',[PaymentController::class,"payment_success"])->name("payment-success");
Route::any('payment-success-web',[PaymentController::class,"payment_success_web"])->name("payment-success-web");

Route::any('payment-failed',[PaymentController::class,"payment_failed"])->name("payment-failed");

Route::any('razor_payment',[PaymentController::class,"razor_payment"])->name("razor-payment");
Route::any('pay_razorpay',[PaymentController::class,"show_pay_razorpay"])->name("razorpay-payment");

Route::any('pay_stripe',[OrderController::class,"pay_stripe"])->name("pay_stripe");
Route::any('payment_success',[OrderController::class,"payment_success"])->name("payment_success");
Route::any('payment_fail',[OrderController::class,"payment_fail"])->name("payment_fail");
Route::any('payWithpaypal',[PayPaypalController::class,"payWithpaypal"])->name("payWithpaypal");
Route::any('getStatus',[PayPaypalController::class,"getStatus"])->name("getStatus");

Route::get("resetpassword/{code}",[FrontController::class,"resetpassword"]);
Route::any("resetnewpwd",[FrontController::class,"resetnewpwd"]);
Route::get("privacy_policy",[Admincontroller::class,"privacy"]);
Route::get("languagechange/{lang}",[Admincontroller::class,"languagechange"]); 
Route::get('localization/{locale}',[Admincontroller::class,"showlocationchange"]);


 Route::get("login",[Admincontroller::class,"showlogin"])->name("showlogin");

 Route::get("sellerlogin",[Admincontroller::class,"showsellerlogin"])->name("sellerlogin");

 Route::post("postsellerlogin",[Admincontroller::class,"postsellerlogin"])->name("postsellerlogin");

Route::group(['prefix' => 'seller'], function () {
   Route::group(['middleware' => ['sellercheckexiste']], function () {
        Route::get("dashboard",[Admincontroller::class,"showsellerdashboard"])->name("dashboard");      
        Route::get("logout",[Admincontroller::class,"sellershowlogout"])->name("logout");

        Route::get("product/{id}",[SellerProductController::class,"showproduct"])->name('product');
        Route::get("productdatatable/{id}",[SellerProductController::class,"productdatatable"])->name("productdatatable");

        Route::get("savecatalog/{id}/{tab}/{cat_id}",[SellerProductController::class,"showaddcatalog"])->name("addcatalog");

        Route::get("getsubcategory/{id}",[SellerProductController::class,"getsubcategory"])->name("getsubcategory");
        Route::get("getbrandbyid/{id}",[SellerProductController::class,"getbrandbyid"])->name("getbrandbyid");
        Route::post("saveproduct",[SellerProductController::class,"saveproduct"])->name("saveproduct");
        Route::get("changeproductstatus/{id}",[SellerProductController::class,"changeproductstatus"]);
        Route::post("saveprice",[SellerProductController::class,"saveprice"])->name("saveprice");
        Route::post("saveinventory",[SellerProductController::class,"saveinventory"])->name("saveinventory");
        Route::post("saveproductimage",[SellerProductController::class,"saveproductimage"])->name("#saveproductimage");
         Route::post("saveseoinfo",[SellerProductController::class,"saveseoinfo"])->name("saveseoinfo");
         Route::get("getoptionvalues/{id}",[SellerProductController::class,"getoptionvalues"])->name("getoptionvalues");
         Route::post("productuploadimg",[SellerProductController::class,"productuploadimg"])->name("productuploadimg");
         Route::get("getallproduct",[SellerProductController::class,"getallproduct"])->name("getallproduct");
         Route::post("updateproduct",[SellerProductController::class,"updateproduct"])->name("updateproduct");
         Route::post("saverealtedprice",[SellerProductController::class,"saverealtedprice"])->name("saverealtedprice");
          Route::get("productlist/{id}/{pro_id}",[SellerProductController::class,"productlist"])->name("productlist");

           Route::get("checktotalproduct",[SellerProductController::class,"checktotalproduct"]);

            route::post("saveproductattibute",[SellerProductController::class,"saveproductattibute"])->name("saveproductattibute");
       Route::post("saveproductoption",[SellerProductController::class,"saveproductoption"])->name("saveproductoption");
       Route::post("saverelatedproduct",[SellerProductController::class,"saverelatedproduct"])->name("saverelatedproduct");


        Route::get("sales",[SellerProductController::class,"showsales"]);
        route::get("orderdatatable",[SellerProductController::class,"orderdatatable"]);

        Route::get("vieworder/{id}",[OrderController::class,"sellervieworder"])->name("vieworder");
         Route::get("generateorderpdf/{id}",[OrderController::class,"generateorderpdf"])->name("generateorderpdf");
         Route::get("changeorderstatus/{order_id}/{status_id}",[OrderController::class,"changeorderstatus"])->name("changeorderstatus");

          Route::get("paymenthistory",[PaymentController::class,"paymenthistory"]);
        Route::get("sellerpaymenthistorydatatable",[PaymentController::class,"sellerpaymenthistory"]);
        
        
         Route::get("coupon",[sellerCouponController::class,"index"]);
        Route::get("coupondatatable",[sellerCouponController::class,"coupondatatable"])->name("coupondatatable");
        Route::get("addcoupon",[sellerCouponController::class,"addcoupon"])->name("addcoupon");
        Route::any("savecoupon",[sellerCouponController::class,"savecoupon"])->name("savecoupon");
        Route::get("deletecoupon/{id}",[sellerCouponController::class,"deletecoupon"]);
        Route::post("savecouponsecondstep",[sellerCouponController::class,"savecouponsecondstep"])->name("savecouponsecondstep");
        Route::post("savecouponstepthree",[sellerCouponController::class,"savecouponstepthree"])->name("savecouponstepthree");
        Route::get("editcoupon/{id}",[sellerCouponController::class,"editcoupon"])->name("editcoupon");
         Route::get("getallsubcategory",[Categorycontroller::class,"getallsubcategory"]);

         Route::get("attibutes/{id}",[SellerProductController::class,"showattribute"]);
         Route::get("addattributerow/{id}",[SellerProductController::class,"addattributerow"]);
         Route::get("addattributeinnerrow/{newrow}/{lang}/{val}",[SellerProductController::class,"addattributeinnerrow"]);
    
        Route::post("offer_status",[sellerCouponController::class,"offer_status"])->name("offer_status");
    
        Route::get("options/{id}",[SellerProductController::class,"showoptions"]);
        Route::get("add_customisation",[SellerProductController::class,"add_customisation"]);
        Route::get("addoptionrow/{id}",[SellerProductController::class,"addoptionrow"]);
        Route::get("notification/{id}",[SellerProductController::class,"notification"]);
        
       
       /*My old route*/
       Route::post("check_main_offer",[sellerCouponController::class,"check_main_offer"])->name("check_main_offer");
        Route::get("res_category",[Categorycontroller::class,"res_category"])->name("res_category");  
        Route::get("res_categorydatatable",[Categorycontroller::class,"res_categorydatatable"])->name("res_categorydatatable");
        Route::get("update_res_category/{id}",[Categorycontroller::class,"update_res_category"])->name("update_res_category");
        Route::post("post_update_category",[Categorycontroller::class,"post_update_category"])->name("post_update_category");  

        Route::get("delete_res_category/{id}",[Categorycontroller::class,"delete_res_category"])->name("delete_res_category");
        Route::get("sub_category/{id}",[Categorycontroller::class,"sub_category"])->name("sub_category");
         Route::get("res_subcategorydatatable/{id}",[Categorycontroller::class,"res_subcategorydatatable"])->name("res_subcategorydatatable");
          Route::get("update_res_subcategory/{cat_id}/{id}",[Categorycontroller::class,"update_res_subcategory"])->name("update_res_subcategory");
          Route::post("post_update_subcategory",[Categorycontroller::class,"post_update_subcategory"])->name("post_update_subcategory");
          Route::get("delete_res_subcategory/{id}",[Categorycontroller::class,"delete_res_subcategory"])->name("delete_res_subcategory");
        /*My new route*/
        Route::get("restaurant_profile",[UserController::class,"show_restaurant_profile"])->name("restaurant_profile");
        Route::any("updatesellerprofile",[UserController::class,"edit_restaurant_profile"])->name("updatesellerprofile");
        Route::get("delete_res_product/{id}",[SellerProductController::class,"delete_res_product"])->name("delete_res_product");
           
     });
 });



Route::group(['prefix' => 'admin'], function () {

    Route::post("postlogin",[Admincontroller::class,"postlogin"])->name("postlogin");

    Route::group(['middleware' => ['admincheckexiste']], function () {

      Route::get("dashboard",[Admincontroller::class,"showdashboard"])->name("dashboard");
      //logout
      Route::get("logout",[Admincontroller::class,"showlogout"])->name("logout");

      //start profile 
      Route::get("editprofile",[Admincontroller::class,"editprofile"])->name("editprofile")->middleware('admincheckexiste');
      Route::post("updateprofile",[Admincontroller::class,"updateprofile"])->name("updateprofile")->middleware('admincheckexiste');
      //end profile
       
      Route::get("checktotalproduct",[ProductController::class,"checktotalproduct"]);
      //password change 
      Route::get("changepassword",[Admincontroller::class,"changepassword"])->name("changepassword")->middleware('admincheckexiste');
      Route::get("samepwd/{id}",[Admincontroller::class,"check_password_same"]);
      Route::post("updatepassword",[Admincontroller::class,"updatepassword"]);

      Route::get("contact",[QuestionSupportController::class,"contactindex"]);

      Route::get("notification",[NotificationController::class,"index"]);
      Route::get("notificationTable",[NotificationController::class,"notificationTable"]);
      Route::post("sendnotification",[NotificationController::class,"addsendnotification"]);
      //end password change

      //categories
      Route::get("category",[Categorycontroller::class,"index"])->name("category");
      Route::get("categorydatatable",[Categorycontroller::class,"categorydatatable"])->name('categorydatatable');
      Route::post("addcategory",[Categorycontroller::class,"addcategory"])->name("addcategory");
      Route::get("getcategorybyid/{id}",[Categorycontroller::class,"getcategorybyid"])->name("getcategorybyid");
      Route::post("updatecategory",[Categorycontroller::class,"updatecategory"])->name("updatecategory");
      Route::get("sepical_category",[Categorycontroller::class,"sepical_category"])->name("sepical_category");
      Route::get("sepicalcategorytable",[Categorycontroller::class,"sepicalcategorytable"])->name("sepicalcategorytable");
      Route::get("addsepicalcategory",[Categorycontroller::class,"addsepicalcategory"])->name("addsepicalcategory");
      Route::post("storesepicalcategory",[Categorycontroller::class,"storesepicalcategory"])->name("storesepicalcategory");
      Route::get("editsepicalcategory/{id}",[Categorycontroller::class,"editsepicalcategory"])->name("editsepicalcategory");
      Route::post("updatesepicalcategory",[Categorycontroller::class,"updatesepicalcategory"])->name("updatesepicalcategory");
      Route::get("sepicalchange/{id}",[Categorycontroller::class,"sepicalchange"])->name("sepicalchange");
      Route::get("deletecategory/{id}",[Categorycontroller::class,"deletecategory"])->name("deletecategory");
      Route::get("deletebrand/{id}",[Categorycontroller::class,"deletebrand"])->name("deletebrand");

        //sub category
        Route::get("subcategory/{id}",[Categorycontroller::class,"subindex"])->name("subcategory");
        Route::get("subcategorydatatable/{id}",[Categorycontroller::class,"subdatatable"])->name("subcategorydatatable");
        Route::post("subaddcategory",[Categorycontroller::class,"subaddcategory"])->name("subaddcategory");

        Route::get("brand/{id}",[Categorycontroller::class,"brandindex"])->name("brand");
        Route::get("branddatatable/{id}",[Categorycontroller::class,"branddatatable"])->name('branddatatable');
        Route::post("addbrand",[Categorycontroller::class,"addbrand"])->name("addbrand");
        Route::get("getbrandbyname/{id}",[Categorycontroller::class,"getbrandbyname"])->name("getbrandbyid");
        Route::post("updatebrand",[Categorycontroller::class,"updatebrand"])->name("updatebrand");
        Route::get("viewscategory",[Categorycontroller::class,"viewcategory"])->name("viewcategory");
        Route::get("getallsubcategory",[Categorycontroller::class,"getallsubcategory"]);
        Route::get("product",[ProductController::class,"showproduct"])->name('product');
        Route::get("productdatatable",[ProductController::class,"productdatatable"])->name("productdatatable");
        Route::get("addcatalog",[ProductController::class,"showaddcatalog"])->name("addcatalog");
        Route::get("getsubcategory/{id}",[ProductController::class,"getsubcategory"])->name("getsubcategory");
        Route::get("getbrandbyid/{id}",[ProductController::class,"getbrandbyid"])->name("getbrandbyid");
        Route::post("saveproduct",[ProductController::class,"saveproduct"])->name("saveproduct");
        Route::get("changeproductstatus/{id}",[ProductController::class,"changeproductstatus"]);
        Route::post("saveprice",[ProductController::class,"saveprice"])->name("saveprice");
        Route::post("saveinventory",[ProductController::class,"saveinventory"])->name("saveinventory");
        Route::post("saveproductimage",[ProductController::class,"saveproductimage"])->name("#saveproductimage");
         Route::post("saveseoinfo",[ProductController::class,"saveseoinfo"])->name("saveseoinfo");
         Route::get("getoptionvalues/{id}",[ProductController::class,"getoptionvalues"])->name("getoptionvalues");
         Route::post("productuploadimg",[ProductController::class,"productuploadimg"])->name("productuploadimg");
         Route::get("getallproduct",[ProductController::class,"getallproduct"])->name("getallproduct");
         Route::post("updateproduct",[ProductController::class,"updateproduct"])->name("updateproduct");
         Route::post("saverealtedprice",[ProductController::class,"saverealtedprice"])->name("saverealtedprice");


     Route::get("savecatalog/{id}/{tab}",[ProductController::class,"showaddcatalog"])->name("addcatalog");
     
       Route::get("editproduct/{id}",[ProductController::class,"editproduct"])->name("editproduct");
       Route::post("saveadditionalinfo",[ProductController::class,"saveadditionalinfo"])->name("saveadditionalinfo");
       Route::get("getattibutevalue/{id}",[ProductController::class,"getattibutevalue"])->name("getattibutevalue");
       Route::get("getattributedata",[ProductController::class,"getattributedata"])->name("getattributedata");

       route::post("saveproductattibute",[ProductController::class,"saveproductattibute"])->name("saveproductattibute");
       Route::post("saveproductoption",[ProductController::class,"saveproductoption"])->name("saveproductoption");
       Route::post("saverelatedproduct",[ProductController::class,"saverelatedproduct"])->name("saverelatedproduct");
       Route::post("saveupsellproduct",[ProductController::class,"saveupsellproduct"])->name("saveupsellproduct");
       Route::post("savecrosssellproduct",[ProductController::class,"savecrosssellproduct"])->name("savecrosssellproduct");
       Route::get("getproductprice/{id}",[ProductController::class,"getproductprice"])->name("getproductprice");
       Route::get("userdelete/{id}",[UserController::class,"userdelete"]);

        Route::get("attributeset",[ProductController::class,"showattset"])->name('attributeset');
        Route::get("AttributeSetdatatable",[ProductController::class,"AttributeSetdatatable"])->name("AttributeSetdatatable");
        Route::post("addattrset",[ProductController::class,"addattrset"])->name("addattrset");
        route::get("getattrsetbyid/{id}",[ProductController::class,"getattrsetbyid"])->name("getattrsetbyid");
        Route::post("updateattrset",[ProductController::class,"updateattrset"])->name("updateattrset");
        Route::get("options",[ProductController::class,"indexoption"]);
        Route::get("Optiondatatable",[ProductController::class,"Optiondatatable"])->name("Optiondatatable");
        Route::get("addoption",[ProductController::class,"showaddoption"])->name("addoption");
        Route::post("saveoption",[ProductController::class,"saveoption"])->name("saveoption");
        Route::get("editoption/{id}",[ProductController::class,"editoption"])->name("editoption");
        Route::post("updateoption",[ProductController::class,"updateoption"])->name("updateoption");

        Route::get("attribute",[ProductController::class,"showattribute"])->name("attribute");
        Route::get("attributedatatable",[ProductController::class,"attributedatatable"])->name("attributedatatable");
        Route::get("addattribute",[ProductController::class,"showaddattribute"])->name("showaddattribute");   
        route::post("saveattribute",[ProductController::class,"saveattribute"])->name("saveattribute");
        Route::get("editattribute/{id}",[ProductController::class,"editattribute"])->name("editattribute");  
        Route::post("updateattribute",[ProductController::class,"updateattribute"])->name("updateattribute"); 

       Route::get("review",[ProductController::class,"showreview"]);
       Route::get("reviewdatatable/{id}",[ProductController::class,"reviewdatatable"])->name("reviewdatatable");
       Route::get("changereview/{id}",[ProductController::class,"changereview"])->name("changereview");

       Route::post("productimagesection",[ProductController::class,"productimage"])->name("productimagesection");
       Route::get("productlist/{id}/{pro_id}",[ProductController::class,"productlist"])->name("productlist");
       Route::get("deletecatlog/{id}",[ProductController::class,"deletecatlog"])->name("deletecatlog");
       Route::get("deleteoption/{id}",[ProductController::class,"deleteoption"])->name("deleteoption");
       Route::get("deleteattset/{id}",[ProductController::class,"deleteattset"])->name("deleteattset");
       Route::get("deleteattribute/{id}",[ProductController::class,"deleteattribute"])->name("deleteattribute");
       Route::get("deletereview/{id}",[ProductController::class,"deletereview"])->name("deletereview");


       Route::get("banner",[BannerController::class,"showbanner"])->name("showbanner");
       Route::post("bannerupload",[BannerController::class,"bannerupload"])->name("bannerupload");
       Route::post("updatebanner",[BannerController::class,"updatebanner"])->name("updatebanner");

       Route::get("offer",[OfferController::class,"showoffer"])->name("showoffer");
       Route::get("addoffer",[OfferController::class,"showaddoffer"])->name("addoffer");
       Route::get("getofferon/{id}",[OfferController::class,"getofferon"])->name("getofferon");
       Route::get("offerdatatable/{id}",[OfferController::class,"offerdatatable"])->name("offerdatatable");
       Route::post("storeoffer",[OfferController::class,"storeoffer"])->name("storeoffer");
       Route::get("sensonal_offer",[OfferController::class,"showsensonaloffer"])->name("sensonal_offer");
       Route::get("sensonaldatatable",[OfferController::class,"sensonaldatatable"])->name("sensonaldatatable");
       Route::get("add_sensonal_offer",[OfferController::class,"addsensonal"])->name("add_sensonal_offer");
       Route::post("storesensonal",[OfferController::class,"storesensonal"])->name("storesensonal");
       Route::get("changespeofferstatus/{id}",[OfferController::class,"changeoffer"])->name("changespeofferstatus");
        Route::get("addoffersection/{id}",[OfferController::class,"addoffersection"])->name("addoffersection");
        Route::post("storeofferdata",[OfferController::class,"storeofferdata"])->name("storeofferdata");
        Route::get("deleteoffer/{id}",[OfferController::class,"deleteoffer"])->name("deleteoffer");
        Route::get("deletesensonaloffer/{id}",[OfferController::class,"deletesensonaloffer"])->name("deletesensonaloffer");
        Route::get("deals",[OfferController::class,"deals"])->name("deals");
        Route::get("editdeal/{id}",[OfferController::class,"editdeal"])->name("editdeal");
        Route::get("dealdatatable",[OfferController::class,"dealdatatable"])->name("dealdatatable");
        Route::get("getofferfordeal/{deal_id}",[OfferController::class,"getofferfordeal"])->name("getofferfordeal");
        Route::get("updatedeal/{deal_id}/{offer_id}",[OfferController::class,"updatedeal"])->name("updatedeal");
        Route::get("editoffer/{id}",[OfferController::class,"editoffer"]);
        Route::post("updateofferdata",[OfferController::class,"updateofferdata"])->name("updateofferdata");
        Route::get("normaloffer",[OfferController::class,"shownormaloffer"]);

        Route::get("coupon",[CouponController::class,"index"]);
        Route::get("coupondatatable",[CouponController::class,"coupondatatable"])->name("coupondatatable");
        Route::get("addcoupon",[CouponController::class,"addcoupon"])->name("addcoupon");
        Route::post("savecoupon",[CouponController::class,"savecoupon"])->name("savecoupon");
        Route::get("deletecoupon/{id}",[CouponController::class,"deletecoupon"]);
        Route::post("savecouponsecondstep",[CouponController::class,"savecouponsecondstep"])->name("savecouponsecondstep");
        Route::post("savecouponstepthree",[CouponController::class,"savecouponstepthree"])->name("savecouponstepthree");
        Route::get("editcoupon/{id}",[CouponController::class,"editcoupon"])->name("editcoupon");

        Route::get("user",[UserController::class,"index"])->name("user");
        Route::get("userdatatable/{id}",[UserController::class,"userdatatable"])->name("userdatatable");
        Route::post("adduser",[UserController::class,"adduser"])->name("adduser");
        Route::get("changeuserstatus/{id}",[UserController::class,"changestatus"])->name("changeuserstatus");
        Route::get("edituser/{id}",[UserController::class,"edituser"])->name("edituser");
        Route::post("updateuser",[UserController::class,"updateuser"])->name("updateuser");
        Route::get("userrole",[UserController::class,"userrole"])->name("userrole");
        Route::get("roletable",[UserController::class,"roletable"])->name("roletable");
        Route::get("admin",[UserController::class,"indexadmin"])->name("admin");

        Route::get("pages",[SettingController::class,"indexpage"])->name("page");
        Route::get("pagedatatable",[SettingController::class,"pagedatatable"])->name("pagedatatable");

        Route::get("editpage/{id}",[SettingController::class,"editpage"])->name("editpage");
        Route::post("updatepage",[SettingController::class,"updatepage"])->name("updatepage");
        Route::post("savesoicalsetting",[SettingController::class,"savesoicalsetting"])->name("savesoicalsetting");
        Route::get("shipping",[SettingController::class,"showshipping"])->name("shipping");
        Route::get("shippingdatatable",[SettingController::class,"shippingdatatable"])->name("shippingdatatable");
        Route::get("changeshipping_status/{id}",[SettingController::class,"changeshipping"])->name("changeshipping_status");
        Route::get("editshipping/{id}",[SettingController::class,"editshipping"])->name("editshipping");
        Route::post("updateshipping",[SettingController::class,"updateshipping"])->name("updateshipping");
        Route::post("savepaymentdata",[SettingController::class,"savepaymentdata"])->name("savepaymentdata");

        Route::get("taxes",[TaxesController::class,"showtaxes"])->name("taxes");
        Route::get("taxesdatatable",[TaxesController::class,"taxesdatatable"])->name("taxesdatatable");
        Route::get("addtaxes",[TaxesController::class,"addtaxes"])->name("addtaxes");
        Route::post("storetaxes",[TaxesController::class,"storetaxes"])->name("storetaxes");
        Route::get("edittaxes/{id}",[TaxesController::class,"edittaxes"])->name("edittaxes");
        Route::post("updatetaxdata",[TaxesController::class,"updatetaxdata"])->name("updatetaxdata");

       
        Route::get("setting",[SettingController::class,"editsetting"])->name("setting");
        Route::get("getcountrylist",[SettingController::class,"getcountrylist"])->name("getcountrylist");
        Route::get("getlanglist",[SettingController::class,"getlanglist"])->name("getlanglist");
        Route::post("updatesetting",[SettingController::class,"updatesetting"])->name("updatesetting");
        route::post("savegeneralsetting",[SettingController::class,"updatesetting"])->name("savegeneralsetting");
          
        Route::get("order",[OrderController::class,"showorder"])->name("order");
        Route::get("orderdatatable",[OrderController::class,"orderdatatable"])->name("orderdatatable");

        Route::get("vieworder/{id}",[OrderController::class,"vieworder"])->name("vieworder");
        Route::get("generateorderpdf/{id}",[OrderController::class,"generateorderpdf"])->name("generateorderpdf");
        Route::get("transactionorder",[OrderController::class,"showtransactionorder"]);
        Route::get("transactiondatatable",[OrderController::class,"transactiondatatable"]);
        Route::get("changeorderstatus/{order_id}/{status_id}",[OrderController::class,"changeorderstatus"])->name("changeorderstatus");

        Route::get("report",[ReportController::class,"index"]);
        Route::get("couponreport/{start_date}/{end_date}/{order_status}/{code}",[ReportController::class,"couponreport"]);
        Route::get("customer_order_report/{start_date}/{order_date}/{order_status}/{name}/{email}",[ReportController::class,"customerOrder"]);
        Route::get("product_purchase_report/{start_date}/{order_date}/{order_status}/{product}/{sku}",[ReportController::class,"product_purchase_report"]);
        route::get("add_coupon_report/{start_date}/{end_date}",[ReportController::class,"add_coupon_report"]);
        Route::get("add_customer_report/{start_date}/{end_date}",[ReportController::class,"add_customer_report"]);
        Route::get("add_product_report/{start_date}/{end_date}",[ReportController::class,"add_product_report"]);
        Route::get("tax_report/{start_date}/{end_date}/{tax_name}",[ReportController::class,"tax_report"]);
        Route::get("shipping_report/{start_date}/{end_date}/{shipping_method}",[ReportController::class,"shipping_report"]);
       Route::get("sales_report/{start_date}/{end_date}/{order_status}",[ReportController::class,"sales_report"]);
       Route::get("product_stock_report/{product}/{sku}/{stock}",[ReportController::class,"product_stock_report"]);
       Route::get("top_seller_report/{start_date}/{end_date}",[ReportController::class,"top_seller_report"]);

        Route::get("latestorder",[OrderController::class,"latestorder"])->name("latestorder");
        Route::get("latestreview",[OrderController::class,"latestreview"])->name("latestreview");
        Route::post("sendordermail",[OrderController::class,"sendordermail"])->name("sendordermail");

        Route::get("featureproduct",[FeatureProductController::class,"index"])->name("featureproduct");
        Route::get("featureproductdatatable",[FeatureProductController::class,"featureproductdatatable"])->name("featureproductdatatable");
        Route::get("deletefeature/{id}",[FeatureProductController::class,"deletefeature"])->name("deletefeature");
        Route::post("addfeatureproduct",[FeatureProductController::class,"addfeatureproduct"])->name("addfeatureproduct");

        Route::get("support/{id}",[QuestionSupportController::class,"helpindex"]);
        Route::get("topicdatatable/{id}",[QuestionSupportController::class,"topicdatatable"]);
        Route::post("addsupporttopic",[QuestionSupportController::class,"addsupporttopic"]);
        Route::get("questionans/{support_id}/{topic_id}",[QuestionSupportController::class,"questionansindex"]);
        Route::post("addquesans",[QuestionSupportController::class,"addquesans"]);
        Route::get("quesdatatable/{topic_id}",[QuestionSupportController::class,"quesdatatable"]);
        Route::get("editsupport/{id}",[QuestionSupportController::class,"editsupport"]);
        Route::post("updatetopic",[QuestionSupportController::class,"updatetopic"]);
        Route::get("deletesupport/{id}",[QuestionSupportController::class,"deletesupport"]);
        Route::get("editques/{id}",[QuestionSupportController::class,"editques"]);
        Route::post("updatequestion",[QuestionSupportController::class,"updatequestion"]);
        Route::get("deletequestion/{id}",[QuestionSupportController::class,"deletequestion"]);
        Route::get("contactdatatable",[QuestionSupportController::class,"contactdatatable"]);
        Route::get("deletecontact/{id}",[QuestionSupportController::class,"deletecontact"]);


        Route::resource("complain",ComplainController::class);
        Route::get("complaindatatable",[ComplainController::class,"complaindatatable"]);

        Route::get("notification/{id}",[OrderController::class,"notification"]);
        Route::get("getcoupondata/{id}",[OfferController::class,"getcoupondata"]);

        Route::get("serverkey/{id}",[SettingController::class,"serverkey"]);
        Route::post("updatekey",[SettingController::class,"updatekey"]);

        Route::get("datatable",[ComplainController::class,"datatabletest"]);
        Route::get("changesettingstatus/{field}",[SettingController::class,"changesettingstatus"]);
        
        Route::get("news",[BannerController::class,"shownews"]);
        Route::post("sennews",[BannerController::class,"sendnews"]);

        Route::get("savebanner/{category_id}/{brand_id}",[Categorycontroller::class,"savebanner"]);
        Route::post("updatebarndbanner",[Categorycontroller::class,"updatebarndbanner"]);
        Route::get("removebanner/{id}",[Categorycontroller::class,"removebanner"]);

        Route::get("seller",[UserController::class,"showseller"]);
        Route::get("sellerTable",[UserController::class,"sellerTable"]);
        
        /*My New Route */
        Route::any("check_email",[UserController::class,"check_email"])->name("check_email");
        /*DONE*/
        Route::post("updateseller",[UserController::class,"updateseller"]);

        Route::get("editseller/{id}",[UserController::class,"editseller"]);

        Route::get("unapprove_product",[ProductController::class,"unapprove_product"]);
        Route::get("unapproveproductdataTable",[ProductController::class,"unapproveproductdataTable"]);

        Route::get("city",[CityController::class,"showcity"]);
        Route::get("Citydatatable",[CityController::class,"Citydatatable"]);
        Route::post("addcity",[CityController::class,"addcity"])->name("addcity");
        Route::get("getcitybyid/{id}",[CityController::class,"getcitybyid"])->name("getcitybyid");
        Route::post("updatecity",[CityController::class,"updatecity"])->name("updatecity");
        Route::get("deletecity/{id}",[CityController::class,"deletecity"])->name("deletecity");

          Route::get("language",[LanguageController::class,"show_language"]);
          Route::get("language_datatable",[LanguageController::class,"language_datatable"]);
          Route::post("add_language",[LanguageController::class,"add_language"]);
          Route::get("translation/{code}",[LanguageController::class,"show_translation"]);
          Route::get("translationdatatable/{code}",[LanguageController::class,"translationdatatable"])->name("translationdatatable");
          Route::get("getdatatranslation/{id}",[LanguageController::class,"getdatatranslation"])->name("getdatatranslation");
          Route::any("updatetranslation",[LanguageController::class,"updatetranslation"])->name("updatetranslation");
           Route::get("deletelang/{id}",[LanguageController::class,"deletelang"]);

           Route::get("pendingpayment",[PaymentController::class,"pendingshowpayment"]);
        Route::get("completepayment",[PaymentController::class,"showcompletepayment"]);
        Route::get("pendingpaymentdatatable",[PaymentController::class,"pendingpaymentdatatable"]);
        Route::get("completepaymentdatatable",[PaymentController::class,"completepaymentdatatable"]);
        Route::get("currentpayment",[PaymentController::class,"currentpayment"]);
        Route::get("ordercurrentpaymentTable",[PaymentController::class,"ordercurrentpaymentTable"]);
        
         Route::get("payamount/{seller_id}",[PaymentController::class,"showpaymentform"]);
        Route::post("savechequepayment",[PaymentController::class,"savechequepayment"]);
        
        Route::get("deliveryboys",[DeliveryController::class,"index"]);
      Route::get("deliverydatatable",[DeliveryController::class,"deliverydatatable"])->name("deliverydatatable");
      Route::post("add_delivery_boy",[DeliveryController::class,"add_delivery_boy"])->name("add_delivery_boy");
      Route::get("deleteboy/{id}",[DeliveryController::class,"delete"]);
      Route::get("editdeliveryboys/{id}",[DeliveryController::class,"editdeliveryboys"]);
      Route::post("update_delivery_boy",[DeliveryController::class,"update_delivery_boy"]);
  
  
      Route::get("document_verification",[DeliveryController::class,"show_document_verification"])->name("document_verification");
      Route::get("documentverificationdatatable",[DeliveryController::class,"documentverificationdatatable"])->name("documentverificationdatatable");
      
      Route::get("document_status/{id}/{status}",[DeliveryController::class,"document_status"]);
   
   
      Route::any("assignorder",[OrderController::class,"show_assignorder"])->name("assignorder");
  
        Route::get("about",[FrontController::class,"about"])->name("about");
        Route::get("Terms_condition",[FrontController::class,"admin_privacy"])->name("Terms_condition");
        Route::get("app_privacy",[FrontController::class,"app_privacy"])->name("app_privacy");
        Route::get("data_deletion",[FrontController::class,"data_deletion"])->name("data_deletion");
        
        Route::post("edit_about",[FrontController::class,"edit_about"])->name("edit_about");
        Route::post("edit_terms",[FrontController::class,"edit_terms"])->name("edit_terms");
        Route::post("edit_app_privacy",[FrontController::class,"edit_app_privacy"])->name("edit_app_privacy");
        Route::post("edit_data_deletion",[FrontController::class,"edit_data_deletion"])->name("edit_data_deletion");
	

    });

});
