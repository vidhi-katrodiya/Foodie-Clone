<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\SellerController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CartDataController;
use App\Http\Controllers\API\ProductFilterController;
use App\Http\Controllers\API\DeliveryBoyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/*user*/
Route::any("userregister",[ApiController::class,"userregister"]);
Route::any("login",[ApiController::class,"Showlogin"]);
Route::any("getcategory",[ApiController::class,"getcategory"]);
Route::any("editprofile",[ApiController::class,"editprofile"]);
Route::any("getAddress",[ApiController::class,"show_getAddress"]);
Route::any("SaveAddress",[ApiController::class,"show_SaveAddress"]);
Route::any("couponlistforuser",[ApiController::class,"couponlistforuser"]);
Route::any("gethelp",[ApiController::class,"gethelp"]);
Route::any("getwishlist",[ApiController::class,"getwishlist"]);
Route::any("forgotpassword",[ApiController::class,"forgotpassword"]);
Route::any("verifiedcoupon",[ApiController::class,"verifiedcoupon1"]);
Route::any("addwish",[ApiController::class,"addwish"]);
Route::any("addreview",[ApiController::class,"postreview"]);
Route::any("viewproduct",[ApiController::class,"viewproduct"]);
Route::any("changes_password",[ApiController::class,"show_changes_password"]);
Route::any("DeleteAddress",[ApiController::class,"show_DeleteAddress"]);
Route::any("preplaceorder",[ApiController::class,"preplaceorder"]);
Route::any("placeorder",[ApiController::class,"postplaceorder"]);
Route::any("get_populer_restaurants",[SellerController::class,"get_populer_restaurants"]);
Route::any("restaurant_list",[SellerController::class,"get_restaurant_list"]);
Route::any("serach_restaurants",[SellerController::class,"serach_restaurants"]);
Route::any("get_restaurants_product",[SellerController::class,"get_restaurants_product"]);
Route::any("get_fillter_restaurant",[SellerController::class,"get_fillter_restaurant"]);
Route::any("get_restaurants_by_category",[SellerController::class,"get_restaurants_by_category"]);
Route::any("get_restaurants_by_filter",[SellerController::class,"get_restaurants_by_filter"]);
Route::any("vieworder",[OrderController::class,"vieworder"]);
Route::any("order_history",[OrderController::class,"order_history"]);
Route::any("getcart",[CartDataController::class,"getcart"]);
Route::any("addcart",[CartDataController::class,"addcart"]);
Route::any("removecart",[CartDataController::class,"removecart"]);
Route::any("removeallitem",[CartDataController::class,"removeallitem"]);


/*this api are not used in foodiclone*/
Route::any("get_paymet_page",[OrderController::class,"get_paymet_page"]);
Route::any("add_order_data",[ApiController::class,"add_order_data"]);
Route::any("delete_user",[ApiController::class,"delete_user"]);
Route::any("listofcity",[ApiController::class,"listofcity"]);
Route::any("getlang",[ApiController::class,"getlang"]);
Route::any("mainoffers",[ApiController::class,"mainoffers"]);
Route::any("page",[ApiController::class,"viewpage"]);
Route::any("searchsuggestion",[ApiController::class,"searchsuggestion"]);
Route::any("save_token",[ApiController::class,"save_token"]);
Route::any("searchproduct",[ApiController::class,"searchproduct"]);
Route::any("getbannerfrombrand",[ApiController::class,"getbannerfrombrand"]);
Route::any("categoryoffer",[ApiController::class,"categoryoffer"]);
Route::any("offers",[ApiController::class,"showoffers"]);
Route::any("categoryoffer",[ApiController::class,"categoryoffer"]);
Route::any("addcomplain",[ApiController::class,"addcomplain"]);
Route::any("gettax",[ApiController::class,"taxlist"]);
Route::any("verified_coupon_code",[ApiController::class,"verified_coupon_code"]);
Route::any("bestselling",[ApiController::class,"bestselling"]);
Route::any("productfilter",[ProductFilterController::class,"productfilter"]);
Route::any("offerfilter",[ApiController::class,"offerfilter"]);
Route::any("getcolorls/{category}/{subcategory}/{brand}",[ProductFilterController::class,"getcolorls"]);
Route::any("resendsms",[ApiController::class,"resendsms"]);
Route::any("searchcategorybypro",[ApiController::class,"searchcategorybypro"]);
Route::any("sendsms",[ApiController::class,"sendsms"]);
/**/

