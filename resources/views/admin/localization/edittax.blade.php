@extends('admin.index')
@section('title')
{{__("messages.Edit Tax")}} || {{__("messages.Admin")}}
@endsection
 @section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.EditTax')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.EditTax')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
<div class="rowset">
<div class="col-lg-8">
<div class="card">
   <div class="card-header">
      <strong class="card-title">{{__('messages.EditTax')}}</strong>
   </div>
   <div class="card-body">
      <div class="">
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
               <form action="{{url('admin/updatetaxdata')}}" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="id" id="id" value="{{$taxes_data->id}}" /> {{csrf_field()}}
                 
                   <input type="hidden" name="based_on" value="1"/>
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                       <li class="nav-item">
                                             <a class="nav-link {{$i==0?'active':''}}" id="{{$l->code}}-tab" data-toggle="tab" href="#{{$l->code}}" role="tab" aria-controls="{{$l->code}}" aria-selected="true">{{$l->name}}</a>
                                       </li>
                                       <?php $i++; ?>
                                    @endforeach
                                 </ul>
                                 <div class="tab-content p-1" id="myTabContent">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                        <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-tab">
                                            <div class="row form-group" style="margin-top: 15px">
                                                <div class="col col-md-3">
                                                   <label for="text-input" class=" form-control-label">{{__('messages.tax_name')}}<span class="reqfield">*</span></label>
                                                </div>
                                                 <?php $name = "tax_name_".$l->code;?>
                                                <div class="col-12 col-md-9">
                                                   <input type="text" id="tax_name_{{$l->code}}" placeholder="{{__('messages.tax_name')}}" class="form-control" name="tax_name_{{$l->code}}" value="{{$taxes_data->$name}}" required>
                                                </div>
                                             </div>                                           
                                        </div>
                                       <?php $i++; ?>
                                    @endforeach
                                </div>
                  <div class="row form-group">
                     <div class="col col-md-3">
                        <label for="text-input" class=" form-control-label">{{__('messages.rate')}}(%)<span class="reqfield">*</span></label>
                     </div>
                     <div class="col-12 col-md-9">
                        <input  type="number" id="rate" step="any" placeholder="{{__('messages.rate')}}" class="form-control" name="rate" value="{{$taxes_data->rate}}" required>
                     </div>
                  </div>
                 
                  <div>
                       @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                               {{__('messages.update')}}
                                       </button>
                                  @else
                                         <button class="btn btn-primary florig" type="submit">{{__('messages.update')}}</button>
                                  @endif
                    
                  </div>
            </div>
         </div>
        </form>
      </div>
   </div>
</div>
@stop