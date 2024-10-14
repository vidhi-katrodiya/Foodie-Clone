@extends('admin.index')
@section('title')
{{__("messages.Save Banner")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.savebanner')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li><a href="{{url('brand').'/'.$category_id}}">{{__('messages.back')}}</a></li>
               <li class="active">{{__('messages.savebanner')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="row rowset">
      <div class="col-lg-6">
         <div class="card">
            <div class="card-header">
               <strong class="card-title">{{__('messages.savebanner')}}</strong>
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
                     <form action="{{url('admin/updatebarndbanner')}}" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}    
                         <input type="hidden" name="brand_id" value="{{$brand_id}}">
                         <input type="hidden" name="category_id" value="{{$category_id}}">                             
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                              {{__('messages.Banner')}}
                           <span class="reqfield">*</span>
                           </label>
                           @if($data->image!="")
                              <img src="{{asset('public/upload/category/banner').'/'.$data->image}}">
                           @endif
                           <input type="file" required id="banner"  class="form-control" name="banner" required="" >
                        </div>
                       
                        <div class="form-group">
                           
                            @if(Session::get("is_demo")=='1')
                                 <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-lg btn-info btn-block">
                                    {{__('messages.update')}}
                                </button>
                                @else
                                  <button id="payment-button" type="submit" class="btn btn-lg btn-info btn-block">
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