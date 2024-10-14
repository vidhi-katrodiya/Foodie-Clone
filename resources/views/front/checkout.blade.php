@extends('front.layout')

@section('title')
  {{__("messages.checkout")}}
@endsection

@section('content')
<style type="text/css">
  .owl-carousel{
    display: flex !important;
  }
  .card{
    height:200px;
  }
  .mall-category-item img{
    height: 60px;
    width: 100%;
  }

  .selector{
      position:relative;
      width:100%;
      background-color:var(--smoke-white);
      height:55px;
      display:flex;
      justify-content:space-around;
      align-items:center;
      border-radius:10px;
      box-shadow:0 0 16px rgba(0,0,0,.2);
  }
  .selecotr-item{
      position:relative;
      flex-basis:calc(80% / 3);
      height:100%;
      margin-bottom:-10px;
      display:flex;
      justify-content:center;
      align-items:center;
  }
  .selector-item_radio{
      appearance:none;
      display:none;
  }
  .selector-item_label{
      position:relative;
      height:64%;
      width:100%;
      color:#dc3545;
      text-align:center;
      border-radius:10px;
      line-height:229%;
      font-weight:600;
      transition-duration:.5s;
      transition-property:transform, color, box-shadow;
      transform:none;
  }
  .selector-item_radio:checked + .selector-item_label{
    font-size: 16px;
      background-color:#db5359;
      color:var(--white);
      box-shadow:0 0 4px rgba(0,0,0,.5),0 2px 4px rgba(0,0,0,.5);
      transform:translateY(-2px);
  }
  @media (max-width:480px) {
    .selector{
      width: 90%;
    }
  }

</style>
 
