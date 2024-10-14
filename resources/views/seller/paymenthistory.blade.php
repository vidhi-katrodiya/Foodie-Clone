@extends('seller.index')
@section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.payment history')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li class="active">{{__('messages.payment history')}}</li>
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
                        <table id="sellerpaymenthistory" class="table table-striped table-bordered dttablewidth">
                            <thead>
                                <tr>
                                    <th>{{__("messages.id")}}</th>
                                    <th>{{__("messages.date")}}</th>
                                    <th>{{__("messages.Total Amount")}}</th>
                                    <th>{{__("messages.Notes")}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@stop