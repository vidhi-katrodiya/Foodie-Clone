<?php  //echo "<pre>";print_r($user);die();?>
@extends('front.layout')

@section('title')
{{__("messages.Restuarant")}}
@endsection

@section('content')

<style type="text/css">
  .owl-carousel .owl-item img{
    width: 56% !important;
    margin-left: 25px !important;
  }
  .img-fluid{
    width: 508px !important;
    height: 186px;
  }
</style>
 <section class="breadcrumb-osahan pt-5 pb-5 bg-dark position-relative text-center">
      <h1 class="text-white">Offers Near You</h1>
      <h6 class="text-white-50">Best deals at your favourite restaurants</h6>
    </section>
    <section class="section pt-5 pb-5 products-listing">
      <div class="container">
        <div class="row d-none-m">
          <div class="col-md-12">
            <div class="dropdown float-right">
              <a class="btn btn-outline-info dropdown-toggle btn-sm border-white-btn" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Sort by: <span class="text-theme">Distance</span> &nbsp;&nbsp; </a>
              <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 ">
                <a class="dropdown-item" href="{{route('listing_data',['type'=>'shortby_distance'])}}">Distance</a>
                <a class="dropdown-item" href="{{route('listing_data',['type'=>'no_of_offers'])}}">No Of Offers</a>
                <a class="dropdown-item"  href="{{route('listing_data',['type'=>'rating'])}}">Rating</a>
              </div>
            </div>
            <h4 class="font-weight-bold mt-0 mb-3">OFFERS <small class="h6 mb-0 ml-2">299 restaurants </small>
              <input type="hidden" name="type" id="data_type" value="{{$type}}">
               @if ($type == 'express_delivery_res' || $type == 'search_res')
                  <input type="hidden" name="lat" id="lat" value="{{$lat}}">
                  <input type="hidden" name="long" id="long" value="{{$lan}}">

                  @if ($type == 'search_res')
                      <input type="hidden" name="cat_id" id="cat_id" value="{{$cat_id}}">
                 
                  @else
                       <input type="hidden" name="cat_id" id="cat_id" value="">
                   
                  @endif
              @else
                   <input type="hidden" name="lat" id="lat" value="">
                  <input type="hidden" name="long" id="long" value="">
              @endif
              
            </h4>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="filters shadow-sm rounded bg-white mb-4">
              <div class="filters-header border-bottom pl-4 pr-4 pt-3 pb-3">
                <h5 class="m-0">Filter By</h5>
              </div>
              <div class="filters-body">
                <div id="accordion">
                  
                  <div class="filters-card border-bottom p-4">
                    <div class="filters-card-header" id="headingTwo">
                      <h6 class="mb-0">
                        <a href="#" class="btn-link" data-bs-toggle="collapse" data-bs-target="#collapsetwo" aria-expanded="true" aria-controls="collapsetwo"> All cuisines <i class="icofont-arrow-down float-right"></i>
                        </a>
                      </h6>
                    </div>
                    <div id="collapsetwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
                      <div class="filters-card-body card-shop-filters">
                        <form class="filters-search mb-3">
                          <div class="form-group">
                            <i class="icofont-search"></i>
                            <input type="text" class="form-control" placeholder="Start typing to search...">
                          </div>
                        </form>
                        <?php $i=1; ?>
                        @foreach($category as $ex_cat)
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" value="{{$ex_cat->id}}" class="custom-control-input common_selector fil_category" id="cb<?=$i?>">
                          <label class="custom-control-label" for="cb<?=$i?>">{{$ex_cat->cat_name}} <!-- <small class="text-black-50">156</small> -->
                          </label>
                          <?php $i++; ?>
                        </div>
                        @endforeach
                        <div class="mt-2">
                          <a href="#" class="link">See all</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="filters-card border-bottom p-4">
                    <div class="filters-card-header" id="headingCategory">
                      <h6 class="mb-0">
                        <a href="#" class="btn-link" data-bs-toggle="collapse" data-bs-target="#collapseFeature" aria-expanded="true" aria-controls="collapseFeature"> Feature <i class="icofont-arrow-down float-right"></i>
                        </a>
                      </h6>
                    </div>
                    <div id="collapseFeature" class="collapse" aria-labelledby="headingCategory" data-parent="#accordion">
                      <div class="filters-card-body card-shop-filters">
                        <div class="custom-control custom-radio">
                          <input type="radio" id="customRadio1" name="customRadio" value="0" class="custom-control-input common_selector fil_feature" >
                          <label class="custom-control-label" for="customRadio1">All</label>
                        </div>
                        <div class="custom-control custom-radio">
                          <input type="radio" id="customRadio2" name="customRadio" value="1" class="custom-control-input common_selector fil_feature">
                          <label class="custom-control-label" for="customRadio2">Free Delivery</label>
                        </div>
                        <div class="custom-control custom-radio">
                          <input type="radio" id="customRadio3" name="customRadio" value="2" class="custom-control-input common_selector fil_feature" >
                          <label class="custom-control-label" for="customRadio3">Coupons</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="filters-card border-bottom p-4">
                    <div class="filters-card-header" id="headingOffer">
                      <h6 class="mb-0">
                        <a href="#" class="btn-link" data-bs-toggle="collapse" data-bs-target="#collapseOffer" aria-expanded="true" aria-controls="collapseOffer"> Delivery time <i class="icofont-arrow-down float-right"></i>
                        </a>
                      </h6>
                    </div>
                    <div id="collapseOffer" class="collapse" aria-labelledby="headingOffer" data-parent="#accordion">
                      <div class="filters-card-body card-shop-filters">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" value="0" class="custom-control-input common_selector fil_del_time" id="cb19">
                          <label class="custom-control-label" for="cb19">Any Time </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" value="25" class="custom-control-input common_selector fil_del_time" id="cb20">
                          <label class="custom-control-label" for="cb20">25 min </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" value="30" class="custom-control-input common_selector fil_del_time" id="cb36">
                          <label class="custom-control-label" for="cb36">30 min </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" value="40" class="custom-control-input common_selector fil_del_time" id="cb47">
                          <label class="custom-control-label" for="cb47">40 min </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" value="50" class="custom-control-input common_selector fil_del_time" id="cb58">
                          <label class="custom-control-label" for="cb58">50 min </label>
                        </div>
                        <div class="mt-2">
                          <a href="#" class="link">See all</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-9">
             <div class="owl-carousel owl-carousel-category owl-theme list-cate-page mb-4">
              @foreach($category as $ex_category)
                <div class="item">
                  <div class="osahan-category-item">
                    <a href="#">
                      <img class="img-fluid" src="{{url('public/upload/category/')}}/{{$ex_category->image}}" alt="">
                      <h6>{{$ex_category->cat_name}}</h6>
                      <p>{{$ex_category->delivery_charges}}</p>
                    </a>
                  </div>
                </div>   
              @endforeach
            
            </div>
            <div class="row filter_data">
              
              
              
            </div>
          </div>
        </div>
      </div>
      </div>
    </section>
    
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function(){
  filter_data();
});
</script>
@endsection   
  
<!-- 
@section('script')
<script type="text/javascript">


$(document).ready(function(){

    filter_data();

    function filter_data()
    {
        $('.filter_data').html('<div id="loading" style="" ></div>');
        
        var type=$('#data_type').val();
        var lat=$('#lat').val();
        var long=$('#long').val();
        var cat_id=$('#cat_id').val();
        var fil_feature = get_filter('fil_feature');
        var fil_del_time = get_filter('fil_del_time');
        var fil_category = get_filter('fil_category');
        var token= $("#token").val();
        $.ajax({
            url: $("#front_path").val()+"/filter_data" ,
            method:"POST",
            data:{"_token": token,"type":type,"lat":lat,"cat_id":cat_id,"long":long,"fil_category":fil_category, "fil_del_time":fil_del_time,"fil_feature":fil_feature},
            success:function(data){
                $('.filter_data').html(data);
            }
        });
    }

    function get_filter(class_name)
    {
        var filter = [];
        $('.'+class_name+':checked').each(function(){
            filter.push($(this).val());
        });
        return filter;
    }

    $('.common_selector').click(function(){
        filter_data();
    });

});
</script> -->
@endsection   
  