
@extends('front.layout')

@section('title')
{{__("messages.My Profile")}}
@endsection

@section('content')
<style type="text/css">
    .img-fluid{
    width: 508px !important;
    height: 186px;
  }

</style>

<section class="section pt-4 pb-4 osahan-account-page">
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <div class="osahan-account-page-left shadow-sm rounded bg-white h-100">
          <div class="border-bottom p-4">
            <div class="osahan-user text-center">
              <div class="osahan-user-media">
                @if(Auth::id())
                <img class="mb-3 rounded-pill shadow-sm mt-1" src="{{asset('public/front/img/user/'.$data->profile_pic)}}" alt="gurdeep singh osahan">
                <div class="osahan-user-media-body">
                  <h6 class="mb-2">{{$data->first_name}}</h6>
                  <p class="mb-1">{{$data->phone}}</p>
                  <p>{{$data->email}}</p>
                  <p class="mb-0 text-black font-weight-bold">
                    <a class="text-primary mr-3" data-bs-toggle="modal" data-bs-target="#edit-profile-modal" >
                      <i class="icofont-ui-edit"></i> EDIT </a>
                  </p>
                </div>
                @endif
              </div>
            </div>
          </div>
          <ul class="nav nav-tabs flex-column border-0 pt-4 pl-4 pb-4" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="true">
                <i class="icofont-food-cart"></i> Orders </a>
            </li>
           <!--  <li class="nav-item">
              <a class="nav-link" id="offers-tab" data-bs-toggle="tab" href="#offers" role="tab" aria-controls="offers" aria-selected="false">
                <i class="icofont-sale-discount"></i> Offers </a>
            </li> -->
            <li class="nav-item">
              <a class="nav-link" id="favourites-tab" data-bs-toggle="tab" href="#favourites" role="tab" aria-controls="favourites" aria-selected="false">
                <i class="icofont-heart"></i> Favourites </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="addresses-tab" data-bs-toggle="tab" href="#addresses" role="tab" aria-controls="addresses" aria-selected="false">
                <i class="icofont-location-pin"></i> Addresses </a>
            </li>
            <li class="nav-item">
              <a class="nav-link"  href="userlogout" aria-selected="false">
                <i class="icofont-sign-out"></i> Logout </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-md-9">
        <div class="osahan-account-page-right rounded shadow-sm bg-white p-4 h-100">
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
              <h4 class="font-weight-bold mt-0 mb-4">Past Orders</h4>
              <div class="order_data">
              </div>
              
            </div>
            <div class="tab-pane fade" id="offers" role="tabpanel" aria-labelledby="offers-tab">
              <h4 class="font-weight-bold mt-0 mb-4">Offers</h4>
              <div class="row mb-4 pb-2 offer_data">
                
              </div>
            </div>
            <div class="tab-pane fade" id="favourites" role="tabpanel" aria-labelledby="favourites-tab">
              <h4 class="font-weight-bold mt-0 mb-4">Favourites</h4>
              <div class="row favourit_data">
                
              </div>
            </div>
            <div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
              @if(Auth::id())
              <a href="{{url('add_address/'.$data->id)}}" type="button"  class="btn btn-sm btn-primary mr-2" style="float:right;">ADD NEW ADDRESS</a>
              @else
              <a  data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-sm btn-primary mr-2" type="button" style="float:right;">ADD NEW ADDRESS </a> 
              @endif
              <h4 class="font-weight-bold mt-0 mb-4">Manage Addresses</h4>

              <div class="row address_data">
                
                
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="edit-profile-modal" tabindex="-1" role="dialog" aria-labelledby="edit-profile" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="edit-profile">Edit profile</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @if(Auth::id())
        <form method="post" action="{{route('edit_profile')}}" enctype="multipart/form-data">
        @csrf
            <div class="form-row">
                <div class="form-group col-md-4" >
                    <div class="upload-btn-wrapper">
                       <button class="btn imgcatlog">
                       <input type="hidden" name="real_basic_img" id="real_basic_img" value="<?= isset($data->image)?$data->image:""?>"/>
                       <?php 
                          if(Auth::user()->profile_pic!=""){
                              $path=asset('public/front/img/user/') .'/'. Auth::user()->profile_pic;
                          }
                          else{
                              $path=asset('public/front/img/user/default_user.png');
                          }
                          ?>
                       <img src="{{$path}}" alt="..." class="img-thumbnail imgsize"  id="basic_img" style="height:120px;">
                       </button>
                        <input type="hidden" name="basic_img" id="basic_img1"/>
                   </div>
               </div> 
               <div class="form-group col-md-8" >
                   
                   <label>Profile Pic </label>
                   @if(Auth::user()->profile_pic!="")
                   <input type="file" name="image" id="upload_image" class="form-control" />
                   @else
                    <input type="file" class="form-control" required="" name="image" id="upload_image" />
                   @endif
                </div>
            </div>
            
            <div class="form-group col-md-12">
              <label>Phone number </label>
              <input type="number" value="{{$data->phone}}" name="phone" class="form-control" placeholder="Enter Phone number">
            </div>
            <div class="form-group col-md-12">
              <label>Email id </label>
              <input type="text" value="{{$data->email}}"  name="email" class="form-control" placeholder="Enter Email id">
              @if ($errors->has('email'))
                <span class="text-danger">{{ $errors->first('email') }}</span>
              @endif
            </div>
            <div class="form-group col-md-12 mb-0">
              <label>Password </label>
              <input type="password" value="{{$data->password}}"  name="password" class="form-control" placeholder="Enter password">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn d-flex w-50 text-center justify-content-center btn-outline-primary" data-bs-dismiss="modal">CANCEL </button>
            <button type="submit" class="btn d-flex w-50 text-center justify-content-center btn-primary">UPDATE</button>
          </div>
        </form>
        @endif
      </div>
      
    </div>
  </div>
</div>

<div class="modal fade" id="delete-address-modal" tabindex="-1" role="dialog" aria-labelledby="delete-address" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">

          <form  id="basicform" method="post" action="{{route('delete_address')}}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="delete-address">Delete</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <input type="hidden" value="" class="address_id" name="address_id">
              <p class="mb-0 text-black">Are you sure you want to delete this Address..?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn d-flex w-50 text-center justify-content-center btn-outline-primary" data-bs-dismiss="modal">CANCEL </button>
              <button type="submit" class="btn d-flex w-50 text-center justify-content-center btn-primary">DELETE</button>
            </div>
          </form>
          
        </div>
      </div>
</div>
@endsection

@section('script')
<script type="text/javascript">


$(document).ready(function(){
    favourit_data(1);
    offer_data(1);
    address_data(1);
    order_data(1);

});


</script>
@endsection   
  