Route::any("about",[ApiController::class,"about"]);
Route::any("privacy",[ApiController::class,"privacy"]);

Route::group(['prefix' => 'DeliveryBoy'], function (){
        Route::any("register",[DeliveryBoyController::class,"postregister"]);
        Route::any("login",[DeliveryBoyController::class,"Showlogin"]);
        Route::any("editprofile",[DeliveryBoyController::class,"show_editprofile"]);
        Route::any("order_history",[DeliveryBoyController::class,"order_history"]);
        Route::any("order_list",[DeliveryBoyController::class,"show_order_list"]);
        Route::any("order_action",[DeliveryBoyController::class,"show_order_action"]);
        Route::any("presence",[DeliveryBoyController::class,"deliveryboy_presence"]);
        Route::any("total_amount_order_list",[DeliveryBoyController::class,"total_amount_order_list"]);


        /*this api are not used in foodiclone*/
        Route::any("saveDocument",[DeliveryBoyController::class,"saveDocument"]);
        Route::any("getDocument",[DeliveryBoyController::class,"getDocument"]);
        Route::any("deleteDocument",[DeliveryBoyController::class,"deleteDocument"]);
        Route::any("today_orders",[DeliveryBoyController::class,"today_orders"]);
        Route::any("delete_delivery_boy",[DeliveryBoyController::class,"delete_delivery_boy"]);
        Route::any("get_document_status",[DeliveryBoyController::class,"get_document_status"]);
        Route::any("order_history_by_filter",[DeliveryBoyController::class,"order_history_by_filter"]);
        /**/
});

Route::group(['prefix' => 'Seller'], function (){
    Route::any("register",[SellerController::class,"postregister"]);
    Route::any("login",[SellerController::class,"Showlogin"]);
    Route::any("editprofile",[SellerController::class,"show_editprofile"]);
    Route::any("view_profile",[SellerController::class,"view_profile"]);
    Route::any("add_category",[SellerController::class,"add_category"]);
    Route::any("categorydelete",[SellerController::class,"show_categorydelete"]);
    Route::any("saveproduct",[SellerController::class,"post_saveproduct"]);
    Route::any("saveproductimage",[SellerController::class,"post_saveproductimage"]);
    Route::any("saveproductoption",[SellerController::class,"post_saveproductoption"]);
    Route::any("savecoupon",[SellerController::class,"show_savecoupon"]);
    Route::any("productlistbystore",[SellerController::class,"show_productlistbystore"]);
    Route::any("listofcoupon",[SellerController::class,"show_listofcoupon"]);
    Route::any("coupon_detail",[SellerController::class,"show_coupon_detail"]);
    Route::any("listofoption",[SellerController::class,"show_listofoption"]);
    Route::any("productdelete",[SellerController::class,"show_productdelete"]);
    Route::any("coupondelete",[SellerController::class,"show_coupondelete"]);
    Route::any("order_history",[SellerController::class,"order_history"]);
    Route::any("order_list",[SellerController::class,"show_order_list"]);
    Route::any("total_amount_order_list",[SellerController::class,"total_amount_order_list"]);
    Route::any("order_action",[SellerController::class,"show_order_action"]);
    Route::any("getmenucategory",[SellerController::class,"getmenucategory"]);
    Route::any("get_res_product",[SellerController::class,"get_res_product"]);
    Route::any("delete_owner",[SellerController::class,"delete_owner"]);
    Route::any("get_category",[SellerController::class,"get_category"]);    
    Route::any("restaurant_list",[SellerController::class,"get_restaurant_list"]);
});




