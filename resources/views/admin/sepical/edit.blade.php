@extends('admin.index')

@section('title')
{{__("messages.Edit")}} || {{__("messages.Admin")}}
@endsection
@section('content') 
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.edit')}} {{__('messages.add_sepical_category')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.edit')}} {{__('messages.add_sepical_category')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="rowset">
      <div class="col-lg-8">
         <div class="card">
            <div class="card-header">
               <strong class="card-title">{{__('messages.edit')}} {{__('messages.add_sepical_category')}}</strong>
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


                     <form action="{{url('admin/updatesepicalcategory')}}" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$data->id}}" />
                        <input type="hidden" name="real_image" value="{{$data->image}}" />
                           <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <?php $i=0; ?>
                        @foreach($lang as $l)
                           <li class="nav-item">
                                 <a class="nav-link {{$i==0?'active':''}}" id="{{$l->code}}-tab" data-toggle="tab" href="#{{$l->code}}" role="tab" aria-controls="{{$l->code}}" aria-selected="true">{{$l->name}}</a>
                           </li>
                           <?php $i++; ?>
                        @endforeach
                     </ul>
                     <div class="tab-content p-1" id="myTabContent">
                        <?php $i=0; ?>
                        @foreach($lang as $l)
                           <?php $title = "title_".$l->code;  $description = "description_".$l->code; ?>
                           <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-tab">
                              <div class="form-group">
                                 <label for="name" class=" form-control-label">
                                 {{__('messages.title')}}
                                 <span class="reqfield">*</span>
                                 </label>
                                 <input type="text" id="title_{{$l->code}}" placeholder="{{__('messages.title')}}" class="form-control" name="title_{{$l->code}}" value="{{isset($data->$title)?$data->$title:''}}" required>
                              </div>
                           
                              <div class="form-group">
                                 <label for="name" class=" form-control-label">
                                 {{__('messages.description')}}
                                 <span class="reqfield">*</span>
                                 </label>
                                 <textarea class="form-control" name="description_{{$l->code}}" id="description_{{$l->code}}" placeholder="{{__('messages.description')}}" required>{{isset($data->$description)?$data->$description:''}}</textarea>
                              </div>
                           </div>
                           <?php $i++; ?>
                        @endforeach
                     </div>
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.cate_gory')}}
                           <span class="reqfield">*</span>
                           </label>
                           <select name="category" id="categorylh" class="form-control" required >
                              <option value="">{{__('messages.select_category')}}</option>
                              @foreach($category as $ca)
                              <option value="{{$ca->id}}" <?=$data->category_id ==$ca->id ? ' selected="selected"' : '';?>>{{$ca->name}}</option>
                              @endforeach
                           </select>
                        </div>
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.image')}}(542X370)
                           </label>
                           <img src="{{asset('public/upload/category/image').'/'.$data->image}}" class="imgsize1" />
                           <input type="file" id="image" class="form-control" accept="image/*" name="image" >
                        </div>
                     
                        
                        <div>
                           @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                               {{__('messages.save')}}
                                        </button>
                                     @else
                                         <button class="btn btn-primary florig" type="submit">{{__('messages.update')}}</button>
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
@stop