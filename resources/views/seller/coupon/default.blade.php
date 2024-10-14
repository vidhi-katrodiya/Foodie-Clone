@extends('seller.index') 
<style type="text/css">
.switch {
     display: inline-block;
}
 .switch input {
     display: none;
}
 .switch small {
     display: inline-block;
     width: 43px;
     height: 18px;
     background: #455a64;
     border-radius: 30px;
     position: relative;
     cursor: pointer;
}
 .switch small:after {
     content: "No";
     position: absolute;
     color: #fff;
     font-size: 11px;
     font-weight: 600;
     width: 100%;
     left: 0px;
     text-align: right;
     padding: 0 6px;
     box-sizing: border-box;
     line-height: 18px;
}
 .switch small:before {
     content: "";
     position: absolute;
     width: 12px;
     height: 12px;
     background: #fff;
     border-radius: 50%;
     top: 3px;
     left: 3px;
     transition: .3s;
     box-shadow: -3px 0 3px rgba(0,0,0,0.1);
}
 .switch input:checked ~ small {
     background: #4fc5c5;
     transition: .3s;
}
 .switch input:checked ~ small:before {
     transform: translate(25px, 0px);
     transition: .3s;
}
 .switch input:checked ~ small:after {
     content: "Yes";
     text-align: left;
}
</style>
@section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.coupon')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li class="active">{{__('messages.coupon')}}</li>
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
                <button onclick="addcoupon()" class="btn btn-primary btn-flat m-b-30 m-t-30">{{__('messages.add_coupon')}}</button>
                <div class="table-responsive dtdiv">
                    <table id="couponmainTable" class="table table-striped table-bordered dttablewidth">
                        <thead>
                            <tr>
                                <th>{{__('messages.id')}}</th>
                                <th>{{__('messages.name')}}</th>
                                <th>{{__('messages.code')}}</th>
                                <th>{{__('messages.date')}}</th>
                                <th>{{__('messages.value')}}</th>
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

<script type="text/javascript">
    function offer_status(id,status)
    {

      $.ajax({
          url: $("#url_path").val()+"/seller/offer_status",
          method:"POST",
          data: {"_token": "{{ csrf_token() }}","id":id,"status":status},
          success: function(data)
           {
                alert(data);
                location.reload();
           }
      });
    }
</script>