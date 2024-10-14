@extends('seller.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4  float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.catalog')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li><a href="#">{{__('messages.product')}}</a></li>
               <li class="active">{{__('messages.catalog')}}</li>
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
               <button  class="btn btn-primary btn-flat m-b-30 m-t-30" onclick="addcatlog(<?= $id?>)" >{{__('messages.add')}} {{__('messages.catalog')}}</button>
               <input type="hidden" name="cat_id" id="cat_id" value="{{$id}}">
               <div class="table-responsive cmr1">
                  <table id="productdataTable" class="table table-striped table-bordered dttablewidth" >
                     <thead>
                        <tr>
                           <th>{{__('messages.id')}}</th>
                           <th>{{__('messages.thumbnail')}}</th>
                           <th>{{__('messages.name')}}</th>
                           <th>{{__('messages.price')}}</th>
                           <th>{{__('messages.status')}}</th>
                           <!-- <th>{{__('messages.attribute')}}</th> -->
                           <th>{{__('messages.option')}}</th>
                           <th>{{__('messages.action')}}</th>
                        </tr>
                     </thead>
                  </table>
               </div>
            </div>
         </div>
      </div>

</div>
@stop