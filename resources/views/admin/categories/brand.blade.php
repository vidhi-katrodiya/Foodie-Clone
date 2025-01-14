@extends('admin.index') 
@section('title')
{{__("messages.Brands")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
        <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.brands')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
        <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li>
                        <a href="{{url('admin/category')}}">
                  {{__('messages.category')}}
                  </a>
                    </li>
                    <li>
                        <a href="{{url('admin/category')}}">
                  {{$parent_name}}
                  </a>
                    </li>
                    <li>
                        <a href="{{url('admin/subcategory/'.$parent_ids)}}">
                  {{$subcategory}}
                  </a>
                    </li>
                    <li class="active">
                        {{__('messages.brands')}}
                    </li>
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
                    <div class="alert  {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">
                        {{ Session::get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                @endif
                <input type="hidden" name="parent_id" id="parent_id" value="{{$subcategoryid}}" />
                <button class="btn btn-primary btn-flat m-b-30 m-t-30" data-toggle="modal" data-target="#addsubcategorymodal">
                    {{__('messages.add_brands')}}
                </button>
                <div class="table-responsive dtdiv">
                    <table id="brandTable" class="table table-striped table-bordered dttablewidth">
                        <thead>
                            <tr>
                                <th>{{__('messages.id')}}</th>
                                <th>{{__('messages.name')}}</th>
                                <th>{{__('messages.Banner')}}</th>
                                <th>{{__('messages.city')}}</th>
                                <th>{{__('messages.action')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="addsubcategorymodal" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smallmodalLabel">
               {{__('messages.add_brands')}}
            </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{url('admin/addbrand')}}" method="post">
                <input type="hidden" name="category_id" id="parentid" value="{{$subcategoryid}}" />
                {{csrf_field()}}
                <div class="modal-body">
                     @foreach($lang as $l)
                    <div class="form-group">
                        <label for="cc-payment" class="control-label mb-1">
                            {{__('messages.brand_name')}} ({{$l->name}})
                        </label>
                        <input  name="name_{{$l->code}}" type="text" class="form-control" aria-required="true" aria-invalid="false" value="" placeholder="{{__('messages.brand_name')}}" required="">
                    </div>
                   @endforeach
                    <div class="form-group">
                        <label for="cc-payment" class="control-label mb-1">
                            {{__('messages.select city')}}
                        </label>
                        <select class="form-control" required name="city" id="city">
                            <option value="">{{__('messages.select city')}}</option>
                            @foreach($city as $c)
                                <option value="{{$c->id}}">{{$c->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                    
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{__('messages.cancel')}}
                    </button>
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
<div class="modal fade" id="editbrand" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smallmodalLabel">{{__('messages.edit_brand')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{url('admin/updatebrand')}}" method="post">
                {{csrf_field()}}
                <input type="hidden" name="id" id="id" />
                <div class="modal-body">
                     @foreach($lang as $l)
                         <div class="form-group">
                            <label for="cc-payment" class="control-label mb-1">
                                {{__('messages.brand_name')}} ({{$l->name}})
                            </label>
                            <input  name="name_{{$l->code}}" id="name_{{$l->code}}" type="text" class="form-control" aria-required="true" aria-invalid="false" value="" placeholder="{{__('messages.brand_name')}}" required="">
                        </div>
                    @endforeach
                     
                     <div class="form-group">
                        <label for="cc-payment" class="control-label mb-1">
                            {{__('messages.select city')}}
                        </label>
                        <select class="form-control" required name="city" id="edit_city">
                            <option value="">{{__('messages.select city')}}</option>
                            @foreach($city as $c)
                                <option value="{{$c->id}}">{{$c->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{__('messages.cancel')}}
                    </button>
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