@extends('admin.index')
@section('title')
{{__("messages.Orders")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.orders')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">

                    <li class="active">{{__('messages.orders')}}</li>
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

                    <div class="table-responsive dtdiv">
                        <table id="ordercustomerTable" class="table table-striped table-bordered dttablewidth">
                            <thead>
                                <tr>
                                    <th>{{__('messages.order_id')}}</th>
                                    <th>{{__('messages.customer')}} {{__('messages.name')}}</th>
                                    <th>{{__('messages.payment_method')}}</th>
                                    <th>{{__('messages.shipping_method')}}</th>
                                    <th>{{__('messages.total')}}</th>
                                    <th>{{__('messages.view')}}</th>
                                    <th>{{__('messages.status')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
<div class="modal fade" id="assignorder" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">{{__('messages.ass_header')}}
               </h5>
               <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
               <form name="menu_form_category" action="{{url('admin/assignorder')}}" method="post" enctype="multipart/form-data">
                  {{csrf_field()}}
                  <div class="form-group">
                     <label>{{__('messages.order_id')}}</label>
                     <input type="text" class="form-control" placeholder="{{__('messages.order_id')}}" name="id" id="order_id" readonly>
                  </div>
                  <div class="form-group">
                     <label>{{__('messages.sel_del_boy')}}</label>
                     <select class="form-control" name="assign_id" required>
                        <option value="">{{__('messages.sel_del_boy')}}</option>
                        @foreach($delivery as $c)
                        <option value="{{$c->id}}">{{$c->name}}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="col-md-12">
                     <div class="col-md-6">
                        <input type="submit" name="add_menu_cat"  class="btn btn-primary btn-md form-control" value="{{__('messages.add')}}">
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