<section class="offer-dedicated-body mt-4 mb-4 pt-2 pb-2">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="offer-dedicated-body-left">
          <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h6 class="mb-3">You may also like</h6>

              @php
              $product = DB::table('products')->where('user_id',$cart_res->res_id)->get();
              @endphp

              <div class="owl-carousel owl-theme owl-carousel-five offers-interested-carousel" >
                @foreach($product as $value)
                  <div class="item">
                    <div class="mall-category-item position-relative ">
                      @php
                        $cat = DB::table('product_options')->where('product_id',$value->id)->get();
                        $count_opt = count($cat);
                      @endphp

                      @if($count_opt>0)
                        <a  id="add" class="btn btn-primary btn-sm position-absolute" onclick="product_option({{$value->id}})" type="button" data-bs-toggle="modal" data-bs-target="#modal" style="color:white"> ADD </a>
                      @else
                        <a  id="add" class="btn btn-primary btn-sm position-absolute" onclick="add_out_opt({{$value->id}})" type="button" style="color:white">ADD </a>
                      @endif
                    
                      <img class="img-fluid" src="{{url('public/upload/product/')}}/{{$value->basic_image}}" style="height:86px;">
                      <h6>{{$value->name}}</h6>
                      <small><?php echo "₹".number_format($value->price,2); ?></small>
                    </div>
                  </div>
                @endforeach
              </div>
          </div>

          <div class="pt-2"></div>
          <div class="bg-white rounded shadow-sm p-4 mb-4">
          
            <h4 class="mb-1">Choose a delivery method</h4><br>
            <div class="selector">
              <div class="selecotr-item">
                  <input type="radio" id="radio1" name="add_name" class="selector-item_radio" value="Home" checked onchange="hide_add()">
                  <label for="radio1" class="selector-item_label" >Home Delivery</label>
              </div>
              <div class="selecotr-item">
                  <input type="radio" id="radio2" name="add_name" class="selector-item_radio" value="pick_up" onchange="hide_add()">
                  <label for="radio2" class="selector-item_label">Pick-up</label>
              </div>
            </div><br>

            <div id="show_add">
              <h4 class="mb-1">Choose a delivery address</h4>
                
                <p class="mb-0 text-black font-weight-bold" style="float:right;">
                  @if(Auth::id())
                    <a href="add_address/{{$user->id}}" type="button"  class="btn btn-sm btn-primary mr-2">ADD NEW ADDRESS</a>
                  @else
                    <a  data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-sm btn-primary mr-2" type="button" style="float:right;">ADD NEW ADDRESS </a> 
                  @endif
                </p>
              <h6 class="mb-3 text-black-50">Multiple addresses in this location</h6>
              @if(Auth::id())
                @php
                  $add1 = DB::table('address')->where('user_id',Auth::id())->where('name','Home')->orderBy('id','desc')->first();
                  $add2 = DB::table('address')->where('user_id',Auth::id())->where('name','Office')->orderBy('id','desc')->first();
                  $add3 = DB::table('address')->where('user_id',Auth::id())->where('name','Other')->orderBy('id','desc')->limit(2)->get();
                  $user = DB::Table('users')->where('id',Auth::id())->first();
                @endphp
                <div class="row">
                @if($add1)
                    @if($add1->address == $user->shipping_address)

                        <div class="col-md-6">
                          <div class="bg-white card addresses-item mb-4 address-<?=$add1->id?>" style="border-color:#3ecf8e;">
                            <div class="gold-members p-4">
                              <div class="media">
                                <div class="mr-3">
                                  <i class="icofont-ui-home icofont-3x icon icon-<?=$add1->id?>" style="color:#3ecf8e;"></i>
                                </div>
                                <div class="media-body">
                                  <h6 class="mb-1 text-black">{{$add1->name}}</h6>
                                  <p class="text-black-<?=$add1->id?> text-black">{{$add1->address}}. </p>
                                  <p class="mb-0 text-black font-weight-bold">
                                    <a class="btn btn-sm btn-secondary mr-2 ajax-<?=$add1->id?> add_btn" onclick="add_current_add({{$add1->id}})" style="color:white; background-color: #3ecf8e; border-color:#3ecf8e;" > DELIVER HERE</a>
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    @else
                        <div class="col-md-6">
                          <div class="bg-white card addresses-item mb-4 address-<?=$add1->id?>">
                            <div class="gold-members p-4">
                              <div class="media">
                                <div class="mr-3">
                                  <i class="icofont-ui-home icofont-3x"></i>
                                </div>
                                <div class="media-body">
                                  <h6 class="mb-1 text-black icon icon-<?=$add1->id?>">{{$add1->name}}</h6>
                                  <p class="text-black-<?=$add1->id?>">{{$add1->address}}. </p>
                                  <p class="mb-0 text-black font-weight-bold">
                                    <a class="btn btn-sm btn-secondary mr-2 ajax-<?=$add1->id?> add_btn" onclick="add_current_add({{$add1->id}})" style="color:white;"> DELIVER HERE</a>
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    @endif
                @endif
                
                @if($add2)
                     @if($add2->address == $user->shipping_address)
                        <div class="col-md-6 ">
                          <div class="bg-white card addresses-item mb-4 address-<?=$add2->id?>" style="border-color:#3ecf8e;">
                            <div class="gold-members p-4">
                              <div class="media">
                                <div class="mr-3">
                                  <i class="icofont-briefcase icofont-3x icon icon-<?=$add2->id?>" style="color:#3ecf8e;"></i>
                                </div>
                                <div class="media-body">
                                  <h6 class="mb-1 text-secondary">{{$add2->name}}</h6>
                                  <p class="text-black text-black-<?=$add2->id?>">{{$add2->address}}}. </p>
                                  <p class="mb-0 text-black font-weight-bold">
                                   <a class="btn btn-sm btn-secondary mr-2 ajax-<?=$add2->id?> add_btn" onclick="add_current_add({{$add2->id}})" style="color:white; background-color: #3ecf8e; border-color:#3ecf8e;"> DELIVER HERE</a>
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                     @else
                        <div class="col-md-6 ">
                          <div class="bg-white card addresses-item mb-4 address-<?=$add2->id?>">
                            <div class="gold-members p-4">
                              <div class="media">
                                <div class="mr-3">
                                  <i class="icofont-briefcase icofont-3x  icon icon-<?=$add2->id?>"></i>
                                </div>
                                <div class="media-body">
                                  <h6 class="mb-1 text-secondary">{{$add2->name}}</h6>
                                  <p class="text-black-<?=$add2->id?>">{{$add2->address}}. </p>
                                  <p class="mb-0 text-black font-weight-bold">
                                   <a class="btn btn-sm btn-secondary mr-2 ajax-<?=$add2->id?> add_btn" onclick="add_current_add({{$add2->id}})" style="color:white;"> DELIVER HERE</a>
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      @endif
                @endif
                </div>
              
                @if(count($add3) > 0)
                <div class="row">
                  @foreach($add3 as $val)
                      @if($val->address == $user->shipping_address)
                        <div class="col-md-6 ">
                            <div class="bg-white card addresses-item address-<?=$val->id?>" style="border-color:#3ecf8e;"> 
                              <div class="gold-members p-4">
                                <div class="media">
                                  <div class="mr-3">
                                    <i class="icofont-location-pin icofont-3x  icon icon-<?=$val->id?>" style="color:#3ecf8e;""></i>
                                  </div>
                                  <div class="media-body">
                                    <h6 class="mb-1 text-secondary">{{$val->name}}</h6>
                                    <p class="text-black  text-black-<?=$val->id?>">{{$val->address}}. </p>
                                    <p class="mb-0 text-black font-weight-bold">
                                      <a class="btn btn-sm btn-secondary mr-2 ajax-<?=$val->id?> add_btn" onclick="add_current_add({{$val->id}})" style="color:white; background-color: #3ecf8e; border-color:#3ecf8e;"> DELIVER HERE</a>
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                      @else
                        <div class="col-md-6 ">
                            <div class="bg-white card addresses-item address-<?=$val->id?>">
                              <div class="gold-members p-4">
                                <div class="media">
                                  <div class="mr-3">
                                    <i class="icofont-location-pin icofont-3x  icon icon-<?=$val->id?>" ></i>
                                  </div>
                                  <div class="media-body">
                                    <h6 class="mb-1 text-secondary">{{$val->name}}</h6>
                                    <p  class="text-black-<?=$val->id?>">{{$val->address}}. </p>
                                    <p class="mb-0 text-black font-weight-bold">
                                      <a class="btn btn-sm btn-secondary mr-2 ajax-<?=$val->id?> add_btn" onclick="add_current_add({{$val->id}})" style="color:white;"> DELIVER HERE</a>
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                      @endif
                  @endforeach
                </div>
                @endif
              @endif
            </div>
          </div>
          <div class="pt-2"></div>
        
        </div>
      </div>
      <div class="col-md-4">
      @if(Auth::id())
          
        @if(DB::table('users')->where('id',$cart_res->res_id)->exists())
          @php
          $res_name = DB::table('users')->where('id',$cart_res->res_id)->first();
          @endphp
          
          <div class="generator-bg rounded shadow-sm mb-4 p-4 osahan-cart-item">
            <div class="d-flex mb-4 osahan-cart-item-profile">
              <img class="img-fluid mr-3 rounded-pill" alt="osahan" src="{{url('public/upload/restaurant')}}/{{$res_name->res_image}}">
              <div class="d-flex flex-column">
                
                <h6 class="mb-1 text-white">{{$res_name->first_name}}</h6>
                <p class="mb-0 text-white">
                  <i class="icofont-location-pin"></i>{{$res_name->address}}
                </p>
              </div>
            </div>
            <div class="bg-white rounded shadow-sm mb-2">
              @php $total=0;  $tax=0; $discount=0; @endphp
              @foreach($cart as $value)
                  @php
                    
                    $product = DB::table('products')->where('id',$value->product_id)->first();

                    $price = $value->qty_price;
                    $total_price = $total + $price;
                    $total= $total_price;
                  
                    $admin = DB::table('setting')->first();
                    $delivery_charges = $admin->delivery_charges;

                    $total_pay = $total + $tax + $delivery_charges - $discount;
             
                  @endphp
                  
                <div class="gold-members p-2 border-bottom">
                  
                  <span class='count-number float-right'>
                    <button type='button' onclick="remove_cart({{$value->id}})" id='sub' class='sub btn btn-outline-secondary  btn-sm left dec'><i class='icofont-minus'></i>
                    </button>
                  
                    <input class='count-number-input' type='text' value='{{$value->qty}}' readonly='' min='1' max='10' readonly=''>

                    <button type='button' onclick="add({{$value->id}})" id='add' class='add btn btn-outline-secondary btn-sm right inc'>
                      <i class='icofont-plus' data-field='quantity' ></i>
                    </button> 
                  </span>
                  <div class="media">
                    
                    @if($product->is_veg == 1)
                      <div class='mr-2'>
                        <i class='icofont-ui-press text-success food-item'></i>
                      </div>
                    @else
                      <div class='mr-2'>
                        <i class='icofont-ui-press text-danger food-item'></i>
                      </div>
                    @endif
                    <div class='media-body'>
                      <p class='mt-1 mb-0 text-black'>{{$product->name}}</p>
                      <p class="text-gray mb-2">
                            
                          @if( DB::table('product_options')->where('product_id',$product->id)->exists() )
                            @php
                              $option_pro = DB::table('product_options')->where('product_id',$product->id)->first();  
                               $option = explode("#",$value->label);
                            @endphp

                             <?php echo implode(" • ",$option); ?>
                          @endif
                         
                          <h7 class='text-gray mb-0 float-right ml-2' style="margin-top:5px; margin-right:-60px;">
                             <?php echo "₹".number_format($value->qty_price,2); ?>
                          </h7>
                      </p>
                      </span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
            <div class="mb-2 bg-white rounded p-2 clearfix">
              
                    <div class="input-group input-group-sm mb-2">
                      <input type="text" name="coupan" class="form-control" placeholder="Enter promo code" id="coupon">
                      <div class="input-group-append">
                        <button class="btn btn-primary" onclick="discount()" type="submit" id="code">
                          <i class="icofont-sale-discount"></i> APPLY </button>
                      </div>
                    </div>
              <div id="error">
              </div>
              <div class="input-group mb-0">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="icofont-comment"></i>
                  </span>
                </div>
                <textarea class="form-control" placeholder="Any suggestions? We will pass it on..." name="note" id="note" aria-label="With textarea"></textarea>
              </div>
            </div>
              <input type="hidden" id="cart_res" value="{{$cart_res->res_id}}">

            <div class="mb-2 bg-white rounded p-2 clearfix">
              <input type="hidden" id="total" value="{{$total}}">
              <p class="mb-1">Item Total <span class="float-right text-dark"><?php echo "₹".number_format($total,2) ?></span>
              </p>
              <p class="mb-1">Tax <span class="float-right text-dark">
              <input type="hidden" id="tax" value="{{$cart_res->tax}}">
                <?php echo "₹".number_format($cart_res->tax,2) ?>
                </span>
              </p>

              <p class="mb-1">Delivery Charge <span class="text-info" data-toggle="tooltip" data-placement="top" title="Total discount breakup">
                  
                </span>
                <input type="hidden" id="delivery_charges" value="{{$delivery_charges}}">
                <span class="float-right text-dark"><?php echo "₹".number_format($delivery_charges,2) ?></span>
              </p>
              
                
              <p class="mb-1 text-success" >Total Discount 
                <input type="hidden" id="discount_in" value="0">
                <input type="hidden" id="total_amount_in" value="0">
                <span class="float-right text-success" id="total_amount"><?php echo "₹".number_format($discount,2) ?></span>
                (<span class=" text-success" id="discount">0%</span>)
              </p>
              <hr/>
             
              <h6 class="font-weight-bold mb-0" >TO PAY 
                <input type="hidden" id="pay_in" value="{{$total_pay}}">
                <span class="float-right" id="pay_amount"> <?php echo "₹".number_format($total_pay,2) ?></span>
              </h6>
            </div>

            <button onclick="add_order()" class="btn btn-success btn-block btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#pay_type">PAY <span class="pay"><?php echo "₹".number_format($total_pay,2) ?></span><i class="icofont-long-arrow-right"></i>
            </a>
          </div>
        @else
          <div class="generator-bg rounded shadow-sm mb-4 p-4 osahan-cart-item">
            <h5 class="mb-1 text-white">Your Order</h5>
                <p class="mb-4 text-white">0 ITEMS</p>
                <div class="bg-white rounded shadow-sm mb-2" id="cart">
                 
                </div>
                <div class="mb-2 bg-white rounded p-2 clearfix">
                  <img class="img-fluid float-left" src="{{url('public/front/img/wallet-icon.png')}}">
                  <h6 class="font-weight-bold text-right mb-2">Subtotal : <span class="text-danger"> ₹0.00</span>
                  </h6>
                  <p class="seven-color mb-1 text-right">Extra charges may apply</p>
                  <p class="text-black mb-0 text-right">You have saved ₹0.00 on the bill</p>
                </div>
          </div>
        @endif
        @else
          <div class="generator-bg rounded shadow-sm mb-4 p-4 osahan-cart-item">
            <h5 class="mb-1 text-white">Your Order</h5>
                <p class="mb-4 text-white">0 ITEMS</p>
                <div class="bg-white rounded shadow-sm mb-2" id="cart">
                 
                </div>
                <div class="mb-2 bg-white rounded p-2 clearfix">
                  <img class="img-fluid float-left" src="{{url('public/front/img/wallet-icon.png')}}">
                  <h6 class="font-weight-bold text-right mb-2">Subtotal : <span class="text-danger"> ₹0.00</span>
                  </h6>
                  <p class="seven-color mb-1 text-right">Extra charges may apply</p>
                  <p class="text-black mb-0 text-right">You have saved ₹0.00 on the bill</p>
                </div>
          </div>
        @endif
        <div class="pt-2"></div>
      </div>
    </div>
  </div>
