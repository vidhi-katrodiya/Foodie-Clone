
@extends('front.layout')

@section('title')
  {{__("messages.Dashboard")}}
@endsection

@section('content')
<style>
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable{
        background-color: #dd646e!important;
    }
</style>
<style type="text/css">
  
   .img-fluid{
    width: 508px !important;
    height: 186px;
  }
  
</style>
<div class="homepage-header">

    <div class="overlay"></div>

    <section class="pt-5 pb-5 homepage-search-block position-relative">
        <div class="banner-overlay"></div>
        <div class="container">
          <div class="row d-flex align-items-center py-lg-4">
            <div class="col-lg-8 mx-auto">
              <div class="homepage-search-title text-center">
                <h1 class="mb-2 display-4 text-shadow text-white font-weight-normal">
                  <span class="font-weight-bold">Discover the best food & drinks in India IN </span>
                </h1>
                <h5 class="mb-5 text-shadow text-white-50 font-weight-normal">Lists of top restaurants, cafes, pubs, and bars in Melbourne, based on trends</h5>
              </div>
              <div class="homepage-search-form">
                <form class="form-noborder" action="{{route('search_res')}}" method="post">
                    
                    <input type="hidden" value="{{ csrf_token() }}" name="_token">   
                  <div class="form-row">
                    <div class="col-lg-3 col-md-3 col-sm-12 form-group">
                      <div class="location-dropdown">
                        <i class="icofont-location-arrow"></i>
                        <select class="select2 custom-select form-control-lg" name="cat_id" value="{{old('cat_id')}}"   id="selUser">
                            <option disabled selected="" value="">Quick Search</option>
                                @foreach($data as $ex_category)
                                    <option value="{{ $ex_category->id }}" {{($ex_category->id == old('category_id')) ? 'selected' : ''}}>{{ $ex_category->cat_name }}
                                    </option> 
                                @endforeach
                        </select>
                      
                      </div>
                    </div>
                    <div id="map" ></div>
                <div class="col-lg-7 col-md-7 col-sm-12 form-group"  id="addressorder" >
                    <input type="text" name="address" id="address"  class="location-input" placeholder="Address" style="border-radius: 8px;width: -webkit-fill-available;font-size: 15px;height: 50px;border: none;padding-left: 10px;;">
                    </div>
                    <input type="hidden" name="lat" id="lat">
                    <input type="hidden" name="lon" id="lng">
                    <input type="hidden" name="latlang" id="latlang">
                     <input type="hidden" name="city" id="city">
                    <input type="hidden" name="state" id="state">
                    <input type="hidden" name="area" id="area">
                    <input type="hidden" name="pincode" id="pincode">
                    <input type="hidden" name="country" id="country">
                    <div class="col-lg-2 col-md-2 col-sm-12 form-group">
                      <button  type="submit" class="btn btn-primary btn-block btn-lg btn-gradient">Search</button>
                    </div>
                  </div>
                </form>
              </div>
              <h6 class="mt-4 text-shadow text-white font-weight-normal">E.g. Beverages, Pizzas, Chinese, Bakery, Indian...</h6>
              <div class="owl-carousel owl-carousel-category owl-theme" >
                @foreach($data as $category)
                  <div class="item">
                    <div class="osahan-category-item">
                      <a href="#">
                        <img class="img-fluid" src="{{url('public/upload/category/')}}/{{$category->image}}" alt="" style="width: 55% !important; margin: 15px 10px 15px 20px !important;">
                      <h6>{{$category->cat_name}}</h6>
                      <p>{{$category->delivery_charges}}</p>
                      </a>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
    </section>
    </div>
    <section class="section pt-5 pb-5 bg-white homepage-add-section">
      <div class="container">
       
        <div class="row">
          <div class="col-md-3 col-6">
            <div class="products-box">
              <a href="{{route('listing_data',['type'=>'short_offer'])}}">
                <img alt="" src="{{asset('public/front/img/pro1.jpg')}}" class="img-fluid rounded" style="height: auto !important; ">
              </a>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="products-box">
              <a href="{{route('listing_data',['type'=>'all_time_fav_res'])}}">
                <img alt="" style="height: auto !important; " src="{{asset('public/front/img/pro2.jpg')}}" class="img-fluid rounded">
              </a>
            </div>
          </div>
          <div class="col-md-3 col-6" >
            <div class="products-box" onclick="express_delivery_res()">
              <a href="{{url('listing_data_data',array('type'=>'all','is_filter'=>'0'))}}" id="express_del">
                <img alt="" style="height: auto !important; " src="{{asset('public/front/img/pro3.jpg')}}" class="img-fluid rounded">
              </a>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="products-box">
              <a href="{{route('listing_data',['type'=>'rating'])}}">
                <img alt="" style="height: auto !important; " src="{{asset('public/front/img/pro4.jpg')}}" class="img-fluid rounded">
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="section pt-5 pb-5 products-section">
      <div class="container">
        <div class="section-header text-center">
          <h2>Popular Brands</h2>
          <p>Top restaurants, cafes, pubs, and bars in Ludhiana, based on trends</p>
          <span class="line"></span>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="owl-carousel owl-carousel-four owl-theme" >
              @foreach($restaurant as $res_val)
                <div class="item">
                  <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
                    <div class="list-card-image">
                      <div class="star position-absolute">
                        <span class="badge badge-success">
                          @if ($res_val->review_count == 0)
                              <i class="icofont-star"></i> {{round($res_val->rating,1)}} ({{$res_val->review_count}}) </span>
                          @else
                              <i class="icofont-star"></i> {{round($res_val->rating,1)}} ({{$res_val->review_count}}+) </span>
                          @endif
                      </div>
                       <input type="hidden" required name="user_id" id="user_id" value="{{Auth::user()?Auth::user()->id:''}}">
                        <input type="hidden" required name="res_id" id="res_id" value="{{$res_val->id}}">
                      <div class="favourite-heart text-danger position-absolute">
                          @if($res_val->is_fav =="1")
                          <i class="icofont-heart add_fav_icon  fav_icon_{{$res_val->id}}"  style="color: red;" onclick="add_favourite(<?= $res_val->id ?>)" ></i>
                          @else
                              @if(Auth::id())     
                                <i class="icofont-heart delete_fav_icon fav_icon_{{$res_val->id}}"   style="color: gray;" onclick="add_favourite(<?= $res_val->id ?>)" ></i>
                              @else 
                                
                              <a data-bs-toggle="modal" data-bs-target="#myModal"><i class="icofont-heart delete_fav_icon fav_icon_{{$res_val->id}}"   style="color: gray;"></i></a>
                              @endif  
                        
                          @endif
                      </div>
                      <div class="member-plan position-absolute">
                       
                      </div>
                      <a href="{{url('res_detail/')}}/{{$res_val->id}}">
                          <?php $res_image='public/upload/restaurant/'.$res_val->res_image?>
                       @if(file_exists($res_image) && $res_val->res_image != null)
                        <img class="img-fluid item-img" src="{{url('public/upload/restaurant/')}}/{{$res_val->res_image}}" alt="">
                        
                        @else
                            <img class="img-fluid item-img" src="{{url('public/upload/restaurant/restaurant.jpg')}}" alt="">
                        @endif
                       
                      </a>
                    </div>
                    <div class="p-3 position-relative">
                      <div class="list-card-body">
                        <h6 class="mb-1">
                          <a class="text-black">{{$res_val->first_name}}</a>
                        </h6>
                        <p class="text-gray mb-3">
                            <?php 
                          if(!empty($res_val->access_cat))
                          { 
                            $cat_name = implode(" • ", $res_val->access_cat);
                            if(strlen($cat_name) > 38){
                                echo $str = substr($cat_name, 0, 38) . '...';
                            }else{
                                 echo $cat_name;
                            }
                          }
                          else
                          {
                              echo " • • • ";
                          }
                          ?>
                        </p>
                        <p class="text-gray mb-3 time">
                          <span class="bg-light text-dark rounded-sm pl-2 pb-1 pt-1 pr-2">
                            <i class="icofont-wall-clock"></i>{{date("i", strtotime($res_val->delivery_time))}} min </span>
                          <span class="float-right text-black-50"> ₹{{$res_val->two_person_cost}} FOR TWO </span>
                        </p>
                      </div>
                      <div class="list-card-badge">
                        @if($res_val->offer != "")
                           <span class="badge badge-success">OFFER</span>
                            @if($res_val->offer->discount_type =='1')
                               <small>{{$res_val->offer->value}}% off | Use Coupon {{$res_val->offer->code}}</small>
                            @else
                                 <small>₹{{$res_val->offer->value}} off | Use Coupon {{$res_val->offer->code}}</small>
                            @endif
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </section>

  

    

   <!--  <section class="section pt-5 pb-5 bg-white becomemember-section border-bottom">
      <div class="container">
        <div class="section-header text-center white-text">
          <h2>Become a Member</h2>
          <p>Lorem Ipsum is simply dummy text of</p>
          <span class="line"></span>
        </div>
        <div class="row">
          <div class="col-sm-12 text-center">
            <a href="register.html" class="btn btn-success btn-lg"> Create an Account <i class="fa fa-chevron-circle-right"></i>
            </a>
          </div>
        </div>
      </div>
    </section> -->

   
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script> 

<script type="text/javascript">

  function express_delivery_res()
  {
    var lat= $("#lat").val();
    var lan= $("#lng").val();
    if(lat ==" " || lan==" ")
    {
        var url="https://customise.freaktemplate.com/foodieclone/listing_data/all";
    }
    else
    {
        var url="https://customise.freaktemplate.com/foodieclone/express_delivery_res/"+lat+"/"+lan+"";
    }
    
    $("#express_del").attr("href", url);
  }
  
  
  
  
  $(document).ready(function(){
 
    // Initialize select2
    $("#selUser").select2();

    // Read selected option
    $('#but_read').click(function(){
        var username = $('#selUser option:selected').text();
        var userid = $('#selUser').val();

    });
});
</script>
@endsection   
  
