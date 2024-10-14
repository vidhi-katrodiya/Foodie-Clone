
@extends('seller.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.edit_profile')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.edit_profile')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="row rowset">
      <div class="col-lg-12">
         <div class="card">
            <div class="card-header">
               <strong class="card-title">{{__('messages.edit_profile')}}</strong>
            </div>
            <div class="card-body">
               <div id="pay-invoice">
                  <div class="card-body">
                     @if(Session::has('message'))
                     <div class="col-sm-12">
                        <div class="alert  {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">{{ Session::get('message') }}
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                     </div>
                     @endif
                     <form action="{{url('seller/updatesellerprofile')}}" method="post"  enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="row">
                           <div class="col-lg-6">
                                 <div class="form-group">
                                    <label for="name" class=" form-control-label">
                                    {{__('messages.first_name')}}
                                    <span class="florig">*</span>
                                    </label>
                                    <input type="text" id="name" placeholder="{{__('messages.first_name')}}" class="form-control" name="name" value="{{$data->first_name}}">
                                 </div>
                           </div>
                           <div class="col-lg-6">
                                 <div class="form-group">
                                    <label for="name" class=" form-control-label">
                                    Delivery Time
                                    <span class="florig">*</span>
                                    </label>
                                    <input type="time" id="delivery_time"  placeholder="Delivery Time" class="form-control" name="delivery_time" value="{{$data->delivery_time}}">
                              </div>
                           </div>  
                        </div>
                         <div class="row">
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label for="email" class=" form-control-label">
                                 {{__('messages.email')}}
                                 </label>
                                 <input type="text" readonly id="email" name="email" placeholder="{{__('messages.email')}}" class="form-control" value="{{$data->email}}">
                              </div>
                           </div>
                            <div class="col-lg-6">
                                 <div class="form-group">
                                    <label for="name" class=" form-control-label">
                                    Two person cost
                                    <span class="florig">*</span>
                                    </label>
                                    <input type="text" id="two_person_cost" placeholder="Two person cost" class="form-control" name="two_person_cost" value="{{$data->two_person_cost}}">
                                 </div>
                           </div>
                        </div>
                           <div class="row">
                             <div class="form-group col-md-6">
                                 <label for="cc-payment" class="control-label mb-1">{{__('messages.Access Category')}}</label>
                                 <select class="form-control" id="edit_access_cat" name="access_cat[]" required="" multiple>
                                     @php $selected = explode(",", $data->access_cat); @endphp
                                     @foreach($category as $cat)
                                        <option value={{$cat->id}} {{ (in_array($cat->id, $selected)) ? 'selected' : '' }}>{{$cat->cat_name}}</option>
                                     @endforeach
                                 </select>
                             </div>
                             <div class="form-group col-md-6">
                                 <div class="form-group">
                                    <label for="name" class=" form-control-label">
                                    Open to close time
                                    <span class="florig">*</span>
                                    </label>
                                    <input type="text" id="open_close" placeholder="Open to close time" class="form-control" name="open_close_time" value="{{$data->res_time}}">
                                 </div>
                             </div>
                          </div>
                         <div class="row">
                          
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label for="file" class=" form-control-label">  
                                 {{__('messages.profile_picture')}}
                                 </label>
                                 @if(isset($data->res_image))
                                 <img src="{{asset('public/upload/restaurant/'.'/'.$data->res_image)}}" class="imgsize1" />
                                @else
                                 <img src="{{asset('public/upload/dummy.png')}}" class="imgsize1" />
                                 @endif
                                 <div class="form-group">
                                    <input type="file" id="file"  name="file" class="form-control">
                                 </div>
                              </div>
                           </div>
                          
                        </div>
                     
                         <div class="form-group">
                           
                           <input type="hidden" id="address" placeholder="{{__('messages.address')}}" class="form-control" name="address" value="{{$data->address}}">
                        </div> 

                         <div class="col-md-6 p-0"  id="addressorder">
                           <label>Address<span class="reqfield">*</span></label>
                           <input  type="text" class="form-control" id="us2-address" name="address" placeholder='Address' required data-parsley-required="true" required=""/>
                        </div>
                        <div class="map" id="maporder" >
                           <div class="form-group">
                              <div class="col-md-12 p-0">
                                 <div id="us2"></div>
                              </div>
                           </div>
                        </div>
                        
                        <input type="hidden" name="latitude" id="us2-lat" value="{{isset($data->lat)?$data->lat:21.238904587891632}}" />
                        <input type="hidden" name="longitude" id="us2-lon" value="{{isset($data->long)?$data->long:72.88899169714026}}" />
                       
                        <input type="hidden" name="postalCode" id="postalCode" value="{{isset($data->pincode)?$data->pincode:''}}" />
                        <input type="hidden" name="area" id="area" value="{{isset($data->area)?$data->area:''}}" />
                        <input type="hidden" name="city" id="city" value="{{isset($data->city)?$data->city:''}}" />
                        <input type="hidden" name="country" id="country" value="{{isset($data->country)?$data->country:''}}" />
                         <input type="hidden" name="lat" id="us2-lat" value="{{isset($data->lat)?$data->lat:Config::get('mapdetail.lat')}}" />
                        <input type="hidden" name="lon" id="us2-lon" value="{{isset($data->long)?$data->long:Config::get('mapdetail.long')}}" /> 

                        
                        <div>
                             @if(Session::get("is_demo")=='1')
                                 <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-lg btn-info ">
                                    {{__('messages.update')}}
                                </button>
                                @else
                                  <button id="payment-button" type="submit" class="btn btn-lg btn-info ">
                           {{__('messages.update')}}
                           </button>
                                @endif
                          
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@stop