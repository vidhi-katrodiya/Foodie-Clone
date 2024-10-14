@extends('admin.index') 
@section('title')
{{__("messages.City")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
     <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.city')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li class="active">{{__('messages.city')}}</li>
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
                    <div class="col-md-12">
                            <button class="btn btn-primary btn-flat m-b-30 m-t-30" data-toggle="modal" data-target="#addcategorymodal">{{__('messages.add_city')}}</button>
                            <div class="table-responsive dtdiv">
                                <table id="Citydatatable" class="table table-striped table-bordered dttablewidth">
                                    <thead>
                                        <tr>
                                            <th>{{__('messages.id')}}</th>
                                            <th>{{__('messages.name')}}</th>
                                            <th>{{__('messages.action')}}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                </div>
            </div>
     
        </div>

</div>
<div class="modal fade" id="addcategorymodal" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smallmodalLabel">{{__('messages.add_city')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{url('admin/addcity')}}" method="post">
                {{csrf_field()}}
                <div class="modal-body">
                    @foreach($lang as $l)
                    <div class="form-group">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.name')}} ({{$l->name}})</label>
                        <input  name="name_{{$l->code}}" type="text" class="form-control" aria-required="true" aria-invalid="false" value="" required placeholder="{{__('messages.name')}}">
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                    @if(Session::get("is_demo")=='1')
                            <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary">
                                    {{__('messages.submit')}}
                           </button>
                      @else
                            <button type="submit" class="btn btn-primary">
                                {{__('messages.submit')}}
                            </button>
                     @endif
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editcategory" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smallmodalLabel">{{__('messages.edit_city')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{url('admin/updatecity')}}" method="post">
                {{csrf_field()}}
                <input type="hidden" name="id" id="id" />
                <div class="modal-body">
                    @foreach($lang as $l)
                    <div class="form-group">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.name')}} ({{$l->name}})</label>
                        <input id="name_{{$l->code}}" required name="name_{{$l->code}}" type="text" class="form-control" aria-required="true" aria-invalid="false" value="" placeholder="{{__('messages.name')}}">
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                      @if(Session::get("is_demo")=='1')
                            <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary">
                                    {{__('messages.update')}}
                           </button>
                      @else
                            <button type="submit" class="btn btn-primary">
                                {{__('messages.update')}}
                            </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@stop