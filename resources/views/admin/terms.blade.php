@extends('admin.index')
@section('title')
{{__("messages.Trems")}} || {{__("messages.Admin")}}
@endsection
@section('meta-data')
@stop
@section('content')
<style>
    td.dataTables_empty {
    font-size: medium;
    font-weight: 600;
}
</style>
<div class="main-content">
  <div class="page-content">
    <div class="container-fluid">
      <div class="row">
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
              <h4>{{__("messages.term")}}</h4>
                <div class="content mt-3">
                  <div class="animated">
                    <div class="col-sm-12">
                      <div class="modal-content">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h6 class="modal-title">{{url('Privacy-Policy')}}</h6>
                              <a href= "{{url('Privacy-Policy')}}" class="btn btn-md btn-success" value="Visit" target="#" style="float:right; color:white !important">Visit</a>
                            
                          </div>
                          <div class="modal-body">
                            <form action="{{url('admin/edit_terms')}}" method="post" enctype="multipart/form-data">
                              {{csrf_field()}} 
                              <div class="form-group">
                                
                                <input type="hidden" class="form-control" id="id" name="id" required="" value="{{isset($data->id)?$data->id:0}}">

                                <textarea class="form-control" name="trems">{{isset($data->trems)?$data->trems:''}}</textarea>
                                
                              </div>
                              @if(Session::get("is_demo")=='1')
                                  <button type="button" onclick="disablebtn()" class="btn btn-success">{{__('messages.Submit')}}</button>
                              @else
                                   <button  class="btn btn-success" type="submit" value="Submit">{{__("messages.Submit")}}</button>
                              @endif
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>          
            </div>
          </div>
        </div> 
      </div> 
    </div> 
  </div>
</div>

<script src="{{asset('public/js/vendor/jquery-2.1.4.min.js')}}"></script>
 <script src="{{asset('public/ckeditor/ckeditor.js')}}"></script>
  <script type="text/javascript">
    $(document).ready(function () 
      {
          CKEDITOR.replace('trems');
      });
  </script>
@stop
@section('footer')
@stop