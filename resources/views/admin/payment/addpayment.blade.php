@extends('admin.index')
@section('title')
{{__("messages.Payments")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.Send Payment To').' '.$seller_info->brand_name}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.Send Payment To').' '.$seller_info->brand_name}}</li>
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
               <strong class="card-title">{{__('messages.Send Payment To').' '.$seller_info->brand_name}}</strong>
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
                     <form action="{{url('admin/savechequepayment')}}" method="post"  id="paymentform">
                        {{csrf_field()}}                                     <input type="hidden" id="seller_id" name="seller_id" value="{{$seller_id}}">
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.Payment Amount')}}
                           <span class="reqfield">*</span>
                           </label>
                           <input type="text" id="amount" placeholder="{{__('messages.Payment Amount')}}" class="form-control" name="amount" readonly value="{{number_format($amount,2,'.','')}}">
                        </div>

                        <!-- <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.Payment Type')}}
                           <span class="reqfield">*</span>
                           </label>
                           <select name="payment_type" id="payment_type" class="form-control" required="" onchange="checkpaymenttype(this.value)">
                              <option value="">{{__("messages.Select Payment Type")}}</option>
                              <option value="cheque">{{__("messages.Cheque")}}</option>
                              @if($paymentmethod->status==1)
                                 <option value="paypal">{{__("messages.Paypal")}}</option>
                              @endif
                           </select>
                        </div>--->
                         <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.Payment Notes')}}
                           <span class="reqfield">*</span>
                           </label>
                           <input type="text" id="payment_note" placeholder="{{__('messages.Payment Notes')}}" class="form-control" name="payment_note" required value="">
                        </div>

                         <div class="form-group" id="div_main" style="display: none">
                           <label for="name" class=" form-control-label">
                           {{__('messages.DD No')}}
                           <span class="reqfield">*</span>
                           </label>
                           <input type="text" id="dd_no" placeholder="{{__('messages.DD No')}}" class="form-control" name="dd_no"  value="">
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
<input type="hidden" id="payment_error" value="{{__('messages.Please Select Payment Type')}}">
@stop