</section>

<div class="modal" id="address">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="departmentpg-main-box" style="background-color: #F0FCF5;">
          <div class="container"> 
            <div class="global-heading">
              <h2>{{__('messages.add_address')}}<br>
                    <img src="{{asset('public/front/img/design-3.png')}}" class="heading-image" >
              </h2>     
            </div>
              <div class="row" style="background-color: #fff; padding: 10px">
                <div class="col-lg-7 col-md-6 col-sm-6">        
                    <div id="map" style=" height: 300px;width: 100%;"></div>
                    <div id="infowindow-content">
                      <span id="place-name" class="title"></span><br />
                      <span id="place-address"></span>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-6">
                  <div class="location-text-detail">
                      <div class="form-group">
                        <input type="text" name="address" id="address" class="location-input" placeholder="Address">
                      </div>
                      <span id="reg_name_error" class="dangerrequired"></span>
                      <div class="form-group">
                        <input type="text" name="floor" id="floor" class="location-input" placeholder="Floor / Tower">
                      </div>
                      <div class="form-group">
                        <input type="text" name="lat" id="lat">
                      </div>
                       <div class="form-group">
                        <input type="text" name="lng" id="lng">
                      </div>
                       <div class="form-group">
                        <input type="text" name="city" id="city">
                      </div>
                      <input type="text" required name="user_id" id="user_id" value="{{Auth::user()?Auth::user()->id:''}}">
                      <input type="text" required name="name" id="name" value="{{Auth::user()?Auth::user()->name:''}}">
                      <input type="text" required name="state" id="state">
                      <input type="text" required name="pincode" id="pincode" >
                      <div class="radio-btn-box" style="margin-bottom: 30px">
                        <input id="home" class="type" type="radio" name="type" value="home" checked="" />
                        <label for="home" class="label">{{__('messages.home')}}</label>
                        <input class="checkbox" class="type"  id="work" type="radio" name="type" value="work" />
                        <label for="work" class="label">{{__('messages.work')}}</label>
                          <input id="office" type="radio" class="type"  name="type" value="office"/>
                          <label for="office" class="label" >{{__('messages.office')}}</label>
                          <input class="checkbox" id="other" class="type"  type="radio" value="other" name="type" />
                          <label for="other" class="label">{{__('messages.other')}}</label>
                      </div>
                    
                      <button class="custom_font location-btn" type="submit" onclick="add_address_data()">{{__('messages.add_address')}}</button>    
                    
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> 

