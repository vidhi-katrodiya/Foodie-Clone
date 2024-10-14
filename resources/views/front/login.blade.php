<!doctype html>
<html lang="en">
  <!-- Mirrored from askbootstrap.com/preview/osahan-eat/theme-2/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 23 Nov 2022 12:18:58 GMT -->
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Askbootstrap">
    <meta name="author" content="Askbootstrap">
    <title>Osahan Eat - Online Food Ordering Website HTML Template</title>
    <link rel="icon" type="{{asset('public/front/image/png')}}" href="img/favicon.png">
    <link href="{{asset('public/front/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/fontawesome/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/icofont/icofont.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/css/osahan.css')}}" rel="stylesheet">
    <style type="text/css">
        .hr-text {
        line-height: 1em;
        position: relative;
        outline: 0;
        border: 0;
        color: #5A6081;
        text-align: center;
        height: 1.5em;
       
      }
     .hr-text:before {
          content: '';
          
          background: linear-gradient(to right, transparent, #818078, transparent);
          position: absolute;
          left: 0;
          top: 50%;
          width: 100%;
          height: 1px;
        }
       .hr-text:after {
          content: attr(data-content);
          position: relative;
          display: inline-block;
          color: black;

          padding: 0 .5em;
          line-height: 1.5em;
          
          color: #818078;
          background-color: #fcfcfa;
        }
    </style>
  </head>
  <body class="bg-white">
    <div class="container-fluid">
      <div class="row no-gutter">
        <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
        <div class="col-md-8 col-lg-6">
          <div class="login d-flex align-items-center py-5">
            <div class="container">
              @if (Session::has('error'))
                <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                  <ul>
                      <li>{{ Session::get('error') }}</li>
                  </ul>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>
                </div>
              @endif
              <div class="row">
                <div class="col-md-8 col-lg-8 mx-auto pl-6 pr-6">
                  <h3 class="login-heading mb-4">Welcome back!</h3>
                  <form action="{{route('PostLogin')}}" method="post">
                          <input type="hidden" value="{{ csrf_token() }}" name="_token">   
                    <div class="form-label-group">
                      <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address">
                      <label for="inputEmail">Email Address </label>
                    </div>
                    <div class="form-label-group">
                      <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password">
                      <label for="inputPassword">Password</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-3">
                      <input type="checkbox" class="custom-control-input" id="customCheck1">
                      <label class="custom-control-label" for="customCheck1">Remember password</label>
                    </div>
                    <button class="btn btn-lg btn-outline-primary btn-block btn-login text-uppercase font-weight-bold mb-2">Sign in</button>
                    
                  </form>
                  
                  
                </div>
              </div>
                <hr class="hr-text" data-content="New to Foodiclone?">
                <a class="btn btn-lg btn-outline-primary btn-block btn-login text-uppercase font-weight-bold col-md-8 col-lg-8 mx-auto pl-6 pr-6" href="{{route('register')}}" >Create Your Account </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="{{asset('public/front/vendor/jquery/jquery-3.3.1.slim.min.js')}}" type="cb2ea4636cd467db4d875fd3-text/javascript"></script>
    <script src="{{asset('public/front/vendor/bootstrap/js/bootstrap.bundle.min.js')}}" type="cb2ea4636cd467db4d875fd3-text/javascript"></script>
    <script src="{{asset('public/front/vendor/select2/js/select2.min.js')}}" type="cb2ea4636cd467db4d875fd3-text/javascript"></script>
    <script src="{{asset('public/front/js/custom.js')}}" type="cb2ea4636cd467db4d875fd3-text/javascript"></script>
    <script src="{{asset('public/front/../../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js')}}" data-cf-settings="cb2ea4636cd467db4d875fd3-|49" defer=""></script>
    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vaafb692b2aea4879b33c060e79fe94621666317369993" integrity="sha512-0ahDYl866UMhKuYcW078ScMalXqtFJggm7TmlUtp0UlD4eQk0Ixfnm5ykXKvGJNFjLMoortdseTfsRT8oCfgGA==" data-cf-beacon='{"rayId":"76e9d8ddf85cf2f7","version":"2022.11.3","r":1,"token":"dd471ab1978346bbb991feaa79e6ce5c","si":100}' crossorigin="anonymous"></script>
  </body>
  <!-- Mirrored from askbootstrap.com/preview/osahan-eat/theme-2/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 23 Nov 2022 12:18:58 GMT -->
</html>