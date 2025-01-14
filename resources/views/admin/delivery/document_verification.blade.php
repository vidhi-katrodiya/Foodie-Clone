@extends('admin.index')
@section('title')
{{__("messages.Varify Documents")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.Document Verification')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.Document Verification')}}</li>
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
                  <table id="documentverificationtable" class="table table-striped table-bordered dttablewidth" >
                     <thead>
                        <tr>
                           <th>{{__('messages.id')}}</th>
                           <th>{{__('messages.name')}}</th>
                           <th>{{__('messages.document type')}}</th>
                           <th>{{__('messages.document')}}</th>
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