<div class="modal" id="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div id="loginmodel">
            <div class="part-form-main-box" >
              <form id="form" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" value="{{ csrf_token() }}" name="_token">

                <div class="row" id="option_data"> 

                </div> 

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="pay_type" tabindex="-1" role="dialog" aria-labelledby="edit-profile" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <form id="pay_form" action="" method="post" enctype="multipart/form-data">
              <input type="hidden" value="{{ csrf_token() }}" name="_token">
                <div class="modal-header">
                  <h5 class="modal-title" id="edit-profile">Select Payment Type</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                 
                    <input type="hidden" value="" name="id" id="order_id"> 

                    <div class="form-check payment" style="height: 80px;">
                      <input class="form-check-input" type="radio" name="pay_type" id="cod" value="cod" checked style="margin-left:400px; height:40px;" onchange="pay_method()">
                      <label class="form-check-label" for="cod">
                         <img src="{{asset('public/front/img/pay_icon/cod1.png')}}" style="width:150px;">
                      </label>
                    </div>

                    <div class="form-check payment" style="height: 80px;">
                      <input class="form-check-input" type="radio" name="pay_type" id="braintree" value="braintree" style="margin-left:400px; height:40px;" onchange="pay_method()">
                      <label class="form-check-label" for="braintree">
                        <img src="{{asset('public/front/img/pay_icon/braintree1.png')}}" style="width:150px; margin-top:-23px;">
                      </label>
                    </div> 

                     <div class="form-check payment" style="height: 80px;">
                      <input class="form-check-input" type="radio" name="pay_type" id="paystack" value="paystack" style="margin-left:400px; height:40px;" onchange="pay_method()">
                      <label class="form-check-label" for="paystack">
                        <img src="{{asset('public/front/img/pay_icon/paystack.png')}}" style="width:150px;">
                      </label>
                    </div> 

                     <div class="form-check payment" style="height: 80px;">
                      <input class="form-check-input" type="radio" name="pay_type" id="rave" value="rave" style="margin-left:400px; height:40px;" onchange="pay_method()">
                      <label class="form-check-label" for="rave">
                        <img src="{{asset('public/front/img/pay_icon/rave.png')}}" style="width:150px;">
                      </label>
                    </div>  

                    <div class="form-check payment" style="height: 80px;">
                      <input class="form-check-input" type="radio" name="pay_type" id="razorpay" value="razorpay" style="margin-left:400px; height:40px;" onchange="pay_method()">
                      <label class="form-check-label" for="razorpay">
                        <img src="{{asset('public/front/img/pay_icon/razorpay.png')}}" style="width:150px;">
                      </label>
                    </div> 
                
                </div>
                <div class="modal-footer show_pay_link">
                  <button type="button" onclick="cod_pay()" class="btn d-flex w-50 text-center justify-content-center btn-primary">Submit</button>
                </div>
              </form>
        </div>
      </div>
