@extends('admin.index')
@section('title')
{{__("messages.Language")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.Langauge')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.Langauge')}}</li>
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
               <button  class="btn btn-primary btn-flat m-b-30 m-t-30" data-toggle="modal" data-target="#myModal">{{__('messages.add')}} {{__('messages.Langauge')}}</button>
               <div class="table-responsive dtdiv">
                  <table id="language_table" class="table table-striped dttablewidth">
                     <thead>
                        <tr>
                           <th>{{__('messages.id')}}</th>
                           <th>{{__('messages.Name')}}</th>
                           <th>{{__('messages.Code')}}</th>
                           <th>{{__('messages.image')}}</th>
                           <th>{{__('messages.is_rtl')}}</th>
                           <th>{{__('messages.action')}}</th>
                        </tr>
                     </thead>
                  </table>
               </div>
            </div>
         </div>
      </div>
  
</div>
<div class="modal fade" id="myModal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">{{__('messages.add')}} {{__('messages.Langauge')}}
               </h5>
               <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
               <form name="menu_form_category" action="{{url('admin/add_language')}}" method="post" enctype="multipart/form-data">
                  {{csrf_field()}}
                 
                  <div class="form-group">
                     <label>{{__('messages.Name')}}</label>
                     <input type="text" class="form-control" placeholder="{{__('messages.Name')}}" name="name" required>
                  </div>
                  <div class="form-group">
                     <label>{{__('messages.Code')}}</label>
                     <input type="text" class="form-control" placeholder="{{__('messages.Code')}}" name="code" required>
                  </div>
                  <div class="form-group">
                     <label>{{__('messages.image')}}</label>
                     <input type="file" class="form-control" name="file" id="file" required="" />
                  </div>
                  <div class="form-group">
                     <label>{{__('messages.is_rtl')}}</label>
                     <select name="is_rtl" class="form-control">
                         <option value="">{{__('messages.Select Rtl')}}</option>
                         <option value="1">{{__("messages.Yes")}}</option>
                         <option value="0">{{__("messages.No")}}</option>
                     </select>
                  </div>
                
                  <div class="col-md-12">
                     <div class="col-md-6">
                            @if(Session::get("is_demo")=='1')
                                  <button type="button" onclick="disablebtn()" class="btn btn-primary btn-md form-control">{{__('messages.Submit')}}</button>
                            @else
                                   <button id="payment-button" type="submit" class="btn btn-primary btn-md form-control">
                                   {{__('messages.add')}}
                                   </button>
                            @endif
                            
                          
                     </div>
                     <div class="col-md-6">
                        <input type="button" class="btn btn-secondary btn-md form-control" data-dismiss="modal" value="{{__('messages.close')}}">
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

@stop