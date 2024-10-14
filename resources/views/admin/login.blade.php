<!doctype html>
<html class="no-js" lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>{{__('messages.site_name')}}</title>
      <meta name="description" content="Sufee Admin - HTML5 Admin Template">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="apple-touch-icon" href="apple-icon.png">
      <link rel="shortcut icon" href="{{asset('public/home_logo.png')}}">
      <link rel="stylesheet" href="{{url('public/admin/vendors/bootstrap/dist/css/bootstrap.min.css')}}">
      <link rel="stylesheet" href="{{url('public/admin/vendors/font-awesome/css/font-awesome.min.css')}}">
      <link rel="stylesheet" href="{{url('public/admin/vendors/themify-icons/css/themify-icons.css')}}">
      <link rel="stylesheet" href="{{url('public/admin/vendors/flag-icon-css/css/flag-icon.min.css')}}">
      <link rel="stylesheet" href="{{url('public/admin/vendors/selectFX/css/cs-skin-elastic.css')}}">
      <link rel="stylesheet" href="{{url('public/admin/assets/css/style.css')}}">
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
   </head>
   <body class="bg-dark">
      <div class="sufee-login d-flex align-content-center flex-wrap">
         <div class="container">
            <div class="login-content">
               <div class="login-logo">
                  <h4 class="sitecolor" style="font-size: 38px;">
                       Foodieclone
                     <font class="admincolor">
                     {{__('messages.admin')}}
                     </font>
                  </h4>
               </div>
               <div class="login-form">
                  <div id="respond" class="comment-respond">
                     @if(Session::has('message'))
                     <div class="col-sm-12">
                        <div class="alert  {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">{{ Session::get('message') }}
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                     </div>
                     @endif
                  </div>
                  <form action="{{url('admin/postlogin')}}" method="post">
                     {{csrf_field()}}
                     <div class="form-group">
                        <label>{{__('messages.email')}}</label>
                        <input type="email" class="form-control" placeholder="{{__('messages.email')}}" required name="email" id="email" value="admin@gmail.com">
                     </div>
                     <div class="form-group">
                        <label>{{__('messages.password')}}</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="{{__('messages.password')}}" value="123">
                     </div>
                     <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">
                     {{__('messages.sign_in')}}
                     </button>
                     <button type="button" onclick="opensellersignin()" style="margin-top: 15px" class="btn btn-secondary btn-flat m-b-30 m-t-30">
                     Sign In As restaurant
                     </button>
                     <div class="form-group" style="">
                        <a href="{{url('document')}}" style="color: blue !important;float: right;">{{__('messages.document')}}</a>
                     </div>
                  </form>
                   
               </div>
            </div>
         </div>
      </div>
      <script src="{{asset('public/admin/vendors/jquery/dist/jquery.min.js')}}"></script>
      <script src="{{asset('public/admin/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
      <script src="{{asset('public/admin/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
      <script src="{{asset('public/admin/assets/js/main.js')}}"></script>
      <script type="text/javascript">
         function opensellersignin(){
              window.location.href='{{url("sellerlogin")}}';
         }
      </script>
   </body>
</html>