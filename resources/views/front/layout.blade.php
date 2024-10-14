<!doctype html>
<html lang="en">
  <!-- Mirrored from askbootstrap.com/preview/osahan-eat/theme-2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 23 Nov 2022 12:18:18 GMT -->
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Askbootstrap">
    <meta name="author" content="Askbootstrap">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{asset('public/front/img/favicon.png')}}">
    <link href="{{asset('public/front/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/fontawesome/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/icofont/icofont.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/css/osahan.css')}}" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset('public/front/vendor/owl-carousel/owl.carousel.css')}}">
    <link rel="stylesheet" href="{{asset('public/front/vendor/owl-carousel/owl.theme.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('https://rawgit.com/OwlCarousel2/OwlCarousel2/develop/dist/assets/owl.carousel.min.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
 
     <script async defer type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyC_mpeoYYKZynug14lspINNCxNGv0nm8W8&libraries=places&sensor=false&callback=initialise"></script>
         <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
        <script src="{{url('public/js/front_location.js?v=asd')}}"></script>
  
    <style type="text/css">
      .navbar {
         
           margin-bottom: 0px; 
         
      }
      .dropdown-cart-top {
          border-top: 2px solid #dd646e;
          min-width: 340px !important;
      }
      
    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light osahan-nav shadow-sm">
      <div class="container">
        <a class="navbar-brand" href="index.html">
          <img alt="logo" src="{{asset('public/front/img/logo.png')}}">
        </a>
        

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="{{url('/')}}">Home <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{url('offer')}}">
                <i class="icofont-sale-discount"></i> Offers <span class="badge badge-danger">New</span>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Restaurants </a>
              <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                <a class="dropdown-item" href="{{route('listing_data',['type'=>'all'])}}">Listing</a>
                <a class="dropdown-item" href="detail.html">Detail + Cart</a>
                @if(Auth::id())
                <a class="dropdown-item" href="{{route('checkout')}}">Checkout</a>
                @endif
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Pages </a>
              <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                <a class="dropdown-item" href="track-order.html">Track Order</a>
                <!-- <a class="dropdown-item" href="">Invoice</a> -->
                @if(Auth::id()&&Auth::user()->user_type=='1')
                  <a class="dropdown-item" href="{{route('my_account')}}">My Profile</a>
                  <a class="dropdown-item" href="{{route('userlogout')}}">Log out</a>
                @else
                  <a class="dropdown-item" href="{{route('login_user')}}">Login</a>
                  <a class="dropdown-item" href="{{route('register')}}">Register</a>
                @endif
                <a class="dropdown-item" href="404.html">404</a>
                <a class="dropdown-item" href="extra.html">Extra :)</a>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img alt="Generic placeholder image" src="{{asset('public/front/img/user/4.png')}}" class="nav-osahan-pic rounded-pill"> My Account </a>
              <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                <a class="dropdown-item" href="{{url('my_account')}}">
                  <i class="icofont-food-cart"></i> Orders </a>
                <a class="dropdown-item" href="{{url('offer')}}">
                  <i class="icofont-sale-discount"></i> Offers </a>
                <a class="dropdown-item" href="{{route('Wishlist')}}">
                  <i class="icofont-heart"></i> Favourites </a>
                <a class="dropdown-item" href="orders.html#payments">
                  <i class="icofont-credit-card"></i> Payments </a>
                <a class="dropdown-item" href="{{route('address_list')}}">
                  <i class="icofont-location-pin"></i> Addresses </a>
              </div>
            </li>
            <li class="nav-item dropdown dropdown-cart">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-shopping-basket"></i> Cart <span class="badge badge-success">5</span>
              </a>
              <div class="dropdown-menu dropdown-cart-top p-0 dropdown-menu-right shadow-sm border-0">
                @if(DB::table('tbcart')->where('user_id',Auth::id())->exists())
                  @php
                    $cart = DB::table('tbcart')->where('user_id',Auth::id())->get();
                    $res_id = DB::table('tbcart')->where('user_id',Auth::id())->first();
                    $res = DB::table('users')->where('id',$res_id->res_id)->first();
                    $total=0;
                    $tax=0;
                    $discount=0;
                  @endphp
                  <div class="dropdown-cart-top-header p-4">
                      <img class="img-fluid mr-3" alt="osahan" src="{{url('public/upload/restaurant')}}/{{$res->res_image}}" style="height:59px; width:59px!important">
                      <h6 class="mb-0">{{$res->first_name}}</h6>
                      <p class="text-secondary mb-0">{{$res->address}}</p>
                      <small>
                        <a class="text-primary font-weight-bold" href="{{url('res_detail/'.$res->id)}}">View Full Menu</a>
                      </small>
                  </div>
                  <div class="dropdown-cart-top-body border-top p-4">
                  @foreach($cart as $val)
                    @php
                    $product = DB::table('products')->where('id',$val->product_id)->first();
                    $price = $val->qty_price;
                    $total_price = $total + $price;
                    $total= $total_price;
                  
                    $admin = DB::table('setting')->first();
                    $delivery_charges = $admin->delivery_charges;

                    $total_pay = $total + $tax + $delivery_charges - $discount;
                    @endphp

                    
                      <p class="mb-2">
                        @if($product->is_veg == 1)
                         <i class='icofont-ui-press text-success food-item'></i>
                        @else
                          <i class='icofont-ui-press text-danger food-item'></i>
                        @endif
                        
                        {{$product->name}} x {{$val->qty}}<span class="float-right text-secondary">
                          ₹{{number_format($val->qty_price,2)}}</span><br>

                          @if( DB::table('product_options')->where('product_id',$product->id)->exists() )
                            @php
                              $option_pro = DB::table('product_options')->where('product_id',$product->id)->first();  
                               $option = explode("#",$val->label);
                            @endphp
                             <?php echo implode(" • ",$option); ?>
                          @endif
                        </p>
                  @endforeach
                </div>
                <div class="dropdown-cart-top-footer border-top p-4">
                  <p class="mb-0 font-weight-bold text-secondary">Sub Total <span class="float-right text-dark">₹{{number_format($total,2)}}</span>
                  </p>
                  <small class="text-info">Extra charges may apply</small>
                </div>
                <div class="dropdown-cart-top-footer border-top p-2">
                  <a class="btn btn-success btn-block btn-lg" href="{{route('checkout')}}"> Checkout</a>
                </div>
                @else
                <div class="dropdown-cart-top-header p-4">
                  Your cart is empty.
                </div>
                <div class="dropdown-cart-top-footer border-top p-2">
                  <a class="btn btn-success btn-block btn-lg" type="submit" href="#"> Checkout</a>
                </div>
                @endif
              </div>
            </li>
             @if(Auth::id()&&Auth::user()->user_type=='1')
             <li class="nav-item">
                <a class="nav-link" href="{{route('userlogout')}}">Log out</a>
             </li>
             @else
             <li class="nav-item">
                <a class="nav-link" href="{{route('login_user')}}">Login</a>
             </li>
             @endif
          </ul>
        </div>
      </div>
    </nav>
    @yield('content')

   
    <section class="section pt-5 pb-5 text-center bg-white">
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            <h5 class="m-0">Operate food store or restaurants? <a href="login.html">Work With Us</a>
            </h5>
          </div>
        </div>
      </div>
    </section>
    <section class="footer pt-5 pb-5">
      <div class="container">
        <div class="row">
          <div class="col-md-4 col-12 col-sm-12">
            <h6 class="mb-3">Subscribe to our Newsletter</h6>
            <form class="newsletter-form mb-1">
              <div class="input-group">
                <input type="text" placeholder="Please enter your email" class="form-control">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary"> Subscribe </button>
                </div>
              </div>
            </form>
            <p>
              <a class="text-info" href="register.html">Register now</a> to get updates on <a href="offers.html">Offers and Coupons</a>
            </p>
            <div class="app">
              <p class="mb-2">DOWNLOAD APP</p>
              <a href="#">
                <img class="img-fluid" src="{{asset('public/front/img/google.png')}}" style="max-width:30% !important; height:auto !important;">
              </a>
              <a href="#">
                <img class="img-fluid" src="{{asset('public/front/img/apple.png')}}" style="max-width:30% !important; height:auto !important;">
              </a>
            </div>
          </div>
          <div class="col-md-1 col-sm-6 mobile-none"></div>
          <div class="col-md-2 col-6 col-sm-4">
            <h6 class="mb-3">About OE</h6>
            <ul>
              <li>
                <a href="#">About Us</a>
              </li>
              <li>
                <a href="#">Culture</a>
              </li>
              <li>
                <a href="#">Blog</a>
              </li>
              <li>
                <a href="#">Careers</a>
              </li>
              <li>
                <a href="#">Contact</a>
              </li>
            </ul>
          </div>
          <div class="col-md-2 col-6 col-sm-4">
            <h6 class="mb-3">For Foodies</h6>
            <ul>
              <li>
                <a href="#">Community</a>
              </li>
              <li>
                <a href="#">Developers</a>
              </li>
              <li>
                <a href="#">Blogger Help</a>
              </li>
              <li>
                <a href="#">Verified Users</a>
              </li>
              <li>
                <a href="#">Code of Conduct</a>
              </li>
            </ul>
          </div>
          <div class="col-md-2 m-none col-4 col-sm-4">
            <h6 class="mb-3">For Restaurants</h6>
            <ul>
              <li>
                <a href="#">Advertise</a>
              </li>
              <li>
                <a href="#">Add a Restaurant</a>
              </li>
              <li>
                <a href="#">Claim your Listing</a>
              </li>
              <li>
                <a href="#">For Businesses</a>
              </li>
              <li>
                <a href="#">Owner Guidelines</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <section class="footer-bottom-search pt-5 pb-5 bg-white">
      <div class="container">
        <div class="row">
          <div class="col-xl-12">
            <p class="text-black">POPULAR COUNTRIES</p>
            <div class="search-links">
              <a href="#">Australia</a> | <a href="#">Brasil</a> | <a href="#">Canada</a> | <a href="#">Chile</a> | <a href="#">Czech Republic</a> | <a href="#">India</a> | <a href="#">Indonesia</a> | <a href="#">Ireland</a> | <a href="#">New Zealand</a> | <a href="#">United Kingdom</a> | <a href="#">Turkey</a> | <a href="#">Philippines</a> | <a href="#">Sri Lanka</a> | <a href="#">Australia</a> | <a href="#">Brasil</a> | <a href="#">Canada</a> | <a href="#">Chile</a> | <a href="#">Czech Republic</a> | <a href="#">India</a> | <a href="#">Indonesia</a> | <a href="#">Ireland</a> | <a href="#">New Zealand</a> | <a href="#">United Kingdom</a> | <a href="#">Turkey</a> | <a href="#">Philippines</a> | <a href="#">Sri Lanka</a>
              <a href="#">Australia</a> | <a href="#">Brasil</a> | <a href="#">Canada</a> | <a href="#">Chile</a> | <a href="#">Czech Republic</a> | <a href="#">India</a> | <a href="#">Indonesia</a> | <a href="#">Ireland</a> | <a href="#">New Zealand</a> | <a href="#">United Kingdom</a> | <a href="#">Turkey</a> | <a href="#">Philippines</a> | <a href="#">Sri Lanka</a> | <a href="#">Australia</a> | <a href="#">Brasil</a> | <a href="#">Canada</a> | <a href="#">Chile</a> | <a href="#">Czech Republic</a> | <a href="#">India</a> | <a href="#">Indonesia</a> | <a href="#">Ireland</a> | <a href="#">New Zealand</a> | <a href="#">United Kingdom</a> | <a href="#">Turkey</a> | <a href="#">Philippines</a> | <a href="#">Sri Lanka</a>
            </div>
            <p class="mt-4 text-black">POPULAR FOOD</p>
            <div class="search-links">
              <a href="#">Fast Food</a> | <a href="#">Chinese</a> | <a href="#">Street Food</a> | <a href="#">Continental</a> | <a href="#">Mithai</a> | <a href="#">Cafe</a> | <a href="#">South Indian</a> | <a href="#">Punjabi Food</a> | <a href="#">Fast Food</a> | <a href="#">Chinese</a> | <a href="#">Street Food</a> | <a href="#">Continental</a> | <a href="#">Mithai</a> | <a href="#">Cafe</a> | <a href="#">South Indian</a> | <a href="#">Punjabi Food</a>
              <a href="#">Fast Food</a> | <a href="#">Chinese</a> | <a href="#">Street Food</a> | <a href="#">Continental</a> | <a href="#">Mithai</a> | <a href="#">Cafe</a> | <a href="#">South Indian</a> | <a href="#">Punjabi Food</a> | <a href="#">Fast Food</a> | <a href="#">Chinese</a> | <a href="#">Street Food</a> | <a href="#">Continental</a> | <a href="#">Mithai</a> | <a href="#">Cafe</a> | <a href="#">South Indian</a> | <a href="#">Punjabi Food</a>
            </div>
          </div>
        </div>
      </div>
    </section>


    <footer class="pt-4 pb-4 text-center">
      <div class="container">
        <p class="mt-0 mb-0">© Copyright 2020 Osahan Eat. All Rights Reserved</p>
        <small class="mt-0 mb-0"> Made with <i class="fas fa-heart heart-icon text-danger"></i> by <a class="text-danger" target="_blank" href="{{url('https://www.instagram.com/iamgurdeeposahan/')}}">Gurdeep Osahan</a> - <a class="text-primary" target="_blank" href="{{url('https://askbootstrap.com/')}}">Ask Bootstrap</a>
        </small>
      </div>
    </footer>

    @yield('footer')
    <div class="modal" id="myModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div id="loginmodel">
              <h2></h2>
                <div class="part-form-main-box">
                  <p style="font-size: 22px;text-align: center; margin-bottom: 40px; margin-top: 40px">Do you wont to login ?</p>
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
     <input type="hidden" id="front_path" value="{{url('/')}}">
    <script src="{{asset('public/front/vendor/jquery/jquery-3.3.1.slim.min.js')}}" type="b0cfe9e7114ed9987d469c49-text/javascript"></script>
    <script src="{{asset('public/front/vendor/bootstrap/js/bootstrap.bundle.min.js')}}" type="b0cfe9e7114ed9987d469c49-text/javascript"></script>
    <script src="{{asset('public/front/vendor/select2/js/select2.min.js')}}" type="b0cfe9e7114ed9987d469c49-text/javascript"></script>
    <script src="{{asset('public/front/vendor/owl-carousel/owl.carousel.js')}}" type="b0cfe9e7114ed9987d469c49-text/javascript"></script>
    <script src="{{asset('public/front/js/custom.js')}}" type="b0cfe9e7114ed9987d469c49-text/javascript"></script>

  <!--   <script src="{{asset('public/front/../../askbootstrap.com/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js')}}" data-cf-settings="b0cfe9e7114ed9987d469c49-|49" defer=""></script> -->
    <script defer src="{{url('https://static.cloudflareinsights.com/beacon.min.js/vaafb692b2aea4879b33c060e79fe94621666317369993')}}" integrity="sha512-0ahDYl866UMhKuYcW078ScMalXqtFJggm7TmlUtp0UlD4eQk0Ixfnm5ykXKvGJNFjLMoortdseTfsRT8oCfgGA==" data-cf-beacon='{"rayId":"76e9d8b849fbf4d2","version":"2022.11.3","r":1,"token":"dd471ab1978346bbb991feaa79e6ce5c","si":100}' crossorigin="anonymous"></script>
   
    <script type="text/javascript" src="https://rawgit.com/OwlCarousel2/OwlCarousel2/develop/dist/owl.carousel.min.js"></script>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <script type="text/javascript" src="{{asset('public/js/front.js?v=svh')}}"></script>


        <script type="text/javascript">
            $(function() {
              $('.owl-carousel.owl-carousel-category.owl-theme').owlCarousel({
                loop:true,
                nav: true,
                navText: ['<span aria-label="Previous">&#x2039;</span>', '<span aria-label="Next">&#x203a;</span>'],
                dots: false,
                autoplay:true,
                autoplayTimeout:1000,
                autoplayHoverPause:true,
                responsive: {
                  0: {
                    items: 2,
                  },
                  750: {
                    items: 4,
                  },
                  1050: {
                    items: 8,
                  }
                }
              });
            });
        </script>

        <script type="text/javascript">
            $(function() {
              $('.owl-carousel.owl-carousel-four.owl-theme').owlCarousel({
                loop:true,
                nav: true,
                navText: ['<span aria-label="Previous">&#x2039;</span>', '<span aria-label="Next">&#x203a;</span>'],
                dots: false,
                autoplay:true,
                autoplayTimeout:1000,
                autoplayHoverPause:true,
                responsive: {
                  0: {
                    items: 1,
                  },
                  750: {
                    items: 2,
                  },
                  1050: {
                    items: 4,
                  }
                }
              });
            });
        </script>
        
        <script type="text/javascript">
            $(function() {
              $('.owl-carousel.owl-carousel-five.owl-theme').owlCarousel({
                loop:true,
                nav: true,
                navText: ['<span aria-label="Previous">&#x2039;</span>', '<span aria-label="Next">&#x203a;</span>'],
                dots: false,
                autoplay:true,
                autoplayTimeout:1000,
                autoplayHoverPause:true,
                responsive: {
                  0: {
                    items: 1,
                  },
                  750: {
                    items: 3,
                  },
                  1050: {
                    items: 6,
                  }
                }
              });
            });
        </script>
       
  </body>
  @yield('script')
  <!-- Mirrored from askbootstrap.com/preview/osahan-eat/theme-2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 23 Nov 2022 12:18:32 GMT -->
</html>