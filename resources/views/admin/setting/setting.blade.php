@extends('admin.index')
@section('title')
{{__("messages.Settings")}} || {{__("messages.Admin")}}
@endsection
@section('content')   
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="breadcrumbs " >
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.setting')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.setting')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3">
   <div class="rowset">
      <div class="col-md-12 col-lg-9">
         <div class="card">
            <div class="card-body">
                <input type="hidden" name="isemailset" id="isemailset" value="{{Config::get('mail.username')!=''?1:0}}"/>
               <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
                     <a class="nav-link active show" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">{{__('messages.general_sec')}}</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">{{__('messages.soical_sec')}}</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="true">{{__('messages.shipping')}} {{__('messages.section')}}</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="payment-tab" data-toggle="tab" href="#payment" role="tab" aria-controls="payment" aria-selected="true">{{__('messages.payment_method')}} {{__('messages.section')}}</a>
                  </li>
               </ul>
               <div class="tab-content pl-3 p-1" id="myTabContent">
                  <div class="tab-pane fade active show" id="general" role="tabpanel" aria-labelledby="general-tab">
                     <div class="cmr1">
                        <form id="updatesetting" class="needs-validation" method="post" enctype="multipart/form-data">
                        {{csrf_field()}} 
                        
                                           <div class="form-group col-md-12">
                                              <div class="form-group col-md-6">
                          <label for="name" class=" form-control-label">
                              {{__('messages.com_logo')}} <b>{{__('messages.img_tr')}} (128X61)</b>
                              <span class="reqfield">*</span>
                              </label>
                        <div id="uploaded_image">
                            <div class="upload-btn-wrapper">
                                <button class="btn imgcatlog">
                                   <input type="hidden" name="real_logo" id="real_logo" value="<?= isset($data->logo)?$data->logo:""?>"/>
                                                   <?php 
                                                         if(isset($data->logo)){
                                                             $path=asset('public/Ecommerce/images/')."/".$data->logo;
                                                         }
                                                         else{
                                                             $path=asset('public/Ecommerce/images/logo1.png');
                                                         }
                                                   ?>
                                                <img src="{{$path}}" alt="..." class="img-thumbnail"  id="img_logo" >
                                                </button>
                                                <input type="hidden" name="logo" id="logo"/>
                                                <input type="file" name="upload_image_logo" id="upload_image_logo" />
                                             </div>
                                          </div>
                                          </div>
                         <div class="form-group col-md-5">
                              <label for="name" class=" form-control-label">
                              {{__('messages.site_rtl')}}
                               <a href="{{url('admin/changesettingstatus/is_rtl')}}">
                                   @if($data->is_rtl==0)
                                     <i class="fa fa-toggle-off" style="font-size: xx-large;font-weight: bolder;color: blue;"></i>
                                   @else
                                      <i class="fa fa-toggle-on" style="font-size: xx-large;font-weight: bolder;color: blue;"></i>
                                   @endif
                                   
                               </a>
                              </label>
                                </br>
                               @if($data->is_web==1)
                               <label for="name" class=" form-control-label">
                              {{__('messages.site_color_setting')}}
                              </label>
                              <a href="{{url('admin/changesettingstatus/set_show')}}">
                                   @if($data->set_show==0)
                                     <i class="fa fa-toggle-on" style="font-size: xx-large;font-weight: bolder;color: blue;"></i>
                                   @else
                                      <i class="fa fa-toggle-off" style="font-size: xx-large;font-weight: bolder;color: blue;"></i>
                                   @endif
                              </a>
                              @endif
                             
                           </div>
                         
                        </div>

                        <div class="custom-tab">
                                          <nav class="col-md-12 tabcatlog">
                                             <div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist">
                                                <?php $k=0;?>
                                                @foreach($lang as $l)
                                                     <a class="nav-item nav-link {{$k==0?'active':'tabdiv'}}" id="step_tab{{$l->code}}" data-toggle="tab" href="#step{{$l->code}}" role="tab" aria-controls="step{{$l->code}}" aria-selected="true">{{$l->name}}</a>  
                                                     <?php $k++; ?>
                                                @endforeach  
                                             </div>
                                          </nav>
                                          <div class="tab-content col-md-12 p-0 " id="nav-tabContent">
                                              <?php $k=0;?>
                                                @foreach($lang as $l)
                                                   <div class="tab-pane fade {{$k==0?'show active':''}}" id="step{{$l->code}}" role="tabpanel" aria-labelledby="step_tab{{$l->code}}" >
                                                          <div class="form-group col-md-12">
                                                            <label for="name" class=" form-control-label">
                                                            {{__('messages.company_name')}}
                                                            <span class="reqfield">*</span>
                                                            </label>
                                                            <?php $company_name = "company_name_".$l->code ?>
                                                            <input type="text" id="company_name_{{$l->code}}" placeholder="{{__('messages.company_name')}}" class="form-control" name="company_name_{{$l->code}}" required value="{{isset($data->$company_name)?$data->$company_name:''}}">
                                                         </div>
                                                      <div class="form-group col-md-12 paddiv">
                                                         <div class="col-md-6">
                                                            <label for="name" class=" form-control-label">
                                                            {{__('messages.email')}}
                                                            <span class="reqfield">*</span>
                                                            </label>
                                                            <?php $email = "email_".$l->code ?>
                                                            <input type="text" id="email_{{$l->code}}" placeholder="{{__('messages.email')}}" class="form-control" name="email_{{$l->code}}" required value="{{isset($data->$email)?$data->$email:''}}">
                                                         </div>
                                                         <div class="col-md-6">
                                                            <label for="name" class=" form-control-label">
                                                            {{__('messages.phone')}}
                                                            <span class="reqfield">*</span>
                                                            </label>
                                                            <?php $phone = "phone_".$l->code ?>
                                                            <input type="text" id="phone_{{$l->code}}" placeholder="{{__('messages.phone')}}" class="form-control" name="phone_{{$l->code}}" required value="{{isset($data->$phone)?$data->$phone:''}}">
                                                         </div>
                                                      </div>
                                                      
                                                      <div class="form-group col-md-12 paddiv">
                                                         <div class="col-md-6">
                                                            <label for="name" class=" form-control-label">
                                                            {{__('messages.working_day')}}
                                                            <span class="reqfield">*</span>
                                                            </label>
                                                            <?php $working_day = "working_day_".$l->code; ?>
                                                            <input type="text" id="working_day_{{$l->code}}" placeholder="{{__('messages.working_placeholder')}}" class="form-control" name="working_day_{{$l->code}}" required value="{{isset($data->$working_day)?$data->$working_day:''}}">
                                                         </div>
                                                         <div class="col-md-6">
                                                            <label for="name" class=" form-control-label">
                                                            {{__('messages.helpline')}}
                                                            <span class="reqfield">*</span>
                                                            </label>
                                                            <?php $helpline = "helpline_".$l->code; ?>
                                                            <input type="text" id="helpline_{{$l->code}}" placeholder="{{__('messages.helpline')}}" class="form-control" name="helpline_{{$l->code}}" required value="{{isset($data->$helpline)?$data->$helpline:''}}">
                                                         </div>
                                                      </div>
                                                      <div class="form-group col-md-12">
                                                         <label for="name" class=" form-control-label">
                                                         {{__('messages.newsletter')}}
                                                         <span class="reqfield">*</span>
                                                         </label>
                                                         <?php $newsletter = "newsletter_".$l->code; ?>
                                                         <textarea  id="newsletter_{{$l->code}}" placeholder="{{__('messages.newsletter')}}" class="form-control" name="newsletter_{{$l->code}}" required >{{isset($data->$newsletter)?$data->$newsletter:''}}</textarea>
                                                      </div>
                                                      <div class="form-group col-md-12">
                                                         <label for="name" class=" form-control-label">
                                                         {{__('messages.main_feature')}}
                                                         <span class="reqfield">*</span>
                                                         </label>
                                                         <?php $main_feature = "main_feature_".$l->code?>
                                                         <textarea id="main_feature_{{$l->code}}" placeholder="{{__('messages.main_feature')}}" class="form-control h150" name="main_feature_{{$l->code}}" required >{{isset($data->$main_feature)?$data->$main_feature:''}}</textarea>
                                                      </div>
                                                      <div class="form-group col-md-12">
                                                         <label for="name" class=" form-control-label">
                                                         {{__('messages.address')}}
                                                         <span class="reqfield">*</span>
                                                         </label>
                                                         <?php $address = "address_".$l->code?>
                                                         <textarea  id="address_{{$l->code}}" placeholder="{{__('messages.address')}}" class="form-control h150" name="address_{{$l->code}}" required >{{isset($data->$address)?$data->$address:''}}</textarea>
                                                      </div>

                                                  </div>
                                                  <?php $k++;?>
                                                @endforeach
                                         </div>
                                      </div>
                         
                        <div class="form-group col-md-12">
                           <label for="name" class=" form-control-label">
                           {{__('messages.default_country')}}
                           <span class="reqfield">*</span>
                           </label>
                           <select class="form-control" name="default_country" id="default_country" required="">
                              <option value="">{{__('messages.select_country')}}</option>
                              @foreach($country as $co)
                              <option value="{{$co->id}}" <?=$data->default_country ==$co->id ? ' selected="selected"' : '';?>>{{$co->nicename}}</option>
                              @endforeach
                           </select>
                        </div>
                        <div class="form-group col-md-12">
                           <label for="name" class=" form-control-label">
                           {{__('messages.default_locales')}}
                           <span class="reqfield">*</span>
                           </label>
                           <select class="form-control" name="default_locale" id="default_locale" required="">
                              <option value="">{{__('messages.select_locale')}}</option>
                              @foreach($langdata as $la)
                              <option value="{{$la->id}}" <?=$data->default_locale ==$la->id ? ' selected="selected"' : '';?>>{{$la->name}}</option>
                              @endforeach
                           </select>
                        </div>
                        <div class="form-group col-md-12">
                           <label for="name" class=" form-control-label">
                           {{__('messages.default_timezone')}}
                           <span class="reqfield">*</span>
                           </label>
                           <select class="form-control" name="timezone" id="timezone" required="">
                              <option value="">{{__('messages.select_timezone')}}</option>
                              @foreach($timezone as $tz=>$value)
                              <option value="{{$tz}}" <?=$data->default_timezone ==$tz ? ' selected="selected"' : '';?>>{{$value}}</option>
                              @endforeach
                           </select>
                        </div>
                        <div class="form-group col-md-12">
                           <label for="name" class=" form-control-label">
                           {{__('messages.default_currency')}}
                           <span class="reqfield">*</span>
                           </label>
                           <select class="form-control" name="currency" id="currency" required="">
                              <option value="{{$data->default_currency}}" selected>{{$data->default_currency}}</option>
                              @include('admin.setting.currency')
                           </select>
                        </div>
                        <!--<div class="col-md-6">-->
                        <!--    <label for="name" class=" form-control-label">-->
                        <!--        {{__('messages.commission')}}-->
                        <!--        <span class="reqfield">*</span>-->
                        <!--    </label>-->
                        <!--    <input type="text" id="commission"  class="form-control" name="commission" required value="{{isset($data->commission)?$data->commission:''}}">-->
                        <!--</div>-->
                         <div class="col-md-6">
                            <label for="name" class=" form-control-label">
                                Dilevery Charge
                                <span class="reqfield">*</span>
                            </label>
                            <input type="text" id="delivery_charges"  class="form-control" name="delivery_charges" required value="{{isset($data->delivery_charges)?$data->delivery_charges:''}}">
                        </div>
                        <div class="form-group col-md-12 cmr1" >
                           <div class="col col-md-12">
                              <div class="form-check">
                                 <div class="status">
                                    <label for="checkbox1" class="form-check-label ">
                                    <input type="checkbox" id="is_customer_order" onclick="changeboxoption(this.value,'is_customer_order')" name="is_customer_order" value="1" class="form-check-input" <?=$data->customer_order_status =='1' ? ' checked="checked"' : '';?>>
                                    {{__('messages.customer_order_confirm_email')}}</label>
                                 </div>
                              </div>
                              <div class="form-check hidd">
                                 <div class="status">
                                    <label for="checkbox1" class="form-check-label ">
                                    <input type="checkbox" id="is_email_confirm" name="is_email_confirm" value="1" class="form-check-input" <?=$data->customer_reg_email =='1' ? ' checked="checked"' : '';?>>
                                    {{__('messages.is_email_confirm')}}</label>
                                 </div>
                              </div>
                              <div class="form-check">
                                 <div class="status">
                                    <label for="checkbox1" class="form-check-label ">
                                    <input type="checkbox" id="is_admin_send_mail" onclick="changeboxoption(this.value,'is_admin_send_mail')" name="is_admin_send_mail" value="1" class="form-check-input" <?=$data->admin_order_mail =='1' ? ' checked="checked"' : '';?>>
                                    {{__('messages.is_admin_send_mail')}}</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-12"> 
                                @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                               {{__('messages.save')}}
                                        </button>
                                     @else
                                         <button class="btn btn-primary florig" type="submit"  > {{__('messages.update')}}</button>
                                     @endif 
                         
                        </div>
                          </form>
                       </div>
                  </div>
                  <div class="tab-pane fade" id="login" role="tabpanel" aria-labelledby="login-tab">
                     <div class="cmr1">
                        <div class="form-group col-md-12 paddiv" style="display:none">
                           <div class="col-md-6">
                              <label for="name" class=" form-control-label" style="display:none">
                              {{__('messages.facebook_api_id')}}
                              <span class="reqfield">*</span>
                              </label>
                              <input type="text" id="facebook_id" placeholder="{{__('messages.facebook_api_id')}}" class="form-control" name="facebook_id" required value="{{$data->facebook_id}}">
                           </div>
                           <div class="col-md-6">
                              <label for="name" class=" form-control-label" style="display:none">
                              {{__('messages.facebook_secret')}}
                              <span class="reqfield">*</span>
                              </label>
                              <input type="text" id="facebook_secret" placeholder="{{__('messages.facebook_secret')}}" class="form-control" name="facebook_secret" required value="{{$data->facebook_secret}}">
                           </div>
                        </div>
                        <div class="form-group col-md-12 paddiv" style="display:none">
                           <div class="col-md-6">
                              <label for="name" class=" form-control-label">
                              {{__('messages.google_api_id')}}
                              <span class="reqfield">*</span>
                              </label>
                              <input type="text" id="google_id" placeholder="{{__('messages.google_api_id')}}" class="form-control" name="google_id" required value="{{$data->google_id}}">
                           </div>
                           <div class="col-md-6">
                              <label for="name" class=" form-control-label">
                              {{__('messages.google_secret')}}
                              <span class="reqfield">*</span>
                              </label>
                              <input type="text" id="google_secret" placeholder="{{__('messages.google_secret')}}" class="form-control" name="google_secret" required value="{{$data->google_secret}}">
                           </div>
                        </div>
                        <div class="form-group col-md-12 cmr1">
                          
                              <div class="form-check">
                                 <div class="status">
                                    <label for="checkbox1" class="form-check-label ">
                                    <input type="checkbox" id="is_facebook_required" name="is_facebook_required" value="1" class="form-check-input" <?=$data->facebook_active =='1' ? ' checked="checked"' : '';?>>
                                    {{__('messages.enable_facebook_login')}}</label>
                                 </div>
                              </div>
                              <div class="form-check">
                                 <div class="status">
                                    <label for="checkbox1" class="form-check-label ">
                                    <input type="checkbox" id="is_google_required" name="is_google_required" value="1" class="form-check-input" <?=$data->google_active =='1' ? ' checked="checked"' : '';?>>
                                    {{__('messages.enable_google_login')}}</label>
                                 </div>
                              </div>
                         
                        </div>
                        <p>{{__('messages.soical_note')}} :- </p>
                            <span><b>{{__('messages.facebook_redirect_url')}}:- {{url('/')}}/auth/facebook/callback</b></span><br>
                            <span><b>{{__('messages.google_redirect_url')}}:- {{url('/')}}/auth/google/callback</b></span>
                        <div class="form-group col-md-12">
                              @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.update')}}
                                        </button>
                                     @else
                                             <button class="btn btn-primary florig" type="button" onclick="savesoicallogin()" >{{__('messages.update')}}</button>
                                     @endif 
                      
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                     <div class="cmr1">
                        <div class="table-responsive dtdiv">
                           <table id="shippingTable" class="table table-striped table-bordered dttablewidth">
                              <thead>
                                 <tr>
                                    <th>{{__('messages.id')}}</th>
                                    <th>{{__('messages.label')}}</th>
                                    <th>{{__('messages.cost')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                 </tr>
                              </thead>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                     <div class="cmr1">
                        <ul class="nav nav-tabs" id="nav-tab" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link active show" id="pay1-tab" data-toggle="tab" href="#pay1" role="tab" aria-controls="pay1" aria-selected="true">{{__('messages.paypal')}}</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="pay2-tab" data-toggle="tab" href="#pay2" role="tab" aria-controls="pay2" aria-selected="true">{{__('messages.stripe')}}</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="pay3-tab" data-toggle="tab" href="#pay3" role="tab" aria-controls="pay3" aria-selected="true">{{__('messages.case_on_delivery')}}</a>
                           </li>
                        </ul>
                        <div class="tab-content pl-3 p-1" id="nav-tabContent">
                           <div class="tab-pane fade active show" id="pay1" role="tabpanel" aria-labelledby="pay1-tab">
                              <div class="cmr1">
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.status')}}</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <div class="form-check">
                                          <div class="status">
                                             <label for="checkbox1" class="form-check-label ">
                                             <input type="checkbox" id="is_enable1" name="is_enable1" value="1" class="form-check-input" <?=$paymentmethod[0]->status =='1' ? ' checked="checked"' : '';?>>{{__('messages.enable')}} {{$paymentmethod[0]->label}}
                                             </label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.label')}} <span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <input type="text" id="pay1_label" placeholder="{{__('messages.label')}}" class="form-control" value="{{$paymentmethod[0]->label}}" name="pay1_label" required>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.description')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <textarea id="pay1_desc" name="pay1_desc" required="" class="form-control">{{$paymentmethod[0]->description}}
                                       </textarea>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.sandbox')}}</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <div class="form-check">
                                          <div class="status">
                                             <label for="checkbox1" class="form-check-label ">
                                             <input type="checkbox" id="is_paymentmode" name="is_paymentmode" value="1" class="form-check-input" <?=$paymentmethod[0]->payment_mode =='1' ? ' checked="checked"' : '';?>>{{__('messages.use_sandbox')}}
                                             </label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.api_key')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <input type="text" id="pay1_key" placeholder="{{__('messages.api_key')}}" class="form-control" value="{{$paymentmethod[0]->payment_key}}" name="pay1_key" required>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.secret')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <input type="password" id="pay1_secret_key" placeholder="{{__('messages.secret')}}" class="form-control" value="{{$paymentmethod[0]->payment_secret}}" name="pay1_secret_key" required>
                                    </div>
                                 </div>
                                 <div class="form-group col-md-12">
                                     @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.update')}}
                                        </button>
                                     @else
                                             <button class="btn btn-primary florig" type="button" onclick="changepayment(1)" >{{__('messages.update')}}</button>
                                     @endif 
                                  
                                 </div>

                              </div>
                           </div>
                           <div class="tab-pane fade" id="pay2" role="tabpanel" aria-labelledby="pay2-tab">
                              <div class="cmr1">
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.status')}}</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <div class="form-check">
                                          <div class="status">
                                             <label for="checkbox1" class="form-check-label ">
                                             <input type="checkbox" id="is_enable2" name="is_enable2" value="1" class="form-check-input" <?=$paymentmethod[1]->status =='1' ? ' checked="checked"' : '';?>>{{__('messages.enable')}} {{$paymentmethod[1]->label}}
                                             </label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.label')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <input type="text" id="pay2_label" placeholder="{{__('messages.label')}}" class="form-control" value="{{$paymentmethod[1]->label}}" name="pay2_label" required>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.description')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <textarea id="pay2_desc" name="pay2_desc"  class="form-control" required="">{{$paymentmethod[1]->description}}
                                       </textarea>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.api_key')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <input type="text" id="pay2_key" placeholder="{{__('messages.api_key')}}" class="form-control" value="{{$paymentmethod[1]->payment_key}}" name="pay2_key" required>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.secret')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <input type="password" id="pay2_secret_key" placeholder="{{__('messages.secret')}}" class="form-control" value="{{$paymentmethod[1]->payment_secret}}" name="pay2_secret_key" required>
                                    </div>
                                 </div>
                                 <div class="form-group col-md-12">
                                      @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.update')}}
                                        </button>
                                     @else
                                               <button class="btn btn-primary florig" type="button"  onclick="changepayment(2)" >{{__('messages.update')}}</button>
                                     @endif 
                                 
                                 </div>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="pay3" role="tabpanel" aria-labelledby="pay3-tab">
                              <div class="cmr1">
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.status')}}</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <div class="form-check">
                                          <div class="status">
                                             <label for="checkbox1" class="form-check-label ">
                                             <input type="checkbox" id="is_enable3" name="is_enable3" value="1" class="form-check-input" <?=$paymentmethod[2]->status =='1' ? ' checked="checked"' : '';?>>{{__('messages.enable')}} {{$paymentmethod[2]->label}}
                                             </label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.label')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <input type="text" id="pay3_label" placeholder="{{__('messages.label')}}" class="form-control" value="{{$paymentmethod[2]->label}}" name="pay3_label" required>
                                    </div>
                                 </div>
                                 <div class="row form-group col-md-12">
                                    <div class="col col-md-3">
                                       <label for="text-input" class=" form-control-label">{{__('messages.description')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                       <textarea id="pay3_desc" name="pay3_desc"  class="form-control" required="">{{$paymentmethod[2]->description}}
                                       </textarea>
                                    </div>
                                 </div>
                                 <div class="form-group col-md-12">
                                       @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.update')}}
                                        </button>
                                     @else
                                               <button class="btn btn-primary florig" type="button"  onclick="changepayment(3)" >{{__('messages.update')}}</button>
                                     @endif
                                   
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
<div class="modal fade" id="editshipping" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
   <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="smallmodalLabel">{{__('messages.edit')}} {{__('messages.shipping_method')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{url('admin/updateshipping')}}" method="post">
            {{csrf_field()}}
            <input type="hidden" name="id" id="id" />
            <div class="modal-body">
               <div class="form-group">
                  <label for="cc-payment" class="control-label mb-1">{{__('messages.label')}} </label>
                  <input id="label" name="label" type="text" class="form-control"  value="" readonly="">
               </div>
               <div class="form-group">
                  <label for="cc-payment" class="control-label mb-1">{{__('messages.cost')}} </label>
                  <input id="cost" name="cost" type="text" class="form-control"  value="" >
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}} </button>
               <button type="submit" class="btn btn-primary">{{__('messages.update')}} </button>
            </div>
         </form>
      </div>
   </div>
</div>
@stop