<?php ini_set("allow_url_fopen","1");
?>
<!doctype html>
<html class="no-js" lang="en">
   <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>{{__('messages.site_name')}}</title>
      <meta name="description" content="{{__('messages.site_name')}}">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="apple-touch-icon" href="apple-icon.png">
      <link rel="shortcut icon" href="{{asset('public/home_logo.png')}}">
      <link rel="stylesheet" href="{{asset('public/admin/vendors/bootstrap/dist/css/bootstrap.min.css')}}">
      <link rel="stylesheet" href="{{asset('public/admin/vendors/font-awesome/css/font-awesome.min.css')}}">
      <link rel="stylesheet" href="{{asset('public/admin/vendors/themify-icons/css/themify-icons.css')}}">
      <link rel="stylesheet" href="{{asset('public/admin/vendors/flag-icon-css/css/flag-icon.min.css')}}">
      <link rel="stylesheet" href="{{asset('public/admin/vendors/selectFX/css/cs-skin-elastic.css')}}">
      <link rel="stylesheet" href="{{asset('public/admin/vendors/jqvmap/dist/jqvmap.min.css')}}">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>
      <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'></script>
      <script src='https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.min.js'></script>
      <link rel='stylesheet' href='https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.css'>
      <input type="hidden" id="url_path" value="{{url('/')}}">
      <script src="{{asset('public/uikit/tests/js/test.js')}}"></script>
       @if(Session::get("is_rtl")==0)
      <link rel="stylesheet" href="{{asset('public/admin/assets/css/style.css').'?v=dddd'}}">
      @else
       <link rel="stylesheet" href="{{asset('public/admin/assets/css/Rtl.css').'?v=2'}}">
      @endif
     
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="{{asset('public/admin/vendors/chosen/chosen.min.css')}}">
      <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.3/css/selectize.default.min.css'>
      <script>UPLOADCARE_PUBLIC_KEY = '7b7f57c2b9e95d9770ed';</script>
      
      <script type="text/javascript" src="{{asset('public/ckeditor/ckeditor.js')}}"></script>
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
     
      <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">

     <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyC_mpeoYYKZynug14lspINNCxNGv0nm8W8&libraries=places"></script>

     <style type="text/css">
       input#us2-address {
            width: 100%;
            background-color: #f7f7f7;
            border: none;
            padding: 3px 0px;
            margin-bottom: 15px;
            padding: 8px;
        }

        .map #us2 {
            width: 100%;
            height: 250px;
        }
     </style>
   </head>
   <style type="text/css">
   .product-heading h1:before{
      background-color:<?= Session::get('site_color') ?> !important;
   }
   .product-heading h1:after{
      background-color:<?= Session::get('site_color') ?> !important;
   }
   .one-product-slider h1:after{
      background-color:<?= Session::get('site_color') ?> !important;
   }
   .col-md-3.services:after{
      background-color:<?= Session::get('site_color') ?> !important;
   }
   
</style>


   <body class="rtl">
       <div id="overlaychk">
                        <div class="cv-spinner">
                           <span class="spinner"></span>
                        </div>
                     </div>
      <div class="go">
      <aside id="left-panel" class="left-panel">
         <nav class="navbar navbar-expand-sm navbar-default">
            <div class="navbar-header">
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
               <i class="fa fa-bars"></i>
               </button>
               <a class="navbar-brand" href="{{url('seller/dashboard')}}">
               {{Auth::user()->first_name}}
               </a>
               <a class="navbar-brand hidden" href="{{url('seller/dashboard')}}">    
               {{__('messages.short_code')}}
               </a>
            </div>
            <div id="main-menu" class="main-menu collapse">
               <ul class="nav navbar-nav">
                  <li class="active">
                     <a href="{{url('seller/dashboard')}}">
                     <i class="menu-icon fa fa-dashboard"></i>
                     {{__('messages.dashboard')}}
                     </a>
                  </li>
                  <h3 class="menu-title"></h3>
                   <li class="active">
                     <a href="{{url('seller/sales')}}"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-dollar"></i>
                           Orders
                     </a>                     
                  </li> 

                  <li class="active">
                     <a href="{{url('seller/res_category')}}"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-list"></i>
                         {{__('messages.add')}} Menu
                     </a>                     
                  </li>
                  
                  
                   <li class="active">
                     <a href="{{url('seller/paymenthistory')}}"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-money"></i>
                           {{__('messages.payment history')}}
                     </a>                     
                  </li>
                   <li class="active">
                     <a href="{{url('seller/coupon')}}">
                     <i class="menu-icon  fa fa-tags"></i>
                     {{__('messages.coupon')}}
                     </a>
                  </li>
                  <li class="active">
                     <a href="{{url('seller/restaurant_profile')}}">
                     <i class="menu-icon  fa fa-user"></i>
                     {{__('messages.my_profile')}}
                     </a>
                  </li>
               </ul>
            </div>
         </nav>
      </aside>
      <div id="right-panel" class="right-panel">
         <header id="header" class="header">
            <div class="header-menu">
               <div class="col-sm-9 pr0 eco-tog">
                  <a id="menuToggle" class="menutoggle pull-left">
                  <i class="fa fa fa-tasks"></i>
                  </a>
                  <div class="header-left florig">                     
                  </div>
               </div>
               <div class="col-sm-1 dropdown">
               <div class="dropdown d-inline-block language-switch">
                            <button type="button" class="btn header-item waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                 @foreach($lang as $l)
                                   @if(Session::get('locale')==$l->code)
                                      <img src="{{asset('public/upload/language_image').'/'.$l->image}}" alt="" height="16">
                                   @endif
                                 @endforeach
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-28px, 70px, 0px);" x-placement="bottom-end">
                              
                              @foreach($lang as $l)
                                <!-- item-->
                                <a href="{{url('languagechange').'/'.$l->code}}" class="dropdown-item notify-item">
                                    <img src="{{asset('public/upload/language_image').'/'.$l->image}}" alt="user-image" style="height: 16px;" class="mr-1" height="12"> <span class="align-middle">{{$l->name}}</span>
                                </a>
                              @endforeach
                               
                            </div>
                        </div>
                     </div>
                   <div class="col-sm-1 dropdown for-notification">
                         <div class="dropdown">
           <button class="btn  dropdown-toggle" type="button" id="bell-button" onclick="checknotify()" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fa fa-bell"></i>
                                         <span class="count bg-danger" id="ordercount" style="">0</span>
           </button>
           <div class="dropdown-menu" aria-labelledby="bell-button" id="notificationshow">
            <p class="red" id="notificationmsg">{{__('messages.orders_pending')}}</p>
           </div>
         </div>
                        </div>
               <div class="col-sm-1 col-12 eco-pro">
                  <div class="user-area dropdown float-right">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <img class="user-avatar rounded-circle" src="{{Session::get('profile_pic')?Session::get('profile_pic'):asset('public/upload/profile/defaultuser.jpg')}}" alt="User Avatar">
                     </a>
                     <div class="user-menu dropdown-menu">
                     <!--    <a class="nav-link" href="{{url('seller/editprofile')}}">
                        <i class="fa fa-user"></i>
                        {{__('messages.my_profile')}}
                        </a>
                        <a class="nav-link" href="{{url('seller/changepassword')}}">
                        <i class="fa fa-user"></i>
                        {{__('messages.change_pwd')}}
                        </a> -->
                        <a class="nav-link" href="{{url('seller/logout')}}">
                        <i class="fa fa-power-off"></i>
                        {{__('messages.logout')}}
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </header>
         @yield('content')
   </div>