</div>

<div class="modal" id="success_payment">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div id="loginmodel">
            <div class="part-form-main-box" >
             
              <div class="col-md-12 text-center">
                <img class="img-fluid mb-5" src="{{asset('public/front/img/thanks.png')}}" alt="404">
                <h1 class="mt-2 mb-2 text-success">Congratulations!</h1>
                <p class="mb-5">You have successfully placed your order</p>
                <a class="btn btn-primary btn-lg" href="{{route('my_account')}}">View Order :)</a>
                <a class="btn btn-primary btn-lg" href="{{url('/')}}">Continue Shopping :)</a>
              </div>
                 
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

@endsection

@section('script')

  <script>

  function pay_method(){
    var id = $("#order_id").val();
    
    var pay_type=$('input[name="pay_type"]:checked').val();
      if(pay_type == "cod")
      {   
         $(".show_pay_link").empty();
         $("#pay_form").attr("action","");
         $(".show_pay_link").append("<button onclick='cod_pay()' class='btn d-flex w-50 text-center justify-content-center btn-primary'>Submit</button>");
      }
      else if(pay_type == "braintree")
      {
        $(".show_pay_link").empty();

        $("#pay_form").attr("action","{{route('braintree-payment')}}");
        $(".show_pay_link").append("<button class='btn d-flex w-50 text-center justify-content-center btn-primary'>Pay</button>");
      }
  }

  function hide_add(){
    var status=$('input[name="add_name"]:checked').val();
    if(status == "pick_up")
      {
        $("#show_add").css("display", "none");
      }else if(status == "Home")
      {
        $("#show_add").show();
      }
  }
             
  function add_current_add(id){
    $.ajax({
           url: $("#front_path").val()+"/add_current_add"+"/"+id,
           method:"POST",
           data: {
                    "_token": "{{ csrf_token() }}",
                    'id': id,
                  },
           success: function(data)
           {

            if(data == 1){
              $(".text-black").removeClass("text-black");
              $(".icon").css({"color":"black"});
              $(".addresses-item").css({"border":"","color":""});
              $(".add_btn").css({"background-color":"#545b62","border":"#545b62"});
              $(".address-"+id).css({"border":"solid 1px #3ecf8e","color":"#3ecf8e"});
              $(".ajax-"+id).css({"background-color":"#3ecf8e","border":"#3ecf8e"});
              $(".icon-"+id).css({"color":"#3ecf8e"});
              $(".text-black-"+id).addClass("text-black");
             
            }
           }
         });
  }

  function add_cart(id){

    var Size=$('input[name="Size"]:checked').val();
    var Capacity=$('input[name="Capacity"]:checked').val();
    var Weight=$('input[name="Weight"]:checked').val();

    var labelSize=$('input[name="Size"]:checked').attr('data-id');
    var labelCapacity=$('input[name="Capacity"]:checked').attr('data-id');
    var labelWeight=$('input[name="Weight"]:checked').attr('data-id');

    var optionSize=$('input[name="Size"]:checked').data("action");
    var optionCapacity=$('input[name="Capacity"]:checked').data("action");
    var optionWeight=$('input[name="Weight"]:checked').data("action");

    $.ajax({
           url: $("#front_path").val()+"/add_cart"+"/"+id,
           method:"POST",
           data: {
                    "_token": "{{ csrf_token() }}",
                    'id': id,
                    'Size':Size,
                    'Capacity':Capacity,
                    'Weight':Weight,
                    'labelSize':labelSize,
                    'labelCapacity':labelCapacity,
                    'labelWeight':labelWeight,
                    'optionSize':optionSize,
                    'optionCapacity':optionCapacity,
                    'optionWeight':optionWeight,
                     },
           success: function(data)
           {

             if(data.is_veg == 1){
                $("#cart").append("<div class='gold-members p-2 border-bottom'><p class='text-gray mb-0 float-right ml-2'>₹"+data.price+"</p><span class='count-number float-right'><button type='button' id='sub1' class='sub1 btn btn-outline-secondary  btn-sm left dec'><i class='icofont-minus'></i></button><input class='count-number-input' type='text' value='1' readonly='' min='1' max='10' readonly=''><button type='button' id='add1' class='add1 btn btn-outline-secondary btn-sm right inc'><i class='icofont-plus' data-field='quantity' ></i></button> </span><div class='media'><div class='mr-2'><i class='icofont-ui-press text-success food-item'></i></div><div class='media-body'><p class='mt-1 mb-0 text-black'>"+data.name+"</p></div></div></div>");
             }else{
                $("#cart").append("<div class='gold-members p-2 border-bottom'><p class='text-gray mb-0 float-right ml-2'>₹"+data.price+"</p><span class='count-number float-right'><button type='button' id='sub1' class='sub1 btn btn-outline-secondary  btn-sm left dec'><i class='icofont-minus'></i></button><input class='count-number-input' type='text' value='1' readonly='' min='1' max='10' readonly=''><button type='button' id='add1' class='add1 btn btn-outline-secondary btn-sm right inc'><i class='icofont-plus' data-field='quantity'></i></button></span><div class='media'><div class='mr-2'><i class='icofont-ui-press text-danger food-item'></i></div><div class='media-body'><p class='mt-1 mb-0 text-black'>"+data.name+"</p></div></div></div>");
             } 
             setTimeout(function(){
                   location.reload();
              }, 10);
           }
      });
  }

  function discount(){
    var code = $("#coupon").val();
    var total = $("#total").val();
    var delivery_charges = $("#delivery_charges").val();
    $.ajax({
           url: $("#front_path").val()+"/add_discount",
           method:"POST",
           data: {
                    "_token": "{{ csrf_token() }}",
                    'code': code,
                    'total':total,
                    'delivery_charges':delivery_charges,
                     },
           success: function(response)
           {
            if(response){
              item=JSON.parse(response);
              
                 $("#discount").text(item.discount);
                 $("#total_amount").text("₹"+item.total_amount);
                 $("#pay_amount").text("₹"+item.pay_amount);
                 $(".pay").text("₹"+item.pay_amount);
                 $("#error").text(item.error);
                 $("#total_amount_in").val(item.total_amount);
                 $("#pay_in").val(item.pay_amount);
                 $("#discount_in").val(item.discount);
                
              }
           }
    });
  }

  function add_order(){
    var shipping_method = $('input[name="add_name"]:checked').val();
    var total = $('#total').val();
    var tax = $('#tax').val();
    var delivery = $('#delivery_charges').val();
    var dis = $('#discount_in').val();
    var dis_amount = $('#total_amount_in').val();
    var pay = $('#pay_in').val();
    var note = $('#note').val();
    var add_id = $('#add_id').val();
    var coupon = $('#coupon').val();
    var cart_res = $('#cart_res').val();

    $.ajax({
           url: $("#front_path").val()+"/add_order",
           method:"POST",
           data: {
                    "_token": "{{ csrf_token() }}",
                    'shipping_method': shipping_method,
                    'total': total,
                    'tax': tax,
                    'delivery': delivery,
                    'dis': dis,
                    'dis_amount': dis_amount,
                    'pay': pay,
                    'note': note,
                    'add_id': add_id,
                    'coupon': coupon,
                    'cart_res': cart_res,
                  },
           success: function(response)
           { 
            $("#order_id").val(response.id)
           }
    });
  }

  function cod_pay(){
    var id = $("#order_id").val();
    $.ajax({
           url: $("#front_path").val()+"/cod_payment",
           method:"POST",
           data: { 
            "_token": "{{ csrf_token() }}",
            'id': id,
           },
           success: function(data)
           {
              if(data == 1){
                  $('#pay_type').modal('toggle');
                  $('#success_payment').modal('show');
              }else{

              }
           }
         });
  }
  
  </script>
@endsection   