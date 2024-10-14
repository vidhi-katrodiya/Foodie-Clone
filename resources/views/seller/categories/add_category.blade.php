@extends('seller.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4  float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.category')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li><a href="#">{{__('messages.product')}}</a></li>
               <li class="active">{{__('messages.category')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   
      <div class="col-12">
         <div class="card">
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

               <div class="content mt-3">
   <div class="row rowset">
      <div class="col-lg-6">
         <div class="card">
            <div class="card-header">
               <strong class="card-title">{{__('messages.category')}}</strong>
            </div>
            <div class="card-body">
               <div id="pay-invoice">
                  <div class="card-body">

                     <form action="{{url('seller/post_update_category')}}" method="post" enctype="multipart/form-data">
                       {{csrf_field()}}    

                         <input type="hidden" name="id" value="{{ isset($category->id)?$category->id:'0'}}">
                         <input type="hidden" name="old_image" value="{{ isset($category->image)?$category->image:''}}"> 
                        
                         <div class="form-group">
                           <label for="name" class=" form-control-label">
                              Category Name
                           <span class="reqfield">*</span>
                           </label>
                           <input type="text" required="" placeholder="Enter category name"  class="form-control" name="cat_name" value="{{ isset($category->cat_name)?$category->cat_name:''}}">
                        </div>   

                        <!-- <div class="form-group">
                           <label for="name" class=" form-control-label">
                              Category Image
                           <span class="reqfield">*</span>
                           </label>
                           @if(isset($category->image))
                           <img src="{{url('public/upload/category').'/'.$category->image}}" class="imgsize1"/>
                           <input type="file"  class="form-control" name="image">
                           @else
                           <input type="file" required=""  class="form-control" name="image">
                           @endif
                        </div> -->
                       
                        <div class="form-group">
                          <button id="payment-button" type="submit" class="btn btn-lg btn-info btn-block">
                           Save
                           </button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
              
               
         </div>
      </div>

</div>
@stop