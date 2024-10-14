@extends('seller.index') 

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="breadcrumbs">
   <div class="col-sm-4">
      <div class="page-header float-left">
         <div class="page-title">
            <h1>{{__('messages.coupon')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="page-header float-right">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li><a href="{{url('seller/coupon')}}">{{__('messages.coupon')}}</a></li>
               <li class="active">{{__('messages.add_coupon')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
<div class="rowset">
<div class="col-lg-9">
   <div class="card">
      <div class="card-header">
         <h4>{{__('messages.add_coupon')}}</h4>
      </div>
      <div class="card-body">
         <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
               <a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">  
               {{__('messages.general')}}
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
               {{__('messages.usage_res')}}
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="coupon-tab" data-toggle="tab" href="#coupon" role="tab" aria-controls="coupon" aria-selected="true">
               {{__('messages.usage_limit')}}
               </a>
            </li>
         </ul>
         <div class="tab-content pl-3 p-1" id="myTabContent">
            <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
               <div class="col-sm-12 error-div" style="display: none">
                    <div class="alert  alert-class alert-info alert-dismissible fade show" role="alert">
                     <p id="error_msg"></p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

               <div class="cmr1">
                  <form id="addcoupon11">
                  <input type="hidden" name="coupon_id" id="coupon_id" value="<?=isset($data->id)?$data->id:''; ?>" />

                           <div class="tab-content p-1" id="myTabContent">
                              <?php $i=0; ?>
                              @foreach($lang as $l)
                                  <div class="tab-pane fade {{$i==0?'show active':''}}" id="{{$l->code}}" role="tabpanel" aria-labelledby="{{$l->code}}-tab">
                                  </div>
                                 <?php $i++; ?>
                              @endforeach
                            <div class="form-group col-md-12">
                                <label for="cc-payment" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span>
                                </label>
                                
                                <input id="coupon_name" name="coupon_name" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?=isset($data->name)?$data->name:''; ?>" placeholder="{{__('messages.name')}}">
                             </div>
                             
                          </div>

                   <div class="form-group col-md-12 paddiv" >
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.discount_type')}}
                        </label>
                        <select id="discount_type" name="discount_type" class="form-control">
                           <option value="0" <?=isset($data->discount_type)&&$data->discount_type=='0'?'selected="selected"':''; ?> > {{__('messages.Fixed')}}
                           </option>
                           <option value="1" <?=isset($data->discount_type)&&$data->discount_type=='1'?'selected="selected"':''; ?>> {{__('messages.percentage')}}
                           </option>
                        </select>
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.value')}}
                        </label>
                        <input id="value" name="value" type="number" class="form-control" aria-required="true" aria-invalid="false" value="<?=isset($data->value)?$data->value:''; ?>" placeholder="{{__('messages.value')}}">
                     </div>
                  </div>
                  <!-- <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.start_date')}}
                        </label>
                        <input id="start_date" name="start_date" type="text" class="form-control" value="<?=isset($data->start_date)?$data->start_date:''; ?>">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.end_date')}}
                        </label>
                        <input id="end_date" name="end_date" type="text" class="form-control" value="<?=isset($data->end_date)?$data->end_date:''; ?>">
                     </div>
                  </div> -->
                  <div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.code')}}
                        <span class="reqfield">*</span>
                        </label>
                        <input id="code" name="code" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?=isset($data->code)?$data->code:''; ?>" placeholder="{{__('messages.code')}}">
                     </div>
                     <div class="col-md-6 cmr1">
                        <div class="form-group col-md-12">
                           <div class="form-check">
                              <div class="status">
                                 <label for="is_main_offer" class="form-check-label ">
                                 <input type="checkbox" id="is_main_offer" name="is_main_offer" value="1" class="form-check-input" <?=isset($data->is_main_offer)&&$data->is_main_offer=='1'?'checked="checked"':''; ?>>Enable Main Offer
                                 </label>
                              </div>
                           </div>
                        </div>
                        <!-- <div class="form-group col-md-12">
                           <div class="form-check">
                              <div class="status">
                                 <label for="status" class="form-check-label ">
                                 <input type="checkbox" id="status" name="status" value="1" class="form-check-input" <?=isset($data->status)&&$data->status=='1'?'checked="checked"':''; ?>>{{__('messages.enable_the_coupon')}}
                                 </label>
                              </div>
                           </div>
                        </div>
                        <div class="form-group col-md-12">
                           <div class="form-check">
                              <div class="status">
                                 <label for="free_shipping" class="form-check-label ">
                                 <input type="checkbox" id="free_shipping" name="free_shipping" value="1" class="form-check-input" <?=isset($data->free_shipping)&&$data->free_shipping=='1'?'checked="checked"':''; ?>>{{__('messages.allow_free_shipping')}}
                                 </label>
                              </div>
                           </div>
                        </div> -->
                     </div>
                  </div>
                  <div class="form-group col-md-12" >
                      @if(Session::get("is_demo")=='1')
                            <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                    {{__('messages.next')}}
                           </button>
                      @else
                            <input type="button" name=""  class="florig btn btn-primary" onclick="savecoupon()" value="{{__('messages.next')}}"> 
                      @endif                   
                  </div>
               </form>
               </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
               <div class="cmr1">
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                      <!-- <input type="hidden" name="tab_two_id"> -->
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.minmum_spend')}}</label>
                        <input id="minmum_send" name="minmum_send" type="text" class="form-control" aria-required="true" aria-invalid="false" placeholder="{{__('messages.minmum_spend')}}" value="<?=isset($data->minmum_spend)?$data->minmum_spend:''; ?>" onkeypress="return isNumberKey(event)">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">
                        {{__('messages.maximum_spend')}}
                        </label>
                        <input id="maximum_spend" name="maximum_spend" type="text" class="form-control" aria-required="true" aria-invalid="false" value="<?=isset($data->maximum_spend)?$data->maximum_spend:''; ?>" placeholder="{{__('messages.maximum_spend')}}" onchange="maxnumber(this.value)" onkeypress="return isNumberKey(event)">
                     </div>
                  </div>

                 <!--  <div class="form-group col-md-12">
                     <div class="form-check">
                        <div class="status">
                           <label for="coupon_on" class="form-check-label ">
                           <input type="checkbox" id="coupon_on" name="coupon_on" value="1" class="form-check-input" checked="" onchange="changeproductdiv()">Coupon On Product
                           </label>
                        </div>
                     </div>
                  </div> -->


                  <div class="form-group col-md-12">
                      <label for="coupon_on" class="form-check-label ">
                        Coupon On 
                      </label><br>
                      <?php
                          $pro="";
                          $cat="";
                        if(isset($data->coupon_on) && !empty($data->coupon_on))
                        {
                          if($data->coupon_on=="0")
                          {
                            $pro="checked";
                          }
                          if($data->coupon_on=="1")
                          {
                            $cat="checked";
                          }
                        }else{
                          $pro="checked";
                          $cat="";
                        }
                      ?>
                      <input type="radio" name="coupon_on" value="0" <?=$pro;?>  onchange="changeproductdiv(0);"> Product 
                      <input type="radio" name="coupon_on" value="1" <?=$cat;?> onchange="changeproductdiv(1);"> Category 
                  </div>

                  <div class="form-group col-md-12" id="productcoupon">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.product')}}</label>
                     <input id="product" name="product" type="text" value="<?=isset($data->product)?$data->product:''; ?>">
                  </div>
                  <div class="form-group col-md-12 disno" id="categorycoupon">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.category')}}</label>
                     <input id="category" name="category" type="text" value="<?=isset($data->categories)?$data->categories:''; ?>">
                  </div>
                  <div class="form-group col-md-12" >
                      @if(Session::get("is_demo")=='1')
                            <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                    {{__('messages.next')}}
                           </button>
                      @else
                             <button type="button" class="btn btn-primary florig" onclick="SaveCouponstep2()">{{__('messages.next')}}</button>
                      @endif  
                    
                  </div>
               </div>
            </div>
            <div class="tab-pane fade" id="coupon" role="tabpanel" aria-labelledby="coupon-tab">
               <div class="cmr1">
                  <div class="form-group col-md-12 paddiv">
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.usage_limit_per_coupon')}}</label>
                        <input id="per_coupon" name="per_coupon" type="number" class="form-control" aria-required="true" aria-invalid="false" placeholder="0" min='0' value="<?=isset($data->usage_limit_per_coupon)?$data->usage_limit_per_coupon:''; ?>">
                     </div>
                     <div class="col-md-6">
                        <label for="cc-payment" class="control-label mb-1">{{__('messages.usage_limit_per_customer')}}</label>
                        <input id="per_customer" name="per_customer" type="number" class="form-control" aria-required="true" aria-invalid="false" placeholder="0" min='0' value="<?=isset($data->usage_limit_per_customer)?$data->usage_limit_per_customer:''; ?>">
                     </div>
                  </div>
                  <div class="form-group col-md-12" >
                      @if(Session::get("is_demo")=='1')
                            <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                    {{__('messages.finish')}}
                           </button>
                      @else
                             <button type="button" onclick="Savecouponstep3()" class="btn btn-primary florig">{{__('messages.finish')}}</button>
                      @endif  
                    
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<input type="hidden" id="coupon_code_use" value="{{__('messages_error_success.coupon_code_use')}}">
<input type="hidden" id="error_coupon_limit" value="{{__('messages_error_success.error_coupon_limit')}}">
<script type="text/javascript">
function maxnumber(val)
{
   var min=$("#minmum_send").val();
   if(parseInt(val)<parseInt(min)){
      alert($("#coupon_vaild_max").val());
      $("#maximum_spend").val("");
   }
}
</script>
<script type="text/javascript">
  $( document ).ready(function() 
  {
      var coupon_on = $("input[name='coupon_on']:checked").val();
      if(coupon_on==0){
        changeproductdiv(0);
      }
      if(coupon_on==1){
        changeproductdiv(1);
      }
  });

   function savecoupon()
   {
      var coupon_id=$("#coupon_id").val();
      var coupon_name=$("#coupon_name").val();
      var discount_type = $("#discount_type option:selected").val();
      var value=$("#value").val();
      // var start_date=$("#start_date").val();
      // var end_date=$("#end_date").val();
      var code=$("#code").val();
     
      var is_main_offer=$('input[name="is_main_offer"]:checked').val();
      if(typeof(is_main_offer) == "undefined")
      {
         is_main_offer=0;
      }
     /* var status=$('input[name="status"]:checked').val();
      if(typeof(status) == "undefined")
      {
         status=0;
      }
      var free_shipping=$('input[name="free_shipping"]:checked').val();
      if(typeof(free_shipping) == "undefined")
      {
         free_shipping=0;
      } */
      $.ajax({
          url: $("#url_path").val()+"/seller/savecoupon",
          method:"POST",
          data: {"_token": "{{ csrf_token() }}","coupon_id":coupon_id,"coupon_name":coupon_name,"discount_type":discount_type,"value":value,"code":code,'is_main_offer':is_main_offer},
          success: function(data) {
            var json = JSON.parse(data);
            if(json.status==0)
            {
               $('error-div')
               alert(json.msg);
            }
            else
            {
                //alert("yes")
                $("#coupon_id").val(json.id);
                $("#home").removeClass('in show active');
                $('a[href="#home"]').removeClass('active');
                $('a[href="#profile"]').addClass('active');
                $("#profile").addClass('in show active');
            }
          }
      });
     
   }
</script>
@stop