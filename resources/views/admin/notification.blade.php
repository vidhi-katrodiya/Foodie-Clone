@extends('admin.index') 
@section('title')
{{__("messages.Notification")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<style type="text/css">

</style>
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
        <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.notification')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
        <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li>                     
                         {{__('messages.notification')}}                 
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
                
                <button class="btn btn-primary btn-flat m-b-30 m-t-30" data-toggle="modal" data-target="#addsubcategorymodal">
                    {{__('messages.notification')}}
                </button>
                <div class="table-responsive dtdiv">
                    <table id="notificationTable" class="table table-striped table-bordered dttablewidth">
                        <thead>
                            <tr>
                                <th>{{__('messages.id')}}</th>
                                <th>{{__('messages.msg')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="addsubcategorymodal" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smallmodalLabel">
               {{__('messages.add_notification')}}
            </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{url('admin/sendnotification')}}" method="post" enctype="multipart/form-data">
               {{csrf_field()}}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cc-payment" class="control-label mb-1">
                            {{__('messages.Notification Type')}}
                        </label>
                        <select  class="form-control" id="type" name="type" required="" onchange="changenotificationtype(this.value)">
                             <option value="0">Normal</option>
                             <option value="1">Product</option>
                        </select>
                    </div>
                    <div id="normalnotification">
                    <div class="form-group" >
                        <label for="cc-payment" class="control-label mb-1">
                            {{__('messages.msg')}}
                        </label>
                        <textarea id="msg" name="msg" class="form-control" aria-required="true" aria-invalid="false" placeholder="{{__('messages.msg')}}" required=""></textarea>
                    </div>
                    <div class="form-group">
                           <label for="file" class=" form-control-label">  
                           {{__('messages.profile_picture')}}
                           </label>
                           <div>
                              <input type="file" id="image" name="image" accept="image/*" class="form-control-file">
                           </div>
                    </div>
                    </div>

                    <div class="form-group" id="productnotification" style="display: none">
                        <label for="cc-payment" class="control-label mb-1">
                            {{__('messages.Search Product')}}
                        </label>
                        <select  class="form-control productselect select2"  id="product_id" name="product_id"  style="width: 100%">
                            @foreach($prdouct as $p)
                                <option value="{{$p->id}}">{{$p->name}}</option>
                            @endforeach
                            <option>sdfdsf</option>
                            <option>abc</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{__('messages.cancel')}}
                    </button>
                             @if(Session::get("is_demo")=='1')
                                     <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-lg btn-info btn-block">
                                              {{__('messages.update')}}
                                    </button>
                                @else
                                     <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                                @endif
                   
                </div>
            </form>
        </div>
    </div>
</div>

@stop