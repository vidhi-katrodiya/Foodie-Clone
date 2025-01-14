@extends('admin.index')
@section('title')
{{__("messages.Add Special")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.add_sepical_category')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.add_sepical_category')}}</li>
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
               <strong class="card-title">{{__('messages.add_sepical_category')}}</strong>
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
                     <form action="{{url('admin/storesepicalcategory')}}" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                      
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
                           <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-tab">
                              <div class="form-group">
                                 <label for="name" class=" form-control-label">
                                 {{__('messages.title')}}
                                 <span class="reqfield">*</span>
                                 </label>
                                 <input type="text" id="title_{{$l->code}}" placeholder="{{__('messages.title')}}" class="form-control" name="title_{{$l->code}}" required>
                              </div>
                           
                              <div class="form-group">
                                 <label for="name" class=" form-control-label">
                                 {{__('messages.description')}}
                                 <span class="reqfield">*</span>
                                 </label>
                                 <textarea class="form-control" name="description_{{$l->code}}" id="description_{{$l->code}}" placeholder="{{__('messages.description')}}" required></textarea>
                              </div>
                           </div>
                           <?php $i++; ?>
                        @endforeach
                     </div>
                     <div class="form-group col-md-12 paddiv">
                           <label for="name" class=" form-control-label">
                           {{__('messages.cate_gory')}}
                           <span class="reqfield">*</span>
                           </label>
                           <select name="category" id="categoryss" class="form-control" required >
                              <option value="">{{__('messages.select_category')}}</option>
                              @foreach($category as $ca)
                              <option value="{{$ca->id}}">{{$ca->name}}</option>
                              @endforeach
                           </select>
                        </div>
                        <div class="form-group">
                           <label for="name" class=" form-control-label">
                           {{__('messages.image')}}(542X370)
                           <span class="reqfield">*</span>
                           </label>
                           <input type="file" id="image" accept="image/*" class="form-control" name="image" required>
                        </div>
                        <div>
                            @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                               {{__('messages.save')}}
                                        </button>
                           @else
                              <button type="submit" class="btn btn-primary florig">{{__('messages.submit')}}</button>
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