</div>
<input type="hidden" id="soundnotify" value="{{asset('public/sound/notification/notification.mp3')}}">
      <input type="hidden" id="url_path" value="{{url('/')}}">
      <input type="hidden" id="error_cur_pwd" value="{{__('passwords.error_cur_pwd')}}">
      <input type="hidden" id="delete_data" value='{{__("messages_error_success.delete_alert")}}'>
      <input type="hidden" id="data_save_success" value="{{__('messages_error_success.data_save_success')}}">
      <input type="hidden" id="something" value="{{__('messages_error_success.error_code')}}">
      <input type="hidden" id="generalmsg" value="{{__('messages_error_success.general_form_msg')}}">
      <input type="hidden" id="fixerror" value="{{__('messages_error_success.per_bet')}}">
      <input type="hidden" id="check_price" value="{{__('messages_error_success.check_price')}}">
      <input type="hidden" id="offer_price_error" value="{{__('messages_error_success.offer_price_error')}}">
      <input type="hidden" id="pass_mus" value="{{__('passwords.pass_mus')}}">
      <input type="hidden" id="requiredfields" value="{{__('messages_error_success.required_field')}}">

      <input type="hidden" id="soundnotify" value="{{asset('public/sound/notification/notification.mp3')}}">
      <input type="hidden" id="orders_pending" value="{{__('messages.orders_pending')}}">
      <input type="hidden" id="you_have" value="{{__('messages.you_have')}}">
      <input type="hidden" id="new_order" value="{{__('messages.new_order')}}">
      <input type="hidden" id="demo_lang" value="{{Session::get('is_demo')}}">
       <input type="hidden" id="vieworder_lang" value="{{__('messages.view_order')}}">
       <input type="hidden" id="setemail" value="{{__('messages.setupemail')}}"/>
        <input type="hidden" id="no_realted_msg" value="{{__('messages.no_realted')}}"/>
        <input type="hidden" id="order_accept_msg" value="{{__('messages.order_accept_by_seller')}}">
      <input type="hidden" id="coupon_vaild_max" value="{{__('messages.coupon_vaild_max')}}"/>
      <input type="hidden" id="image_invaild" value="{{__('messages.img_invaild')}}"/>
      <input type="hidden" id="view_attr" value="{{__('messages.view_attr')}}">
      <input type="hidden" id="view_option" value="Set Customisation">
      <script src="{{asset('public/admin/vendors/jquery/dist/jquery.min.js')}}"></script>
      <script src="{{asset('public/admin/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
      <script src="{{asset('public/admin/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
      <script src="{{asset('public/admin/assets/js/main.js')}}"></script>
      <script src="{{asset('public/admin/vendors/chosen/chosen.jquery.min.js')}}"></script>
      <script src="{{asset('public/admin/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js')}}"></script>
       <script src='https://code.jquery.com/jquery-1.12.3.js'></script>
      <script src="{{url('public/js/locationpicker.js?v=bvp')}}"></script>
     
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
      <script src='https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js'></script>
      <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js'></script>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
      <script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js'></script>
      <script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js'></script>
      <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js'></script>
      <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js'></script>
      <script type="text/javascript" src="{{asset('public/js/seller.js?v=vbn')}}"></script>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.1/js/standalone/selectize.js'></script>
     <!--  <script type="text/javascript" src='https://maps.google.com/maps/api/js?key=AIzaSyC1JUHjsnQZtKx5eBOpG42E_CLoJ1s39AU&libraries=places'></script> -->
   
      @yield('footer')
   </body>
</html>