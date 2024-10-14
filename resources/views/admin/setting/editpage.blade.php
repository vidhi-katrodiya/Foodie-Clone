@extends('admin.index')
@section('title')
{{__("messages.Edit Settings")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.edit_page')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.edit_page')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="rowset">
      <div class="col-md-9">
         <div class="card">
            <div class="card-header">
               <strong class="card-title">{{__('messages.edit_page')}}</strong>
            </div>
            <div class="card-body">
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
                     <form action="{{url('admin/updatepage')}}" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}

                         <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                       <li class="nav-item">
                                             <a class="nav-link {{$i==0?'active':''}}" id="edit{{$l->code}}-edit-tab" data-toggle="tab" href="#{{$l->code}}" role="tab" aria-controls="{{$l->code}}" aria-selected="true">{{$l->name}}</a>
                                       </li>
                                       <?php $i++; ?>
                                    @endforeach
                                 </ul>
                                 <div class="tab-content p-1" id="myTabContent">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                        <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-edit-tab">
                                            <div class="form-group">
                                                <label for="name" class=" form-control-label">
                                                {{__('messages.page_name')}}
                                                <span class="reqfield">*</span>
                                                </label>
                                                <?php $name = "page_name_".$l->code; ?>
                                                <input type="text" id="page_name_{{$l->code}}" placeholder="{{__('messages.page_name')}}" class="form-control" name="page_name_{{$l->code}}" required value="{{isset($data->$name)?$data->$name:''}}">
                                             </div>
                                             <div class="form-group">
                                                <label for="name" class=" form-control-label">
                                                {{__('messages.description')}}
                                                <span class="reqfield">*</span>
                                                </label>
                                                <?php $name = "description_".$l->code; ?>

                                                <textarea class="form-control" name="description_{{$l->code}}" id="description_{{$l->code}}" placeholder=" {{__('messages.description')}}">{{isset($data->$name)?$data->$name:''}}</textarea>
                                             </div>
                                        </div>
                                       <?php $i++; ?>
                                    @endforeach
                                </div>
                        <input type="hidden" name="id" id="id" value="{{$data->id}}"/>
                       
                        <div>
                              @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                               {{__('messages.save')}}
                                        </button>
                                     @else
                                          <button class="btn btn-primary florig" type="submit"> {{__('messages.update')}}</button>
                                     @endif 
                         
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">

  <?php foreach($lang as $l){ ?>
   CKEDITOR.replace('description_<?=$l->code?>');

   <?php } ?>
</script>
@stop