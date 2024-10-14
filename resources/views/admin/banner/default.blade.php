@extends('admin.index') 
@section('title')
{{__("messages.Banner")}} || {{__("messages.Admin")}}
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
        <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.banner')}}
            </h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
        <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li class="active">{{__('messages.banner')}}
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
                <div class="col-lg-6 ban col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 banner1 b2">
                            <?php 
                        if(empty($img1)){ ?>
                                <img id="thumbImg1" src="{{asset('demo.jpg')}}" class=" z-depth-1-half thumb-pic" alt="">
                                <?php  }else{?>
                                    <img id="thumbImg1" src="{{asset('public/upload/banner/image').'/'.$img1}}" class=" z-depth-1-half thumb-pic" alt="">
                                    <?php }  ?>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="banner2">
                                <?php 
                           if(empty($img1)){ ?>
                                    <img id="thumbImg2" src="{{asset('demo-1.jpg')}}" class=" z-depth-1-half thumb-pic" alt="">
                                    <?php  }else{?>
                                        <img id="thumbImg2" src="{{asset('public/upload/banner/image').'/'.$img2}}" class=" z-depth-1-half thumb-pic" alt="">
                                        <?php }  ?>
                            </div>
                            <div class="banner3 b2">
                                <?php
                           if(empty($img1)){ ?>
                                    <img id="thumbImg3" src="{{asset('demo-1.jpg')}}" class=" z-depth-1-half thumb-pic" alt="">
                                    <?php  }else{?>
                                        <img id="thumbImg3" src="{{asset('public/upload/banner/image').'/'.$img3}}" class=" z-depth-1-half thumb-pic" alt="">
                                        <?php }  ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            {{__('messages.ban_upload_sec')}}
                        </div>
                        <form id="imageUploadForm" action="{{url('admin/updatebanner')}}" enctype="multipart/form-data" method="post">
                            {{csrf_field()}}
                            <div class="card-body">
                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.Banner_1')}}:-
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
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
                                 <div class="tab-content p-1" id="myTabContent">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                        <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-tab">
                                            <div class="row form-group">
                                                <div class="col col-md-4">
                                                    <label for="file-input" class=" form-control-label">{{__('messages.title')}}
                                                    </label>
                                                </div>
                                                <?php $title = "title_".$l->code; ?>
                                                <div class="col-12 col-md-8">
                                                    <input type="text" name="title_{{$l->code}}" id="title_{{$l->code}}" class="form-control" value="<?=isset($bannerdata[0]->$title)?$bannerdata[0]->$title:'0'; ?>" required placeholder="DESIGNER BAGS">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col col-md-4">
                                                    <label for="file-input" class=" form-control-label">{{__('messages.sub_title')}}
                                                    </label>
                                                </div>
                                                 <?php $subtitle = "subtitle_".$l->code; ?>
                                                <div class="col-12 col-md-8">
                                                    <input type="text" name="subtitle_{{$l->code}}" id="subtitle_{{$l->code}}" class="form-control" required value="<?=isset($bannerdata[0]->$subtitle )?$bannerdata[0]->$subtitle :'0'; ?>" placeholder="Be the first to get what’s new">
                                                </div>
                                            </div>
                                        </div>
                                       <?php $i++; ?>
                                    @endforeach
                                </div>
                                
                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.subcategory')}}
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <select class="form-control" name="subcategory1" id="subcategory1" required>
                                            @foreach($subcategory as $su)
                                            <option value="{{$su->id}}" <?=isset($bannerdata[0]->subcategory)&&$bannerdata[0]->subcategory ==$su->id? ' selected="selected"' : '';?>> {{$su->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.banner')}} (542X708)
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <input type="file" accept="image/*" name="photo1" id="upload_image1" class="form-control-file">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.Banner_2')}}:-
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                       <li class="nav-item">
                                             <a class="nav-link {{$i==0?'active':''}}" id="{{$l->code}}-tab1" data-toggle="tab" href="#{{$l->code}}1" role="tab" aria-controls="{{$l->code}}" aria-selected="true">{{$l->name}}</a>
                                       </li>
                                       <?php $i++; ?>
                                    @endforeach
                                 </ul>
                                 <div class="tab-content p-1" id="myTabContent">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                        <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}1" role="tabpanel" aria-labelledby="{{$l->code}}-tab1">
                                            <div class="row form-group">
                                                <div class="col col-md-4">
                                                    <label for="file-input" class=" form-control-label">{{__('messages.title')}}
                                                    </label>
                                                </div>
                                                <?php $title = "title_".$l->code; ?>
                                                <div class="col-12 col-md-8">
                                                    <input type="text" name="title_{{$l->code}}" id="title_{{$l->code}}" class="form-control" value="<?=isset($bannerdata[0]->$title)?$bannerdata[0]->$title:'0'; ?>" required placeholder="DESIGNER BAGS">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col col-md-4">
                                                    <label for="file-input" class=" form-control-label">{{__('messages.sub_title')}}
                                                    </label>
                                                </div>
                                                 <?php $subtitle = "subtitle_".$l->code; ?>
                                                <div class="col-12 col-md-8">
                                                    <input type="text" name="subtitle_{{$l->code}}" id="subtitle_{{$l->code}}" class="form-control" required value="<?=isset($bannerdata[0]->$subtitle )?$bannerdata[0]->$subtitle :'0'; ?>" placeholder="Be the first to get what’s new">
                                                </div>
                                            </div>
                                        </div>
                                       <?php $i++; ?>
                                    @endforeach
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.subcategory')}}
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <select class="form-control" required name="subcategory2" id="subcategory2">
                                            @foreach($subcategory as $su)
                                            <option value="{{$su->id}}" <?=isset($bannerdata[1]->subcategory)&&$bannerdata[1]->subcategory ==$su->id? ' selected="selected"' : '';?>> {{$su->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.banner')}} (542X708)
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <input type="file" accept="image/*" name="photo2" id="upload_image2" class="form-control-file">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.Banner_3')}}:-
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                    </div>
                                </div>
                                 <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                       <li class="nav-item">
                                             <a class="nav-link {{$i==0?'active':''}}" id="{{$l->code}}-tab2" data-toggle="tab" href="#{{$l->code}}2" role="tab" aria-controls="{{$l->code}}" aria-selected="true">{{$l->name}}</a>
                                       </li>
                                       <?php $i++; ?>
                                    @endforeach
                                 </ul>
                                 <div class="tab-content p-1" id="myTabContent">
                                    <?php $i=0; ?>
                                    @foreach($lang as $l)
                                        <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}2" role="tabpanel" aria-labelledby="{{$l->code}}-tab2">
                                            <div class="row form-group">
                                                <div class="col col-md-4">
                                                    <label for="file-input" class=" form-control-label">{{__('messages.title')}}
                                                    </label>
                                                </div>
                                                <?php $title = "title_".$l->code; ?>
                                                <div class="col-12 col-md-8">
                                                    <input type="text" name="title_{{$l->code}}" id="title_{{$l->code}}" class="form-control" value="<?=isset($bannerdata[0]->$title)?$bannerdata[0]->$title:'0'; ?>" required placeholder="DESIGNER BAGS">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col col-md-4">
                                                    <label for="file-input" class=" form-control-label">{{__('messages.sub_title')}}
                                                    </label>
                                                </div>
                                                 <?php $subtitle = "subtitle_".$l->code; ?>
                                                <div class="col-12 col-md-8">
                                                    <input type="text" name="subtitle_{{$l->code}}" id="subtitle_{{$l->code}}" class="form-control" required value="<?=isset($bannerdata[0]->$subtitle )?$bannerdata[0]->$subtitle :'0'; ?>" placeholder="Be the first to get what’s new">
                                                </div>
                                            </div>
                                        </div>
                                       <?php $i++; ?>
                                    @endforeach
                                </div>                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.subcategory')}}
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <select class="form-control" required name="subcategory3" id="subcategory3">
                                            @foreach($subcategory as $su)
                                            <option value="{{$su->id}}" <?=isset($bannerdata[2]->subcategory)&&$bannerdata[2]->subcategory ==$su->id ? ' selected="selected"' : '';?>> {{$su->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="file-input" class=" form-control-label">{{__('messages.banner')}} (542X708)
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <input type="file" accept="image/*" name="photo3" id="upload_image3" class="form-control-file">
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group btncenter">

                                @if(Session::get("is_demo")=='1')
                                 <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-secondary d-flex justify-content-center mt-3 righttag">
                                    {{__('messages.save')}}
                                </button>
                                @else
                                  <button type="submit" class="btn btn-secondary d-flex justify-content-center mt-3 righttag">
                                    {{__('messages.save')}}
                                </button>
                                @endif
                              
                                <button type="button" class="btn btn-secondary d-flex justify-content-center mt-3">
                                    {{__('messages.cancel')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop