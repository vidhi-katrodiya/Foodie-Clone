<!doctype html>
<html lang="en">
  <!-- Mirrored from askbootstrap.com/preview/osahan-eat/theme-2/register.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 23 Nov 2022 12:18:58 GMT -->
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Askbootstrap">
    <meta name="author" content="Askbootstrap">
    <title>Register User || Foodieclone</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
     <link href="{{asset('public/front/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/fontawesome/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/icofont/icofont.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/vendor/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/front/css/osahan.css')}}" rel="stylesheet">
  </head>
  <body class="bg-white">
    <div class="container-fluid">
      <div class="row no-gutter">
        <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
        <div class="col-md-8 col-lg-6">
          <div class="login d-flex align-items-center py-5">
            <div class="container">
              <div class="row">
                <div class="col-md-9 col-lg-8 mx-auto pl-5 pr-5">
                  <h3 class="login-heading mb-4">New Buddy!</h3>
                  <form action="{{route('registed_user')}}" method="post" oninput='up2.setCustomValidity(up2.value != password.value ? "Passwords do not match." : "")'>
                    <input type="hidden" value="{{ csrf_token() }}" name="_token">
                    <div class="form-label-group">
                      <input type="text" name="first_name" value="{{old('first_name')}}" id="first_name" class="form-control" placeholder="Name" >
                      <label for="inputName">Name </label>
                    </div>@if ($errors->has('first_name'))
                              <span class="text-danger">{{ $errors->first('first_name') }}</span>
                            @endif
                    <div class="form-label-group">
                      <input type="email" name="email" value="{{old('email')}}" id="inputEmail" class="form-control" placeholder="Email address" >
                      <label for="inputEmail">Email address </label>
                    </div> @if ($errors->has('email'))
                              <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                    <div class="form-label-group">
                      <input type="password" name="password" value="{{old('password')}}" id="inputPassword" class="form-control" placeholder="Password" required>
                      <label for="inputPassword">Password</label>
                    </div>
                    <div class="form-label-group">
                        <label for="pwd" class="col-form-label">Confirm Password:</label>
                        <input type="password" name="up2" class="form-control" id="pwd" name="password">
                    </div>
                    <div class="form-check form-check-inline" required>
                        <input class="form-check-input" type="radio" name="gender" id="gender" value="Male">
                        <label class="form-check-label" for="gender">Male</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender" value="Female">
                        <label class="form-check-label" for="gender">Female</label>
                    </div><br><br>
                    
                    <div class="form-label-group">
                      <input type="date" name="dob" id="first_name" class="form-control" placeholder="Birth Of Date" value="{{old('dob')}}">
                      <label for="inputName">DOB </label>
                    </div>
                    <div class="form-label-group">
                      <input type="number" name="phone" id="first_name" class="form-control" placeholder="Mobile No." value="{{old('phone')}}">
                      <label for="inputName">Mobile No. </label>
                    </div>@if ($errors->has('phone'))
                              <span class="text-danger">{{ $errors->first('phone') }}</span>
                            @endif
                    </div>
                    <button class="btn btn-lg btn-outline-primary btn-block btn-login text-uppercase font-weight-bold mb-2" style="margin-left:152px; margin-right:150px;">Sign Up</button>
                    <div class="text-center pt-3" style="margin-left:220px;"> Already have an Account? <a class="font-weight-bold" href="{{route('login_user')}}">Sign In</a>
                    </div>
                  </form>
                </div>
              </div>
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
  <!-- Mirrored from askbootstrap.com/preview/osahan-eat/theme-2/register.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 23 Nov 2022 12:18:58 GMT -->
</html>