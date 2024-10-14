@extends('admin.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.quesans')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               @if($support==1)
               <li><a href="{{url('admin/support/1')}}">{{__('messages.helpsupport')}}</a></li>
               @endif
               @if($support==2)
               <li><a href="{{url('admin/support/2')}}">{{__('messages.termscon')}}</a></li>
               @endif
               <li class="active">{{__('messages.quesans')}}</li>
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
               <button  data-toggle="modal" data-target="#adduser" class="btn btn-primary btn-flat m-b-30 m-t-30" >{{__('messages.add')}} {{__('messages.quesans')}}</button>
               <div class="table-responsive dtdiv">
                  <table id="quesdatatable" class="table table-striped table-bordered dttablewidth">
                     <thead>
                        <tr>
                           <th>{{__('messages.id')}}</th>
                           <th>{{__('messages.ques')}}</th>
                           <th>{{__('messages.ans')}}</th>
                           <th>{{__('messages.action')}}</th>
                        </tr>
                     </thead>
                  </table>
               </div>
            </div>
         </div>
      </div>
</div>
<div class="modal fade" id="adduser" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="smallmodalLabel">{{__('messages.add')}} {{__('messages.topic')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{url('admin/addquesans')}}" method="post">
            {{csrf_field()}}
            <input type="hidden" name="support_id" id="support_id" value="{{$support}}"/>
            <input type="hidden" name="topic_id" id="topic_id" value="{{$topic}}"/>


            <div class="modal-body">
               <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                       <li class="nav-item">
                                             <a class="nav-link {{$i==0?'active':''}}" id="{{$l->code}}-tab" data-toggle="tab" href="#tab{{$l->code}}" role="tab" aria-controls="{{$l->code}}" aria-selected="true">{{$l->name}}</a>
                                       </li>
                                       <?php $i++; ?>
                                    @endforeach
                                 </ul>
                                 <div class="tab-content p-1" id="myTabContent">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                        <div class="tab-pane fade {{$i==0?'show active':''}}" id="tab{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-tab">
                                            <div class="form-group col-md-12 paddiv">
                                                <label for="cc-payment" class="control-label mb-1">{{__('messages.ques')}}
                                                <span class="reqfield">*</span>
                                                </label>
                                                <textarea  name="ques_{{$l->code}}" class="form-control h150" required  placeholder="{{__('messages.ques')}}"></textarea>
                                             </div>
                                             <div class="form-group col-md-12 paddiv">
                                                <label for="cc-payment" class="control-label mb-1">{{__('messages.ans')}}
                                                <span class="reqfield">*</span>
                                                </label>
                                                <textarea  name="ans_{{$l->code}}"  class="form-control h150" required  placeholder="{{__('messages.ans')}}"></textarea>
                                             </div>

                                        </div>
                                       <?php $i++; ?>
                                    @endforeach
                                </div>
                           </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                 @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.submit')}}
                                        </button>
                                     @else
                                            <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                                     @endif 
              
            </div>
         </form>
      </div>
   </div>
</div>
<div class="modal fade" id="editques" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="smallmodalLabel">
               {{__('messages.edit')}} {{__('messages.topic')}}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{url('admin/updatequestion')}}" method="post">
            {{csrf_field()}}
            <input type="hidden" name="id" id="edit_id" />
            <div class="modal-body">
<ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                       <li class="nav-item">
                                             <a class="nav-link {{$i==0?'active':''}}" id="{{$l->code}}-edit-tab" data-toggle="tab" href="#tabedit{{$l->code}}" role="tab" aria-controls="{{$l->code}}" aria-selected="true">{{$l->name}}</a>
                                       </li>
                                       <?php $i++; ?>
                                    @endforeach
                                 </ul>
                                 <div class="tab-content p-1" id="myTabContent">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                        <div class="tab-pane fade {{$i==0?'show active':''}}" id="tabedit{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-edit-tab">
                                            <div class="form-group col-md-12 paddiv">
                                                <label for="cc-payment" class="control-label mb-1">{{__('messages.ques')}}
                                                <span class="reqfield">*</span>
                                                </label>
                                                <textarea id="ques_{{$l->code}}" name="ques_{{$l->code}}" class="form-control h150" required  placeholder="{{__('messages.ques')}}"></textarea>
                                             </div>
                                             <div class="form-group col-md-12 paddiv">
                                                <label for="cc-payment" class="control-label mb-1">{{__('messages.ans')}}
                                                <span class="reqfield">*</span>
                                                </label>
                                                <textarea id="ans_{{$l->code}}" name="ans_{{$l->code}}"  class="form-control h150" required  placeholder="{{__('messages.ans')}}"></textarea>
                                             </div>

                                        </div>
                                       <?php $i++; ?>
                                    @endforeach
                                </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                 @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.submit')}}
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