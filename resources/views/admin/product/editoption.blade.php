@extends('admin.index') 
@section('title')
{{__("messages.Edit Product")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<div class="breadcrumbs">
    <div class="col-sm-4">
        <div class="page-header float-left">
            <div class="page-title">
                <h1>{{__('messages.option')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                   <li><a href="#">{{__('messages.product')}}</a></li>
                    <li><a href="{{url('admin/options')}}">{{__('messages.option')}}</a></li>
                    <li class="active">{{__('messages.edit')}} {{__('messages.option')}}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content mt-3">
    <div class="row rowset">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4>{{__('messages.edit')}} {{__('messages.option')}}</h4>
                </div>
                <div class="card-body">
                     <form action="{{url('admin/saveoption')}}" method="post">
                                {{csrf_field()}}
                                <input type="hidden" name="id" id="id" value="{{$option->id}}">
                            <div class="cmr1">
                                <div class="row">
                                    <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="select" class=" form-control-label">{{__('messages.type')}}<span class="reqfield">*</span></label>
                                                <select name="type" required id="type" class="form-control">
                                                        <option value="">{{__('messages.select')}} {{__('messages.type')}}</option>
                                                        <option value="1" <?=$option->type ==1 ? ' selected="selected"' : '';?>>{{__('messages.dropdown')}}</option>
                                                        <option value="2" <?=$option->type ==2 ? ' selected="selected"' : '';?>>{{__('messages.checkbox')}}</option>
                                                        <option value="3" <?=$option->type ==3 ? ' selected="selected"' : '';?>>{{__('messages.radiobutton')}}</option>
                                                        <option value="4" <?=$option->type ==4 ? ' selected="selected"' : '';?>>{{__('messages.multiple_select')}}</option>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-top: 35px">
                                            <div class="form-check">
                                                <div class="checkbox">
                                                    <label for="checkbox1" class="form-check-label ">
                                                        <input type="checkbox" id="is_required" name="is_required" value="1" class="form-check-input" <?=$option->is_required ==1 ? ' checked="checked"' : '';?>>{{__('messages.req_option_msg')}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                       <li class="nav-item">
                                             <a class="nav-link {{$i==0?'active':''}}" id="{{$l->code}}-tab" data-toggle="tab" href="#{{$l->code}}" role="tab" aria-controls="{{$l->code}}" aria-selected="true">{{$l->name}}</a>
                                       </li>
                                       <?php $i++; ?>
                                    @endforeach
                                 </ul>
                                 <div class="tab-content pl-3 p-1" id="myTabContent">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                       <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-tab">
                                            <div class="row form-group" style="margin-top: 15px">
                                                <div class="col col-md-3">
                                                    <label for="text-input" class=" form-control-label">{{__('messages.name')}}<span class="reqfield">*</span></label>
                                                </div>
                                                <?php $name = "name_".$l->code;?>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" required id="name_{{$l->code}}" name="name_{{$l->code}}" placeholder="{{__('messages.name')}}" class="form-control" value="{{$option->$name}}">
                                                </div>
                                            </div>
                                        <input type="hidden" name="totalrow_{{$l->code}}" id="totalrow_{{$l->code}}" value='1' />
                                        <table class="table table-striped cmr1" id="sortable_{{$l->code}}">
                                            <thead>
                                                <tr class="tdnew">
                                                    <td></td>
                                                    <td>{{__('messages.label')}}</td>
                                                    <td>{{__('messages.price')}}</td>
                                                    <td></td>
                                                </tr>
                                            </thead>
                                            <tbody id="lstable_{{$l->code}}">
                                                <?php $j=1;?>
                                                 @foreach($optionvalue as $op)
                                                         @if($l->code == $op->lang)
                                                            <tr id="row_{{$l->code}}_{{$j}}">
                                                                <td><i class="ti-layout-grid4-alt"></i></td>
                                                                <td data-id="{{$j}}">
                                                                    <input type="text" required id="label_{{$l->code}}_{{$j}}" name="label_{{$l->code}}[]" placeholder="" value="{{$op->label}}" class="form-control">
                                                                </td>
                                                                <td>
                                                                    <input type="text" id="price_{{$l->code}}_{{$j}}" name="price_{{$l->code}}[]" value="{{$op->price}}" placeholder="" class="form-control">
                                                                </td>                                                    
                                                                <td>
                                                                    <button class="btn btn-danger" type="button" onclick="removerow('{{$j}}','{{$l->code}}')"><i class="fa fa-trash f-s-25"></i></button>
                                                                </td>
                                                            </tr>
                                                            <?php $j++;?>
                                                        @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                       <div class="row"> <button type="button" class="btn btn-outline-secondary fleft" onclick="addoptionrow('{{$l->code}}')">{{__('messages.add_new_row')}}</button></div>
                                    </div>
                                       <?php $i++; ?>
                                    @endforeach
                                 </div>
                                
                                
                                <div class="row col-md-12" style="margin-top: 25px">
                                       @if(Session::get("is_demo")=='1')
                                         <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.save')}} 
                                        </button>
                                       @else
                                            <button type="submit" class="btn btn-primary btn-sm">{{__('messages.save')}} </button>
                                       @endif 
                                    

                                </div>
                            </div>
                        </form>

                    
                </div>
            </div>
        </div>
    </div>
</div>


@stop