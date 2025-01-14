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
      <link rel="stylesheet" href="{{asset('public/admin/assets/css/style.css').'?v=2'}}">
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
       <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
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
               <a class="navbar-brand" href="{{url('admin/dashboard')}}">
               {{__('messages.site_name')}}
               </a>
               <a class="navbar-brand hidden" href="{{url('admin/dashboard')}}">    
               {{__('messages.short_code')}}
               </a>
            </div>
            <div id="main-menu" class="main-menu collapse">
               <ul class="nav navbar-nav">
                  <li class="active">
                     <a href="{{url('admin/dashboard')}}">
                     <i class="menu-icon fa fa-dashboard"></i>
                     {{__('messages.dashboard')}}
                     </a>
                  </li>
                  <h3 class="menu-title"></h3>
                  <li class="active">
                     <a href="{{url('admin/order')}}"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-dollar"></i>
                        Orders
                     </a>                     
                  </li> 
                  <li>
                     <a href="{{url('admin/category')}}">
                        <i class="menu-icon  fa fa-cube"></i>
                        {{__('messages.category')}}
                     </a>
                  </li>
                  <li>
                     <a href="{{url('admin/seller')}}">
                        <i class="menu-icon  fa fa-users"></i>
                        {{__('messages.Seller')}}
                     </a>
                  </li>
                 
                  <li>
                     <a href="{{url('admin/featureproduct')}}">
                        <i class="menu-icon  fa fa-cube"></i>
                     {{__('messages.feature_product')}}
                     </a>
                  </li>
                  <li>
                     <a href="{{url('admin/unapprove_product')}}">
                        <i class="menu-icon  fa fa-cube"></i>
                     {{__('messages.unapprove_product')}}
                     </a>
                  </li>                  
                  <!-- <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-cube"></i>
                     {{__('messages.product')}}
                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="{{url('admin/category')}}">
                           {{__('messages.category')}}
                           </a>
                        </li>                       
                        <li>
                           <a href="{{url('admin/options')}}">
                           {{__('messages.option')}}
                           </a>
                        </li>                        
                        <li>
                           <a href="{{url('admin/review')}}">
                           {{__('messages.review')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/featureproduct')}}">
                           {{__('messages.feature_product')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/unapprove_product')}}">
                           {{__('messages.unapprove_product')}}
                           </a>
                        </li>
                     </ul>
                  </li> -->
                  <li class="active">
                     <a href="{{url('admin/product')}}"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-linode"></i>
                         {{__('messages.catalog')}}
                     </a>                     
                  </li>
                   <li class="active">
                     <a href="{{url('admin/news')}}"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-newspaper-o"></i>
                        {{__('messages.news')}}
                     </a>                     
                  </li>
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-money"></i>
                     {{__('messages.Payment')}}
                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="{{url('admin/pendingpayment')}}">
                           {{__('messages.Pending Payment')}}
                           </a>
                        </li>                       
                        <li>
                           <a href="{{url('admin/completepayment')}}">
                           {{__('messages.Completed Payment')}}
                           </a>
                        </li>
                         <li>
                           <a href="{{url('admin/currentpayment')}}">
                           {{__('messages.Current Payment')}}
                           </a>
                        </li> 
                     </ul>
                  </li>
                  <li class="active">
                     <a href="{{url('admin/city')}}"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-map-marker"></i>
                        {{__('messages.city')}}
                     </a>                     
                  </li>
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-gift"></i>
                     {{__('messages.offers')}} 
                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="{{url('admin/offer')}}">
                           {{__('messages.big_offer')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/normaloffer')}}">
                           {{__('messages.normal_offer')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/sensonal_offer')}}">
                           {{__('messages.sensonal_offer')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/deals')}}">
                           {{__('messages.current_offer')}}
                           </a>
                        </li>
                     </ul>
                  </li>
                  
                 @if(Session::get("is_web")==1)
                  <li class="active">
                     <a href="{{url('admin/sepical_category')}}">
                     <i class="menu-icon fa fa-image"></i>
                     {{__('messages.special_category')}}
                     </a>
                  </li>
                  @endif
                  <span style="color:#c8c9ce!important">Privacy Policy</span>
                     <li>
                        <a href="{{url('admin/about')}}" class="waves-effect">
                        <i class="menu-icon fa fa-key "></i>
                        <span>{{__('messages.About')}}</span>
                        </a>
                     </li>
                     <li>
                        <a href="{{url('admin/Terms_condition')}}" class="waves-effect">
                        <i class="menu-icon fa fa-key"></i>
                        <span>{{__('messages.term')}}</span>
                        </a>
                     </li>
                     <li>
                        <a href="{{url('admin/app_privacy')}}" class="waves-effect">
                        <i class="menu-icon fa fa-key"></i>
                        <span>{{__('messages.Privecy')}}</span>
                        </a>
                     </li>
                     <li>
                        <a href="{{url('admin/data_deletion')}}" class="waves-effect">
                        <i class="menu-icon fa fa-key"></i>
                        <span>{{__('messages.Data-Deletion')}}</span>
                        </a>
                     </li>
                   <li class="active">
                     <a href="{{url('admin/notification')}}">
                     <i class="menu-icon fa fa-image"></i>
                     {{__('messages.notification')}}
                     </a>
                  </li>
                 @if(Session::get("is_web")==1)
                  <li class="active">
                     <a href="{{url('admin/contact')}}">
                     <i class="menu-icon fa fa-address-book"></i>
                     {{__('messages.contact_details')}}
                     </a>
                  </li>
                 @endif
                 
                 <!-- <li class="active">
                     <a href="{{url('admin/coupon')}}">
                     <i class="menu-icon  fa fa-tags"></i>
                     {{__('messages.coupon')}}
                     </a>
                  </li>  -->
                  <li>
                     <a href="{{url('admin/review')}}">
                       <i class="menu-icon  fa fa-cube"></i>
                     {{__('messages.review')}}
                     </a>
                  </li>
                   <li class="active">
                     <a href="{{url('admin/complain')}}">
                     <i class="menu-icon  fa fa-tags"></i>
                     {{__('messages.complain')}}
                     </a>
                  </li>
                  
                  
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-users"></i>
                     {{__('messages.users')}}
                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="{{url('admin/user')}}">
                           {{__('messages.users')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/admin')}}">
                           {{__('messages.admin')}}
                           </a>
                        </li>
                         
                        <li>
                           <a href="{{url('admin/userrole')}}">
                           {{__('messages.role')}}
                           </a>
                        </li>
                        <li>
                            <a href="{{url('admin/deliveryboys')}}">
                                {{__("messages.delivery_boy")}}
                            </a>
                        </li>
                        <li>
                            <a href="{{url('admin/document_verification')}}">
                                {{__("messages.Document Verification")}}
                            </a>
                        </li>
                     </ul>
                  </li>
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-cog"></i>
                     {{__('messages.site_setting')}}
                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="{{url('admin/setting')}}">
                           {{__('messages.setting')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/pages')}}">
                           {{__('messages.page')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/serverkey/1')}}">
                           {{__('messages.Android Server Key')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/serverkey/2')}}">
                           {{__('messages.Iphone Server Key')}}
                           </a>
                        </li>
                        @if(Session::get("is_web")==1)
                        <li>
                           <a href="{{url('admin/banner')}}">
                           {{__('messages.banner')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/support/1')}}">
                           {{__('messages.helpsupport')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/support/2')}}">
                           {{__('messages.termscon')}}
                           </a>
                        </li>
                        @endif
                     </ul>
                  </li>
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon fa fa-globe"></i>
                     {{__('messages.localization')}}
                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="{{url('admin/language')}}">
                           {{__('messages.Langauge')}}
                           </a>
                        </li>
                        <li>
                           <a href="{{url('admin/taxes')}}">
                           {{__('messages.taxes')}}
                           </a>
                        </li>
                     </ul>
                  </li>
<li class="active">
                     <a href="{{url('admin/report')}}">
                     <i class="menu-icon fa fa-bar-chart"></i>
                     {{__('messages.report')}}
                     </a>
                  </li> 
               </ul>
            </div>
         </nav>
      </aside>
      <div id="right-panel" class="right-panel">
         <header id="header" class="header">
            <div class="header-menu">
               <div class="col-sm-8 pr0 eco-tog">
                  <a id="menuToggle" class="menutoggle pull-left">
                  <i class="fa fa fa-tasks"></i>
                  </a>
                  <div class="header-left florig">                     
                  </div>
               </div><div class="col-sm-1 dropdown">
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
               <div class="col-sm-2 col-12 eco-pro">

                  <div class="user-area dropdown float-right">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <img class="user-avatar rounded-circle" src="{{Session::get('profile_pic')}}" alt="User Avatar">
                     </a>
                     <div class="user-menu dropdown-menu">
                        <a class="nav-link" href="{{url('admin/editprofile')}}">
                        <i class="fa fa-user"></i>
                        {{__('messages.my_profile')}}
                        </a>
                        <a class="nav-link" href="{{url('admin/changepassword')}}">
                        <i class="fa fa-user"></i>
                        {{__('messages.change_pwd')}}
                        </a>
                        <a class="nav-link" href="{{url('admin/logout')}}">
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
      <input type="hidden" id="coupon_vaild_max" value="{{__('messages.coupon_vaild_max')}}"/>
      <input type="hidden" id="image_invaild" value="{{__('messages.img_invaild')}}"/>
      <script src="{{asset('public/admin/vendors/jquery/dist/jquery.min.js')}}"></script>
      <script src="{{asset('public/admin/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
      <script src="{{asset('public/admin/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
      <script src="{{asset('public/admin/assets/js/main.js')}}"></script>
      <script src="{{asset('public/admin/vendors/chosen/chosen.jquery.min.js')}}"></script>
      <script src="{{asset('public/admin/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js')}}"></script>
       <script src='https://code.jquery.com/jquery-1.12.3.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>       <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
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
      <script type="text/javascript" src="{{asset('public/js/admin.js').'?v=rtgfrw'}}"></script>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.1/js/standalone/selectize.js'></script>\
      <script>
          function disablebtn(){
                alert("This Action Disable In Demo");
            }
      </script>
      @yield('footer')
   </body>
</html>