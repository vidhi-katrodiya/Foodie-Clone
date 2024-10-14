@extends('seller.index')
@section('content')
<?php ?>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.catalog')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">               
               <li><a href="{{url('admin/product')}}">{{__('messages.catalog')}}</a></li>
               <li class="active">{{__('messages.save')}} {{__('messages.catalog')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3 ">
   <div class="rowset">
      <div class="col-lg-10 orderdiv">
         <div class="card">
            <div class="card-header">
               <h4>{{__('messages.save')}} {{__('messages.catalog')}}</h4>
            </div>
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
               <div class="tab-content pl-3 p-1" id="myTabContent">
                  <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                     <div class="cmr1">
                        <div class="col-lg-12">
                           <div class="custom-tab">
                              <nav class="col-md-12 tabcatlog">
                                 <div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link <?= $tab==1?"active":"tabdiv" ?>" id="custom-nav-general-tab" data-toggle="tab" href="#custom-nav-general" role="tab" aria-controls="custom-nav-general" aria-selected="true">{{__('messages.general')}}</a>
                                    <a class="nav-item nav-link <?= $tab==2?"active":"tabdiv" ?>" id="custom-nav-price-tab" data-toggle="tab" href="#custom-nav-price" role="tab" aria-controls="custom-nav-price" aria-selected="false">{{__('messages.price')}}</a>
                                    <a class="nav-item nav-link <?= $tab==4?"active":"tabdiv" ?>" id="custom-nav-imgls-tab" data-toggle="tab" href="#custom-nav-imgls" role="tab" aria-controls="custom-nav-imgls" aria-selected="false">{{__('messages.images')}}</a>
                                 </div>
                              </nav>
                              <div class="tab-content col-md-12 p-0 " id="nav-tabContent">
                                 <div class="tab-pane fade <?= $tab==1?"show active":"" ?> pd10" id="custom-nav-general" role="tabpanel" aria-labelledby="custom-nav-general-tab" >
                                    <h3>{{__('messages.general')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form action="{{url('seller/saveproduct')}}" method="post">
                                       {{csrf_field()}}
                                       <input type="hidden" name="product_id" id="product1" value="{{$product_id}}"/>
                                       <input type="hidden" name="cat_id" id="cat_id" value="{{$cat_id}}"/>
                                        <div class="custom-tab">
                                         
                                          <div class="tab-content col-md-12 p-0 " id="nav-tabContentA">
                                              <?php $k=0;?>
                                                @foreach($lang as $l)
                                                   <div class="tab-pane fade {{$k==0?'show active':''}}" id="step{{$l->code}}" role="tabpanel" aria-labelledby="step_tab{{$l->code}}" >
                                                        <div class="form-group" style="margin-top: 15px">
                                                            <label for="name_{{$l->code}}" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span>
                                                            </label>
                                                            <?php $name = "name_".$l->code;?>
                                                            <input id="name_{{$l->code}}" name="name_{{$l->code}}" value="<?= isset($data->$name)?$data->$name:""?>" type="text" class="form-control" aria-required="true" aria-invalid="false" placeholder="{{__('messages.name')}}">
                                                         </div>
                                                         <div class="form-group">
                                                            <label for="description" class="control-label mb-1">{{__('messages.description')}}<span class="reqfield">*</span>
                                                            </label>
                                                            <?php $desc = "description_".$l->code;?>

                                                          <textarea name="description_en" id="description_{{$l->code}}" class="editor form-control"><?= isset($data->description)?$data->description:""?></textarea>

                                                         </div>

                                                        <!--  <div class="form-group col-md-12" style="padding-left: 0px">
                                                            <label for="name" class="control-label mb-1 dttablewidth">{{__('messages.meta_keyword')}}</label>
                                                            <?php $meta_keyword = "meta_keyword_".$l->code;?>
                                                            <input id="meta_keyword_{{$l->code}}" value="<?= isset($data->$meta_keyword)?$data->$meta_keyword:""?>" name="meta_keyword_{{$l->code}}" type="text" class="form-control" data-role="tagsinput" aria-invalid="false" placeholder="{{__('messages.meta_keyword')}}">
                                                         </div> -->

                                                  </div>
                                                  <?php $k++;?>
                                                @endforeach
                                         </div>
                                      </div>
                                           <div class="row">
                                          
                                          <div class="form-group col-md-12">
                                            <label for="category" class="control-label mb-1">Is Veg  <span class="reqfield">*</span>
                                             </label>
                                            <input type="radio" class="check_box_veg" name="is_veg" value="1" <?= isset($data->is_veg)&&$data->is_veg=='1'?"checked='checked'":""?>><label for="check_box_veg"> Veg</label>
                                            <input type="radio" class="check_box_non_veg" name="is_veg" value="0" <?= isset($data->is_veg)&&$data->is_veg=='0'?"checked='checked'":""?>><label for="check_box_non_veg"> Non Veg</label>
                                          </div>
                                         
                                         
                                          
                                       </div>
                                       <div class="col-md-12 form-group rowset">
                                         @if(Session::get("is_demo")=='1')
                                         <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary btn-flat m-b-30 m-t-30">
                                         {{__('messages.save')}} 
                                         </button>
                                         @else
                                         <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="submit">{{__('messages.save')}}</button>
                                         @endif 
                                      </div>
                                      
                                    </form>
                                 </div>
                                 <div class="tab-pane fade <?= $tab==2?"show active":"" ?> pd10" id="custom-nav-price" role="tabpanel" aria-labelledby="custom-nav-price-tab">
                                    <h3>{{__('messages.price')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form action="{{url('seller/saveprice')}}" method='post'>
                                        {{csrf_field()}}
                                        <input type="hidden" name="product_id" id="product1" value
                                       ="{{$product_id}}"/>
                                       <input type="hidden" name="c_id" id="c_id" value="{{$cat_id}}"/>
                                        <div class="form-group col-md-6" >
                                             <label for="name" class="control-label mb-1">Unit<span class="reqfield">*</span>
                                             </label>
                                             <input id="weight" name="weight" type="text" class="form-control" value="<?= isset($data->weight)?$data->weight:""?>" aria-required="true" aria-invalid="false" placeholder="i.g gm,kg,pices " required>
                                        </div>
                                     
                                          <div class="form-group col-md-6" >
                                             <label for="name" class="control-label mb-1">Price<span class="reqfield">*</span>
                                             </label>
                                             <input id="mrp" name="mrp" type="text" class="form-control" value="<?= isset($data->MRP)?$data->MRP:""?>" aria-required="true" aria-invalid="false" placeholder="{{__('messages.MRP')}}" required>
                                          </div>
                                         <div class="form-group col-md-6">
                                             <label for="name" class="control-label mb-1">Discount Type<span class="reqfield">*</span>
                                             </label>
                                             <select class="form-control" name="discount_type" id="discount_type">
                                                  <option value="0" <?= isset($data->discount_type)&&$data->discount_type=='0'?"selected='selected'":""?>>Percentage</option>
                                                  <option value="1" <?= isset($data->discount_type)&&$data->discount_type=='1'?"selected='selected'":""?>>Amount</option>
                                             </select>
                                            
                                          </div>
                                           <div class="form-group col-md-6">
                                             <label for="name" class="control-label mb-1">Discount<span class="reqfield">*</span>
                                             </label>
                                             <input id="discount_atm" name="discount_atm" type="text" class="form-control" aria-required="true" value="<?= isset($data->discount_val)?$data->discount_val:""?>" aria-invalid="false" placeholder="{{__('messages.selling_price')}}" required>
                                          </div>
                                       <div class="row form-group rowset">
                                          <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="submit" >{{__('messages.save')}}</button>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade <?= $tab==4?"show active":"" ?> pd10" id="custom-nav-imgls" role="tabpanel" aria-labelledby="custom-nav-imgls-tab">
                                    <h3>{{__('messages.images')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form action="{{url('seller/saveproductimage')}}" method="post">                                   
                                       {{csrf_field()}}
                                        <input type="hidden" name="product_id" id="product1" value
                                       ="{{$product_id}}"/>
                                       <input type="hidden" name="ct_id" id="ct_id" value="{{$cat_id}}"/>
                                       <div class="mar20">
                                          <h4 class="orderdiv">{{__('messages.basic_img')}}</h4>
                                          <div id="uploaded_image">
                                             <div class="upload-btn-wrapper">
                                                <button class="btn imgcatlog">
                                                   <input type="hidden" name="real_basic_img" id="real_basic_img" value="<?= isset($data->basic_image)?$data->basic_image:""?>"/>
                                                   <?php 
                                                         if(isset($data->basic_image)){
                                                             $path=asset('public/upload/product')."/".$data->basic_image;
                                                         }
                                                         else{
                                                             $path=asset('public/admin/images/imgplaceholder.png');
                                                         }
                                                   ?>
                                                <img src="{{$path}}" alt="..." class="img-thumbnail imgsize"  id="basic_img" >
                                                </button>
                                                <input type="hidden" name="basic_img" id="basic_img1"/>
                                                <input type="file" name="upload_image" id="upload_image" />
                                             </div>
                                          </div>
                                       </div>
                                   
                                    <!-- <div class="mar20">
                                       <h4 class="orderdiv">{{__('messages.add_img')}}</h4>
                                     
                                             <div id="additional_image" class="fleft">
                                                <?php $i=0;?>
                                                @if(isset($data->additional_image))
                                                  <?php $imagels=explode(",",$data->additional_image);;?>
                                                   @foreach($imagels as $imls)
                                                      <div id="imgid{{$i}}" class="add-img">
                                                         <input type="hidden" name="add_real_img[]" value="{{$imls}}"/>
                                                         <img src="{{asset('public/upload/product').'/'.$imls}}" class="img-thumbnail imgsize" id="additional_img{{$i}}" name="arrimg[]" />
                                                            <div class="add-box">
                                                               <input type="hidden" id="additionalimg{{$i}}" name="additional_img[]" value="{{asset('public/upload/product').'/'.$imls}}"/>
                                                               <input type="button" id="removeImage1" value="x" class="btn-rmv1" onclick="removeimg('{{$i}}')" />
                                                            </div>
                                                      </div>
                                                      <?php $i++;?>
                                                   @endforeach
                                                @endif                                                                                           
                                             </div>
                                             <div class="upload-btn-wrapper">
                                                      <button class="btn imgcatlog">
                                                      <img src="{{asset('public/admin/images/add_image.png')}}" alt="..." class="img-thumbnail imgsize">
                                                      </button>
                                                      <input type="file" name="add_image" id="add_image" />
                                                   </div>
                                      </div>       
                                    <input type="hidden" name="add_total_img" id="add_total_img" value="{{$i}}" /> -->
                                    <div class="row form-group col-md-12" style="margin-top: 15px;margin-left: 10px;">
                                          <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="submit">{{__('messages.save')}}</button>
                                    </div>
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
<input type="hidden" id="msgtype" value="{{__('messages.type')}}">
<input type="hidden" id="check_price" value="{{__('messages.check_price')}}">
<input type="hidden" id="special_price_check" value="{{__('messages.special_price_check')}}">
<input type="hidden" id="sepical_price_vaildate" value="{{__('messages_error_success.sepical_price_vaildate')}}">
<input type="hidden" id="selling_mrp_vaildate" value="{{__('messages_error_success.selling_mrp_vaildate')}}">
<input type="hidden" id="up_pro" value="0" />
<input type="hidden" id="new_attribute" value="{{__('messages.New Attributes')}}">
<input type="hidden" id="attribute_msg" value="{{__('messages.attribute')}}">
<input type="hidden" id="value_msg" value="{{__('messages.value')}}">
<input type="hidden" id="cross_pro" value="0" />
<input type="hidden" id="sku_already" value="{{__('messages_error_success.sku_already')}}">
@stop 
@section('footer')
<script type="text/javascript" src="{{asset('public/js/sellerproduct.js').'?v=wewe3'}}"></script>
<script>
   <?php foreach($lang as $l){ ?>
   //CKEDITOR.replace('description_<?=$l->code?>');

   <?php } ?>
   
   $(document).ready(function(){
  
  $('.projects_select').click(function(){
    var tab_id = $(this).attr('data-tab');

    $('.projects_select').removeClass('current');
    $('.tab-content').removeClass('current');

    $(this).addClass('current');
    $("#"+tab_id).addClass('current');
  })

})
</script>
@stop