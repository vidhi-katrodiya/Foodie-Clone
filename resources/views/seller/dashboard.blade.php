@extends('seller.index')
@section('content')
<div class="breadcrumbs">
      <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.dashboard')}}</h1>
         </div>
      </div>
   </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.dashboard')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3 sale">
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-money text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text">{{__('messages.total_sale')}}</div>
                  <div class="stat-digit">
                     {{$currency}} {{$total_sell}}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-shopping-cart text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text">{{__('messages.total_order')}}</div>
                  <div class="stat-digit">{{$total_order}}</div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-bar-chart text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text">{{__('messages.total_product')}}</div>
                  <div class="stat-digit">{{$total_product}}</div>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-bar-chart text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text">{{__('messages.Current Payment')}}</div>
                  <div class="stat-digit">{{$currency}} {{$total_Current_Sales}}</div>
               </div>
            </div>
         </div>
      </div>
   </div>
  
</div>


@stop