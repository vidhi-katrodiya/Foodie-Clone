  @extends('front.layout')

  @section('title')
    {{__("message.restuarnt detail")}}
  @endsection

  @section('content')
  <style>
    .owl-carousel{
      display: flex !important;
    }
    .mall-category-item img{
      height: 60px;
      width: 100%;
    }

  </style>
    <section class="restaurant-detailed-banner">
        <div class="text-center">
          <img class="img-fluid cover" src="{{url('public/front/img/mall-dedicated-banner.png')}}">
        </div>
        <div class="restaurant-detailed-header">
          <div class="container">
            <div class="row d-flex align-items-end">
              <div class="col-md-8">
                <div class="restaurant-detailed-header-left">
                  <img class="img-fluid mr-3 float-left" alt="osahan" src="{{url('public/front/img/1.jpg')}}">
                  <h2 class="text-white">{{$user->first_name}}</h2>
                  <p class="text-white mb-1">
                    <i class="icofont-location-pin"></i>{{$user->address}}<span class="badge badge-success">OPEN</span>
                  </p>
                  <p class="text-white mb-0">
                    <i class="icofont-food-cart"></i>  <?php echo $cat_str=implode(" • ", $user->access_cat);?>
                  </p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="restaurant-detailed-header-right text-right">
                  <button class="btn btn-success" type="button">
                    <i class="icofont-clock-time"></i> {{date("i", strtotime($user->delivery_time))}} Minutes </button>
                  <h6 class="text-white mb-0 restaurant-detailed-ratings">
                    <span class="generator-bg rounded text-white">
                      <i class="icofont-star"></i> <?php echo round($user->rating,1)." Ratings"; ?> </span> {{$total_rat}} Ratings <i class="ml-3 icofont-speech-comments"></i> {{$user->review_count}} reviews
                  </h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      </section>

      <section class="offer-dedicated-nav bg-white border-top-0 shadow-sm">
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <span class="restaurant-detailed-action-btn float-right">
                <input type="hidden" required name="user_id" id="user_id" value="{{Auth::user()?Auth::user()->id:''}}">
                <input type="hidden" required name="res_id" id="res_id" value="{{$user->id}}">
                @if($user->is_fav =="1")
                    <button class="btn btn-light btn-sm border-light-btn" type="button" onclick="add_favourite(<?= $user->id ?>)" >
                      <i class="icofont-heart fav_icon_{{$user->id}}"  style="color: red;"></i> Mark as Favourite 
                    </button>
                   
                @else
                    @if(Auth::id())
                      <button class="btn btn-light btn-sm border-light-btn" type="button" onclick="add_favourite(<?= $user->id ?>)" >
                        <i class="icofont-heart fav_icon_{{$user->id}}"  style="color: gray;"></i> Mark as Favourite 
                      </button>     
                      
                    @else 
                    
                    <button class="btn btn-light btn-sm border-light-btn" type="button" data-bs-toggle="modal" data-bs-target="#myModal" >
                        <i class="icofont-heart fav_icon_{{$user->id}}"  style="color: gray;"></i> Mark as Favourite 
                    </button> 
                    
                    @endif  
              
                @endif

               
                
                <a  class="btn btn-outline-danger btn-sm" type="button">
                  <i class="icofont-sale-discount"></i> OFFERS </a>
              </span>
              <ul class="nav" id="pills-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="pills-order-online-tab" data-bs-toggle="pill" href="#pills-order-online" role="tab" aria-controls="pills-order-online" aria-selected="true">Order Online</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="pills-restaurant-info-tab" data-bs-toggle="pill" href="#pills-restaurant-info" role="tab" aria-controls="pills-restaurant-info" aria-selected="false">Restaurant Info</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="pills-book-tab" data-bs-toggle="pill" href="#pills-book" role="tab" aria-controls="pills-book" aria-selected="false">Book A Table</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="pills-reviews-tab" data-bs-toggle="pill" href="#pills-reviews" role="tab" aria-controls="pills-reviews" aria-selected="false">Ratings & Reviews</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <section class="offer-dedicated-body pt-2 pb-2 mt-4 mb-4">
        <div class="container">
          <div class="row">
            <div class="col-md-8">
              <div class="offer-dedicated-body-left">
                <div class="tab-content" id="pills-tabContent">

                  <div class="tab-pane fade show active" id="pills-order-online" role="tabpanel" aria-labelledby="pills-order-online-tab">
                    <div id="#menu" class="bg-white rounded shadow-sm p-4 mb-4 explore-outlets">
                        <h5 class="mb-4">Recommended</h5>
                        <form class="explore-outlets-search mb-4 rounded overflow-hidden border">
                           <div class="input-group">
                              <input type="text" placeholder="Search for dishes..." class="form-control border-0">
                              <div class="input-group-append">
                                 <button type="button" class="btn btn-primary">
                                 <i class="icofont-search"></i>
                                 </button>
                              </div>
                           </div>
                        </form>
                        <h6 class="mb-3">Best Sellers</h6>
                        <div class="owl-carousel owl-theme owl-carousel-five offers-interested-carousel mb-3">
                           @foreach($pro as $product)
                            <div class="col-md-4 col-sm-6 mb-4">
                              <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">

                                <div class="list-card-image">
                                  <div class="star position-absolute">
                                      <span class="badge badge-success">
                                        <i class=""></i> Best Sellers
                                      </span>
                                  </div>
                                  <a href="#">
                                    <img src="{{url('public/upload/product/')}}/{{$product->basic_image}}" style="height:146px; width:100%;" class="img-fluid item-img">
                                  </a>
                                </div>

                                <div class="p-3 position-relative">
                                  <div class="list-card-body">
                                    <h6 class="mb-1">
                                      <a href="#" class="text-black">{{$product->name}}</a>
                                    </h6>
                                    <p class="text-gray mb-2">North Indian • Indian</p>
                                    <p class="text-gray time mb-0">
                                      <a class="btn btn-link btn-sm text-black" href="#" style="background-color: #3ecf8e !important; color:white;">₹{{$product->selling_price}}
                                      </a>
                                      <span class="float-right">
                                         @if(Auth::id())     
                                          @php
                                            $cat = DB::table('product_options')->where('product_id',$product->id)->get();
                                            $count_opt = count($cat);
                                          @endphp

                                          @if($count_opt>0)
                                            <a  id="add" class="btn btn-outline-secondary btn-sm" onclick="product_option({{$product->id}})" type="button" data-bs-toggle="modal" data-bs-target="#modal" style="color:#dd646e; border: 1px solid #dd646e;" >  ADD </a>
                                          @else
                                            <a  id="add" class="btn btn-outline-secondary btn-sm" onclick="add_out_opt({{$product->id}})" type="button" style="color:#dd646e; border: 1px solid #dd646e;">ADD </a>
                                          @endif
                                         
                                        @else 
                                           <a data-bs-toggle="modal" data-bs-target="#add_cart" class="btn btn-primary" type="submit" style="color:white;">ADD</a>
                                          
                                        @endif  
                                        
                                      </span>
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </div>
                             @endforeach
                        </div>
                    </div>
                     
                    @if(Auth::id())   
                      
                      @foreach($res_cat as $cat) 
                       <div class="row">
                         @php
                          $data = DB::table('products')->where('category',$cat->id)->get();
                          $count = count($data);
                         @endphp
                        <h5 class="mb-4 mt-3 col-md-12">{{$cat->cat_name}}<small class="h6 text-black-50"> @php echo " ".$count; @endphp ITEMS</small>
                        </h5> 
                        <div class="col-md-12">
                          <div class="bg-white rounded border shadow-sm mb-4">
                            @foreach($data as $product)

                              @php 
                                $cart_data = DB::table('tbcart')
                                            ->where('user_id',Auth::id())
                                            ->where('product_id',$product->id)
                                            ->get();
                                            $count = count($cart_data);
                              @endphp

                              <div class="gold-members p-3">
                                <span class="count-number float-right">
                                  
                                    <button type="button" id="sub" class="sub btn btn-outline-secondary btn-sm left dec" data-bs-toggle="modal" data-bs-target="#modal1"><i class="icofont-minus"></i>
                                    </button>

                                  @if($count)
                                      <input class="count-number-input" type="text" value="{{$count}}" readonly="" min="1" max="10" readonly="">
                                  @else 
                                      <input class="count-number-input" type="text" value="0" readonly="" min="1" max="10" readonly="">
                                  @endif 

                                  @php
                                    $cat = DB::table('product_options')->where('product_id',$product->id)->get();
                                    $count_opt = count($cat);
                                  @endphp

                                  @if($count_opt>0)
                                    <a  id="add" class="add btn btn-outline-secondary btn-sm right inc" onclick="product_option({{$product->id}})" type="button" data-bs-toggle="modal" data-bs-target="#modal" ><i class="icofont-plus"  data-field="quantity"></i></a>
                                  @else
                                    <a  id="add" class="add btn btn-outline-secondary btn-sm right inc" onclick="add_out_opt({{$product->id}})" type="button" ><i class="icofont-plus"  data-field="quantity"></i></a>
                                  @endif
                                </span>
                                <div class="media">
                                  <div class="mr-3">
                                      @if($product->is_veg == 0)
                                        <i class="icofont-ui-press text-danger food-item"></i>
                                      @elseif($product->is_veg == 1)
                                        <i class="icofont-ui-press text-success food-item"></i>
                                        @else
                                        <span class="badge badge-success"></span>
                                      @endif
                                  </div>
                                  
                                  <div class="media-body">
                                    <h6 class="mb-1">{{$product->name}}
                                      @if($product->is_veg == 0)
                                        <span class="badge badge-danger">Non Veg</span>
                                      @elseif($product->is_veg == 1)
                                        <span class="badge badge-success">Pure Veg</span>
                                      @else
                                        <span class="badge badge-success"></span>
                                      @endif
                                      
                                    </h6>
                                    <p class="text-gray mb-0"><?php echo "₹".number_format($product->price,2); ?></p>
                                  </div>
                                </div>
                              </div>
                              
                              @endforeach
                            </div>
                        </div>
                        </div>
                      @endforeach
                    @else
                      @foreach($res_cat as $cat) 
                        <div class="row">
                           @php
                            $data = DB::table('products')->where('category',$cat->id)->get();
                            $count = count($data);
                           @endphp
                          <h5 class="mb-4 mt-3 col-md-12">{{$cat->cat_name}}<small class="h6 text-black-50"> @php echo " ".$count; @endphp ITEMS</small>
                          </h5>
                          <div class="col-md-12">
                            <div class="bg-white rounded border shadow-sm mb-4">
                              @foreach($data as $product)
                                <div class="gold-members p-3">
                                  <span class="count-number float-right">
                                    <button type="button" id="sub" class="sub btn btn-outline-secondary btn-sm left dec" data-bs-toggle="modal" data-bs-target="#modal1"><i class="icofont-minus"></i>
                                    </button>

                                    <input class="count-number-input" type="text" value="0" readonly="" min="1" max="10" readonly="">
                                      
                                    <a  id="add" class="add btn btn-outline-secondary btn-sm right inc" onclick="product_option({{$product->id}})" type="button" data-bs-toggle="modal" data-bs-target="#modal" ><i class="icofont-plus"  data-field="quantity"></i></a>

                                  </span>
                                  <div class="media">
                                    <div class="mr-3">
                                        @if($product->is_veg == 0)
                                          <i class="icofont-ui-press text-danger food-item"></i>
                                        @elseif($product->is_veg == 1)
                                          <i class="icofont-ui-press text-success food-item"></i>
                                          @else
                                          <span class="badge badge-success"></span>
                                        @endif
                                    </div>
                                    
                                    <div class="media-body">
                                      <h6 class="mb-1">{{$product->name}}
                                        @if($product->is_veg == 0)
                                          <span class="badge badge-danger">Non Veg</span>
                                        @elseif($product->is_veg == 1)
                                          <span class="badge badge-success">Pure Veg</span>
                                        @else
                                          <span class="badge badge-success"></span>
                                        @endif
                                        
                                      </h6>
                                      <p class="text-gray mb-0"><<?php echo "₹".number_format($product->price,2); ?></p>
                                    </div>
                                  </div>
                                </div>
                                
                                @endforeach
                              </div>
                          </div>
                        </div>
                      @endforeach
                    @endif

                    </div>

                  <div class="tab-pane fade" id="pills-restaurant-info" role="tabpanel" aria-labelledby="pills-restaurant-info-tab">
                     <div id="restaurant-info" class="bg-white rounded shadow-sm p-4 mb-4">
                        <div class="address-map float-right ml-5">
                           <div class="mapouter">
                              <div class="gmap_canvas">
                                <iframe width="300" height="170" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q={{$user->first_name}}&amp;t=&amp;z=9&amp;ie=UTF8&amp;iwloc=&amp;output=embed"></iframe>
                                
                              </div>
                           </div>
                        </div>
                        <h5 class="mb-4">Restaurant Info</h5>
                        <p class="mb-3">{{$user->address}}
                        </p>
                        <p class="mb-2 text-black"><i class="icofont-phone-circle text-primary mr-2"></i>{{$user->phone}}</p>
                        <p class="mb-2 text-black"><i class="icofont-email text-primary mr-2"></i> <a href="https://askbootstrap.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="1b727a7674687a737a755b7c767a727735787476">[{{$user->email}}]</a></p>
                        <p class="mb-2 text-black" ><i class="icofont-clock-time text-primary mr-2" ></i>
                          <?php
                          
                            $data=(explode("to",$user->res_time));
                            
                            date_default_timezone_set('Asia/Calcutta'); 
                            $time = date("H:i");
                            
                           $from = date("H:i",strtotime($data[0]));
                           $to = date("H:i",strtotime($data[1]));

                            if($from <= $time and $to >= $time){
                              ?>  
                                Today {{$user->res_time}}
                                <span class="badge badge-success"> OPEN NOW </span>
                              <?php
                            }else{
                              ?>
                                Today {{$user->res_time}}
                                <span class="badge badge-danger"> CLOSE NOW </span>
                              <?php
                            }

                          ?>
                        </p>
                        <hr class="clearfix">
                        
                        <hr class="clearfix">
                        <h5 class="mt-4 mb-4">Categories</h5>
                        <p class="mb-3"><!-- <?php echo $cat_str=implode(" • ", $user->access_cat);?> --></p>
                        <div class="border-btn-main mb-4">
                          @foreach($user->access_cat as $val)
                            <a class="border-btn text-success mr-2" href="#"><i class="icofont-check-circled"></i>{{$val}} </a>
                          @endforeach
                        </div>
                     </div>
                  </div>

                  <div class="tab-pane fade" id="pills-book" role="tabpanel" aria-labelledby="pills-book-tab">
                    <div id="book-a-table" class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                      <h5 class="mb-4">Book A Table</h5>
                      <form  id="basicform" method="post" action="{{route('book_table')}}" enctype="multipart/form-data">
                      @csrf
                        <input class="form-control" type="hidden" name="res_id" value="{{$user->id}}">
                          <div class="row">
                            <div class="col-sm-12">
                               <div class="form-group">
                                  <label>Full Name</label>
                                  <input class="form-control" type="text" name="user_name" placeholder="Enter Full Name">
                               </div> 
                                @if ($errors->has('user_name'))
                                  <span class="text-danger">{{ $errors->first('user_name') }}</span>
                                @endif
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-6">
                               <div class="form-group">
                                  <label>Email Address</label>
                                  <input class="form-control" type="text" name="email" placeholder="Enter Email address">
                               </div>
                               @if ($errors->has('email'))
                                  <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                         
                            <div class="col-sm-6">
                               <div class="form-group">
                                  <label>Mobile number</label>
                                  <input class="form-control" type="text" name="phone_no" placeholder="Enter Mobile number">
                               </div>
                               @if ($errors->has('phone_no'))
                                  <span class="text-danger">{{ $errors->first('phone_no') }}</span>
                                @endif
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-6">
                               <div class="form-group">
                                  <label> Book Date </label>
                                  <input class="form-control" name="book_date" type="date" placeholder="Enter Date ">
                               </div>
                               @if ($errors->has('book_date'))
                                  <span class="text-danger">{{ $errors->first('book_date') }}</span>
                                @endif
                             </div>
                              <div class="col-sm-6">
                                 <div class="form-group">
                                    <label>Book Time</label>
                                    <input class="form-control" name="book_time" type="time" placeholder="Enter Time">
                                 </div>
                                 @if ($errors->has('book_time'))
                                  <span class="text-danger">{{ $errors->first('book_time') }}</span>
                                @endif
                               </div>
                            </div>

                            @if(Auth::id())     
                              <div class="form-group text-right">
                                <button class="btn btn-primary" type="submit"> Submit </button>
                              </div>
                            @else 
                              <div class="form-group text-right">
                                <a data-bs-toggle="modal" data-bs-target="#book" class="btn btn-primary" type="submit" style="color:white;">Submit</a>
                              </div>
                            @endif  
                          
                        </form>
                      </div>
                  </div>

                  <div class="tab-pane fade" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
                    

                    <div class="bg-white rounded shadow-sm p-4 mb-4 clearfix graph-star-rating">
                      <h5 class="mb-0 mb-4">Ratings and Reviews</h5>

                      <div class="graph-star-rating-header">
                      @if(count($review)>0)
                        @php
                          if($user->rating >= 1 && $user->rating < 2)
                                {
                                    $s1="text-warning";
                                    $s2="";
                                    $s3="";
                                    $s4="";
                                    $s5="";
                                }
                                 if($user->rating >= 2 && $user->rating < 3)
                                {
                                    $s1="text-warning";
                                    $s2="text-warning";
                                    $s3="";
                                    $s4="";
                                    $s5="";
                                }
                                if($user->rating >= 3 && $user->rating < 4)
                                {
                                    $s1="text-warning";
                                    $s2="text-warning";
                                    $s3="text-warning";
                                    $s4="";
                                    $s5="";
                                }
                                if($user->rating >= 4 && $user->rating < 5)
                                {
                                    $s1="text-warning";
                                    $s2="text-warning";
                                    $s3="text-warning";
                                    $s4="text-warning";
                                    $s5="";
                                }
                                if($user->rating == 5)
                                {
                                    $s1="text-warning";
                                    $s2="text-warning";
                                    $s3="text-warning";
                                    $s4="text-warning";
                                    $s5="text-warning";
                                }
                                if(empty($user->rating))
                                {
                                    $s1="text-warning";
                                    $s2="";
                                    $s3="";
                                    $s4="";
                                    $s5="";
                                }
                        @endphp
                        <span>
                            <i class="fa fa-star star-light submit mr-1 {{$s1}}" id="submit_1" data-rating="1" style="color:gray;"></i>
                            <i class="fa fa-star star-light submit mr-1 {{$s2}}" id="submit_2" data-rating="2" style="color:gray;"></i>
                            <i class="fa fa-star star-light submit mr-1 {{$s3}}" id="submit_3" data-rating="3" style="color:gray;"></i>
                            <i class="fa fa-star star-light submit mr-1 {{$s4}}" id="submit_4" data-rating="4" style="color:gray;"></i>
                            <i class="fa fa-star star-light submit mr-1 {{$s5}}" id="submit_5" data-rating="5" style="color:gray;"></i>
                        </span>
                         <p class="text-black mb-4 mt-2">Rated <?php echo round($user->rating,2)." Ratings"; ?> out of 5</p>
                      </div>
                      @else
                       <span>
                            <i class="fa fa-star star-light submit mr-1" id="submit_1" data-rating="1" style="color:gray;"></i>
                            <i class="fa fa-star star-light submit mr-1" id="submit_2" data-rating="2" style="color:gray;"></i>
                            <i class="fa fa-star star-light submit mr-1" id="submit_3" data-rating="3" style="color:gray;"></i>
                            <i class="fa fa-star star-light submit mr-1" id="submit_4" data-rating="4" style="color:gray;"></i>
                            <i class="fa fa-star star-light submit mr-1" id="submit_5" data-rating="5" style="color:gray;"></i>
                        </span>
                         <p class="text-black mb-4 mt-2">Rated 0 Ratings out of 5</p>
                      </div>
                      @endif
                      
                      <div class="graph-star-rating-body">
                          @if(count($review)>0) 
                             <div class="rating-list">
                                <div class="rating-list-left text-black">
                                   5 Star
                                </div>
                                <div class="rating-list-center">
                                  @php
                                        $data = DB::table('reviews')->where('ratting',5)->where('res_id',$user->id)->get();
                                        $count = count($data);
                                        $fat = (100*($count)/$user->rating) ; 
                                        
                                  @endphp
                                   <div class="progress">
                                      <div style="width: <?php echo round($fat,2)."%"; ?>" aria-valuemax="5" aria-valuemin="0" aria-valuenow="5" role="progressbar" class="progress-bar bg-primary">
                                         <span class="sr-only">80% Complete (danger)</span>
                                      </div>
                                   </div>
                                </div>
                                <div class="rating-list-right text-black">
                                    <?php echo round($fat,2)."%"; ?>
                                </div>
                             </div>
                             <div class="rating-list">
                                <div class="rating-list-left text-black">
                                   4 Star
                                </div>
                                <div class="rating-list-center">
                                  @php
                                        $data = DB::table('reviews')->where('ratting',4)->where('res_id',$user->id)->get();
                                        $count = count($data);
                                        $fat = (100*($count)/$user->rating) ; 
                                        
                                  @endphp
                                   <div class="progress">
                                      <div style="width: <?php echo round($fat,2)."%"; ?>" aria-valuemax="5" aria-valuemin="0" aria-valuenow="5" role="progressbar" class="progress-bar bg-primary">
                                         <span class="sr-only">80% Complete (danger)</span>
                                      </div>
                                   </div>
                                </div>
                                <div class="rating-list-right text-black">
                                    <?php echo round($fat,2)."%"; ?>
                                </div>
                             </div>
                             <div class="rating-list">
                                <div class="rating-list-left text-black">
                                   3 Star
                                </div>
                                <div class="rating-list-center">
                                  @php
                                        $data = DB::table('reviews')->where('ratting',3)->where('res_id',$user->id)->get();
                                        $count = count($data);
                                        $fat = (100*($count)/$user->rating) ; 
                                        
                                  @endphp
                                   <div class="progress">
                                      <div style="width: <?php echo round($fat,2)."%"; ?>" aria-valuemax="5" aria-valuemin="0" aria-valuenow="5" role="progressbar" class="progress-bar bg-primary">
                                         <span class="sr-only">80% Complete (danger)</span>
                                      </div>
                                   </div>
                                </div>
                                <div class="rating-list-right text-black">
                                    <?php echo round($fat,2)."%"; ?>
                                </div>
                             </div>
                             <div class="rating-list">
                                <div class="rating-list-left text-black">
                                   2 Star
                                </div>
                                <div class="rating-list-center">
                                  @php
                                        $data = DB::table('reviews')->where('ratting',2)->where('res_id',$user->id)->get();
                                        $count = count($data);
                                        $fat = (100*($count)/$user->rating) ; 
                                        
                                  @endphp
                                   <div class="progress">
                                      <div style="width: <?php echo round($fat,2)."%"; ?>" aria-valuemax="5" aria-valuemin="0" aria-valuenow="5" role="progressbar" class="progress-bar bg-primary">
                                         <span class="sr-only">80% Complete (danger)</span>
                                      </div>
                                   </div>
                                </div>
                                <div class="rating-list-right text-black">
                                    <?php echo round($fat,2)."%"; ?>
                                </div>
                             </div>
                          @endif
                      </div>
                    </div>

                    <div class="bg-white rounded shadow-sm p-4 mb-4 restaurant-detailed-ratings-and-reviews">
                        <h5 class="mb-1">All Ratings and Reviews</h5>
                          @if(count($review)>0)
                            @foreach($review as $val_review)
                            @php
                              $data_review = DB::table('users')->where('id',$val_review->user_id)->first();
                            @endphp
                            
                            @if($data_review)
                            <div class="reviews-members pt-4 pb-4">
                               <div class="media">
                                   
                                  @if($data_review->profile_pic == "")
                                    <a href="#"><img alt="Generic placeholder image" src="{{url('public/front/img/user/')}}/" class="mr-3 rounded-pill"></a>
                                  @else
                                    <a href="#"><img alt="Generic placeholder image" src="{{url('public/front/img/user/')}}/{{$data_review->profile_pic}}" class="mr-3 rounded-pill"></a>
                                  @endif
                                   <div class="media-body">
                                     <div class="reviews-members-header">
                                        <span class="star-rating float-right">
                                          @php
                                          if($val_review->ratting==1)
                                            {
                                                $s1="text-warning";
                                                $s2="";
                                                $s3="";
                                                $s4="";
                                                $s5="";
                                            }
                                             if($val_review->ratting==2)
                                            {
                                                $s1="text-warning";
                                                $s2="text-warning";
                                                $s3="";
                                                $s4="";
                                                $s5="";
                                            }
                                            if($val_review->ratting==3)
                                            {
                                                $s1="text-warning";
                                                $s2="text-warning";
                                                $s3="text-warning";
                                                $s4="";
                                                $s5="";
                                            }
                                            if($val_review->ratting==4)
                                            {
                                                $s1="text-warning";
                                                $s2="text-warning";
                                                $s3="text-warning";
                                                $s4="text-warning";
                                                $s5="";
                                            }
                                            if($val_review->ratting==5)
                                            {
                                                $s1="text-warning";
                                                $s2="text-warning";
                                                $s3="text-warning";
                                                $s4="text-warning";
                                                $s5="text-warning";
                                            }
                                            if(empty($val_review->ratting))
                                            {
                                                $s1="text-warning";
                                                $s2="";
                                                $s3="";
                                                $s4="";
                                                $s5="";
                                            }
                                           @endphp
                                       <i class="fa fa-star star-light submit mr-1 {{$s1}}" id="submit_1" data-rating="1" style="color:gray;"></i>
                                        <i class="fa fa-star star-light submit mr-1 {{$s2}}" id="submit_2" data-rating="2" style="color:gray;"></i>
                                        <i class="fa fa-star star-light submit mr-1 {{$s3}}" id="submit_3" data-rating="3" style="color:gray;"></i>
                                        <i class="fa fa-star star-light submit mr-1 {{$s4}}" id="submit_4" data-rating="4" style="color:gray;"></i>
                                        <i class="fa fa-star star-light submit mr-1 {{$s5}}" id="submit_5" data-rating="5" style="color:gray;"></i>
                                        </span>
                                        <h6 class="mb-1"><a class="text-black" href="#"></a>{{$data_review->first_name}}</h6>
                                        <p class="text-gray">{{$val_review->created_at}}</p>
                                     </div>
                                     <div class="reviews-members-body">
                                        <p>{{$val_review->review}}</p>
                                     </div>
                                     <div class="reviews-members-footer">
                                       
                                     </div>
                                  </div>
                               </div>
                            </div>
                            <hr>
                            @endif
                            @endforeach
                            
                          @endif
                    </div>

                    @if(Auth::id()&&Auth::user()->user_type=='1' && count($review)>0)
                      @php
                      if(Auth::user()->user_type=='1'){
                        if(DB::table('reviews')->where('user_id',Auth::id())->exists()){

                          $rate = DB::table('reviews')->where('res_id',$user->id)->first();

                          if($rate){
                            if($rate->ratting == 1){
                                $s1="text-warning";
                                $s2="";
                                $s3="";
                                $s4="";
                                $s5="";
                            }
                            if($rate->ratting == 2){
                                $s1="text-warning";
                                $s2="text-warning";
                                $s3="";
                                $s4="";
                                $s5="";
                            }
                            if($rate->ratting == 3){
                                $s1="text-warning";
                                $s2="text-warning";
                                $s3="text-warning";
                                $s4="";
                                $s5="";
                            }
                            if($rate->ratting == 4){
                                $s1="text-warning";
                                $s2="text-warning";
                                $s3="text-warning";
                                $s4="text-warning";
                                $s5="";
                            }
                            if($rate->ratting== 5){
                                $s1="text-warning";
                                $s2="text-warning";
                                $s3="text-warning";
                                $s4="text-warning";
                                $s5="text-warning";
                               
                            }
                          }
                        }
                      }
                      @endphp 

                      <form  id="basicform" method="post" action="{{route('add_review')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                          <h5 class="mb-4">Leave Comment</h5>
                          <p class="mb-2">Rate the Place</p>
                          <div class="mb-4">
                                <span class="star-rating">
                                   <i class="fa fa-star star-light submit_star mr-1 {{$s1}}" id="submit_star_1" data-rating="1"></i>
                                   <i class="fa fa-star star-light submit_star mr-1 {{$s2}}" id="submit_star_2" data-rating="2"></i>
                                   <i class="fa fa-star star-light submit_star mr-1 {{$s3}}" id="submit_star_3" data-rating="3"></i>
                                   <i class="fa fa-star star-light submit_star mr-1 {{$s4}}" id="submit_star_4" data-rating="4"></i>
                                   <i class="fa fa-star star-light submit_star mr-1 {{$s5}}" id="submit_star_5" data-rating="5"></i>
                                   <input type="hidden" class="level_class" name="ratting">
                                   <input type="hidden" name="res_id" value="{{$user->id}}">
                                   <input type="hidden" name="user_id" value="{{Auth::id()}}">
                                </span>
                              </div>
                          
                            <div class="form-group">
                                <label>Your Comment</label>
                                <textarea class="form-control" name="review" ></textarea>
                            </div>
                            @if ($errors->has('review'))
                              <span class="text-danger">{{ $errors->first('review') }}</span>
                            @endif
                            <div class="form-group">
                                <button class="btn btn-primary btn-sm" type="submit"> Submit Comment </button>
                            </div>
                        </div>
                      </form>
                    @else
                      <form  id="basicform" method="post" action="{{route('add_review')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                          <h5 class="mb-4">Leave Comment</h5>
                          <p class="mb-2">Rate the Place</p>
                          <div class="mb-4">
                                <span class="star-rating">
                                   <i class="fa fa-star star-light submit_star mr-1" id="submit_star_1" data-rating="1"></i>
                                   <i class="fa fa-star star-light submit_star mr-1" id="submit_star_2" data-rating="2"></i>
                                   <i class="fa fa-star star-light submit_star mr-1" id="submit_star_3" data-rating="3"></i>
                                   <i class="fa fa-star star-light submit_star mr-1" id="submit_star_4" data-rating="4"></i>
                                   <i class="fa fa-star star-light submit_star mr-1" id="submit_star_5" data-rating="5"></i>
                                   <input type="hidden" class="level_class" name="ratting">
                                   <input type="hidden" name="res_id" value="{{$user->id}}">
                                </span>
                              </div>
                          
                            <div class="form-group">
                                <label>Your Comment</label>
                                <textarea class="form-control" name="review" ></textarea>
                            </div>
                             <div class="form-group">
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm" type="button" style="color:white;">Submit Comment</a>
                            </div>
                        </div>
                      </form>
                    @endif
                  </div>

                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="pb-2">
                <div class="bg-white rounded shadow-sm text-white mb-4 p-4 clearfix restaurant-detailed-earn-pts card-icon-overlap">
                  <img class="img-fluid float-left mr-3" src="{{url('public/front/img/earn-score-icon.png')}}">
                  <h6 class="pt-0 text-primary mb-1 font-weight-bold">OFFER</h6>
                  <p class="mb-0">

                  @if($offer->discount_type == 0)
                    ₹{{$offer->value}} off on orders above {{$offer->minmum_spend}} | Use coupon <span class="text-danger font-weight-bold">{{$offer->code}}</span>
                  @else
                    {{$offer->value}}% off on orders above {{$offer->minmum_spend}} | Use coupon <span class="text-danger font-weight-bold">{{$offer->code}}</span>
                  @endif
                  </p>
                  <div class="icon-overlap">
                    <i class="icofont-sale-discount"></i>
                  </div>
                </div>
              </div>
              <div class="generator-bg rounded shadow-sm mb-4 p-4 osahan-cart-item">
                
                  @if(Auth::id())   
                   <h5 class="mb-1 text-white">Your Order</h5>
                   @php 
                    $data = DB::table('tbcart')->where('user_id',Auth::id())->get();
                    
                    $count = count($data);
                    $total=0;
                   @endphp
                    <p class="mb-4 text-white">{{$count}} ITEMS</p>
                    <div class="bg-white rounded shadow-sm mb-2" id="cart">
                      @foreach($data as $pro)

                        @php
                          $product = DB::table('products')->where('id',$pro->product_id)->first();
                          $price = $pro->qty_price;
                          $total_price = $total + $price;
                          $total= $total_price;
                        @endphp
                        <div class='gold-members p-2 border-bottom'>
                         
                            <span class='count-number float-right'>

                              <button type='button' onclick="remove_cart({{$pro->id}})" id='sub' class='sub btn btn-outline-secondary  btn-sm left dec'><i class='icofont-minus'></i>
                              </button>
                            
                              <input class='count-number-input' type='text' value='{{$pro->qty}}' readonly='' min='1' max='10' readonly=''>

                              <button type='button' onclick="add({{$pro->id}})" id='add' class='add btn btn-outline-secondary btn-sm right inc'>
                                <i class='icofont-plus' data-field='quantity' ></i>
                              </button> 
                            </span>

                            <div class='media'>
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
                                         $option = explode("#",$pro->label);
                                      @endphp

                                      @if($option[0] == "" && $option[1] != "" && $option[2] != "" )
                                        <?php echo $option[1]." • ".$option[2]; ?>
                                      @elseif($option[0] != "" && $option[1] == "" && $option[2] != "" )
                                        <?php echo $option[1]; ?>
                                      @elseif($option[0] == "" && $option[1] == "" && $option[2] == "" )
                                        <?php echo ""; ?>
                                      @elseif($option[0] != "" && $option[1] != "" && $option[2] == "" )
                                        <?php echo $option[0]." • ".$option[1]; ?>
                                      @elseif($option[0] == "" && $option[1] == "" && $option[2] != "" )
                                        <?php echo $option[2]; ?>
                                      @elseif($option[0] != "" && $option[1] == "" && $option[2] != "" )
                                        <?php echo $option[0]." • ".$option[2]; ?>
                                      @elseif($option[0] == "" && $option[1] != "" && $option[2] != "" )
                                        <?php echo $option[1]." • ".$option[2]; ?>
                                      @elseif($option[0] == "" && $option[1] != "" && $option[2] == "" )
                                      <?php echo $option[1]; ?>
                                      @elseif($option[0] != "" && $option[1] == "" && $option[2] == "" )
                                      <?php echo $option[0]; ?>
                                      @else
                                        <?php echo implode(" • ",$option); ?>
                                      @endif
                                    @endif
                                   
                                   <h7 class='text-gray mb-0 float-right ml-2' style="margin-top:5px; margin-right:-60px;">
                                    
                                       <?php echo "₹".number_format($pro->qty_price,2); ?>
                                    </h7>
                                 </p>
                                </span>
                              </div>
                            </div>
                        </div>
                      @endforeach                   
                    </div>
                    <div class="mb-2 bg-white rounded p-2 clearfix">
                      <img class="img-fluid float-left" src="{{url('public/front/img/wallet-icon.png')}}">
                      <h6 class="font-weight-bold text-right mb-2">Subtotal : 
                        <span class="text-danger"><?php echo "₹".number_format($total,2) ?></span>
                      </h6>
                      <p class="seven-color mb-1 text-right">Extra charges may apply</p>
                      <p class="text-black mb-0 text-right">You have saved ₹0.00 on the bill</p>
                    </div>
                  @else
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
                  @endif
               
                
                <a href="{{route('checkout')}}" class="btn btn-success btn-block btn-lg">Checkout <i class="icofont-long-arrow-right"></i>
                </a>
              </div>
             
            </div>

          </div>
        </div>
      </section>


        <div class="modal" id="book">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-body">
              <div id="loginmodel">
                <h2></h2>
                  <div class="part-form-main-box">
                    <p style="font-size: 22px;text-align: center; margin-bottom: 40px; margin-top: 40px">To Book table you must login first, Do you want to login now?</p>
                    <div class="row">
                      <div class="col-md-6 col-sm-6 mb-1 ">
                        <a class="btn btn-primary btn-block btn-lg btn-gradient" href="{{url('login_user')}}">Yes</a>
                      </div>
                      <div class="col-md-6 col-sm-6 mb-1 ">
                        <button class="btn btn-secondary btn-block btn-lg btn-gradient" data-bs-dismiss="modal">No</button>
                      </div>
                      
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>

  <div class="modal" id="add_cart">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div id="loginmodel">
            <h2></h2>
              <div class="part-form-main-box">
                <p style="font-size: 22px;text-align: center; margin-bottom: 40px; margin-top: 40px">If you do add to cart, you must login first , Do you want to login now?</p>
                <div class="row">
                  <div class="col-md-6 col-sm-6 mb-1 ">
                    <a class="btn btn-primary btn-block btn-lg btn-gradient" href="{{url('login_user')}}">Yes</a>
                  </div>
                  <div class="col-md-6 col-sm-6 mb-1 ">
                    <button class="btn btn-secondary btn-block btn-lg btn-gradient" data-bs-dismiss="modal">No</button>
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
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" id="modal1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div id="loginmodel">
            <h2></h2>
              <div class="part-form-main-box">
                <p style="font-size: 22px;text-align: center; margin-bottom: 40px; margin-top: 40px">If you want to remove item , you can must in add to cart..</p>
                <div class="row">
                  <div class="col-md-6 col-sm-6 mb-1 ">
                     <a class="btn btn-primary btn-block btn-lg btn-gradient" data-bs-dismiss="modal" style="color:white">Yes</a>
                  </div>
                  <div class="col-md-6 col-sm-6 mb-1 ">
                    <button class="btn btn-secondary btn-block btn-lg btn-gradient" data-bs-dismiss="modal">No</button>
                  </div>
                  
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>      
       
  @endsection

  @section('script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript">

  $(document).ready(function(){

      $(document).on('mouseenter', '.submit_star', function(){

          var rating = $(this).data('rating');

          reset_background();

          for(var count = 1; count <= rating; count++)
          {

              $('#submit_star_'+count).addClass('text-warning');

          }

      }); 

      function reset_background()
      {
          for(var count = 1; count <= 5; count++)
          {

              $('#submit_star_'+count).addClass('star-light');

              $('#submit_star_'+count).removeClass('text-warning');

          }
      }

      $(document).on('mouseleave', '.submit_star', function(){

          reset_background();

          for(var count = 1; count <= rating_data; count++)
          {
              $(".level_class").val(count);

              $('#submit_star_'+count).removeClass('star-light');

              $('#submit_star_'+count).addClass('text-warning');
          }

      });

      $(document).on('click', '.submit_star', function(){

          rating_data = $(this).data('rating');

      });

    });


    $(document).ready(function() {
        ViewCustInGoogleMap();
    });
  // 

   function setMarker(people) {
      geocoder = new google.maps.Geocoder();
      infowindow = new google.maps.InfoWindow();

      var lat = '<?php echo $user->lat; ?>';
      var lng = '<?php echo $user->long; ?>';
      latlng = new google.maps.LatLng(lat, lng);
      marker = new google.maps.Marker({
          position: latlng,
          map: map,
          draggable: false,               // cant drag it
          html: people["DisplayText"]    // Content display on marker click
          //icon: "images/marker.png"       // Give ur own image
      });
      //marker.setPosition(latlng);
      //map.setCenter(latlng);
      google.maps.event.addListener(marker, 'click', function(event) {
          infowindow.setContent(this.html);
          infowindow.setPosition(event.latLng);
          infowindow.open(map, this);
      });
              
  } 

  function add_cart(id){
   var option_label = [];
   var option_price = [];
   var option_name = [];
   var opt = $('#name_opt').val();
   var optArray = opt.split(",");
      for(var i = 0; i < optArray.length; i++){

        var label=$('input[name="'+ optArray[i] +'"]:checked').is(":checked");
        if(label == true)
          {
               $('input:checkbox[name="'+ optArray[i] +'"]:checked').each(function() {
                    option_label.push($(this).val());
                    option_price.push($(this).attr('data-id'));
                    option_name.push($(this).data('action'));
                });
          }
      } 

      var opt_label = (JSON.stringify(option_label));
      var opt_price = (JSON.stringify(option_price));
      var opt_name = (JSON.stringify(option_name));
   
   
// ************* radio (single record select) **************
    // var opt = $('#name_opt').val();
    // var optArray = opt.split(",");
    
    //   for(var i = 0; i < optArray.length; i++){
    //       var name=$('input[name="'+ optArray[i] +'"]:checked').val();
    //       var label=$('input[name="'+ optArray[i] +'"]:checked').is(":checked");
    //       if (label == true){
    //             alert(name);
    //       }
    //   }

    // var Size=$('input[name="Size"]:checked').val();
    // var Capacity=$('input[name="Capacity"]:checked').val();
    // var Weight=$('input[name="Weight"]:checked').val();

    // var labelSize=$('input[name="Size"]:checked').attr('data-id');
    // var labelCapacity=$('input[name="Capacity"]:checked').attr('data-id');
    // var labelWeight=$('input[name="Weight"]:checked').attr('data-id');

    // var optionSize=$('input[name="Size"]:checked').data("action");
    // var optionCapacity=$('input[name="Capacity"]:checked').data("action");
    // var optionWeight=$('input[name="Weight"]:checked').data("action");

    $.ajax({
           url: $("#front_path").val()+"/add_cart"+"/"+id,
           method:"POST",
           data: {
                    "_token": "{{ csrf_token() }}",
                    'id': id,
                    'opt_label':opt_label,
                    'opt_price':opt_price,
                    'opt_name':opt_name,
                  },
           success: function(data)
           {

             if(data.is_veg == 1){
                $("#cart").append("<div class='gold-members p-2 border-bottom'><p class='text-gray mb-0 float-right ml-2'>₹"+data.price+"</p><span class='count-number float-right'><button type='button' id='sub1' class='sub1 btn btn-outline-secondary  btn-sm left dec'><i class='icofont-minus'></i></button><input class='count-number-input' type='text' value='1' readonly='' min='1' max='10' readonly=''><button type='button' id='add1' class='add1 btn btn-outline-secondary btn-sm right inc'><i class='icofont-plus' data-field='quantity' ></i></button> </span><div class='media'><div class='mr-2'><i class='icofont-ui-press text-success food-item'></i></div><div class='media-body'><p class='mt-1 mb-0 text-black'>"+data.name+"</p></div></div></div>");
             }else{
                $("#cart").append("<div class='gold-members p-2 border-bottom'><p class='text-gray mb-0 float-right ml-2'>₹"+data.price+"</p><span class='count-number float-right'><button type='button' id='sub1' class='sub1 btn btn-outline-secondary  btn-sm left dec'><i class='icofont-minus'></i></button><input class='count-number-input' type='text' value='1' readonly='' min='1' max='10' readonly=''><button type='button' id='add1' class='add1 btn btn-outline-secondary btn-sm right inc'><i class='icofont-plus' data-field='quantity'></i></button></span><div class='media'><div class='mr-2'><i class='icofont-ui-press text-danger food-item'></i></div><div class='media-body'><p class='mt-1 mb-0 text-black'>"+data.name+"</p></div></div></div>");
             } 
             // setTimeout(function(){
             //       location.reload();
             //  }, 10);
           }
      });
  }

  

  </script>

  @endsection   