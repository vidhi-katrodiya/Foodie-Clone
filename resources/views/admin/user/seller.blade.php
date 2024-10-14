@extends('admin.index')
@section('content')
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.Seller')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.Seller')}}</li>
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
               <button  data-toggle="modal" data-target="#adduser" class="btn btn-primary btn-flat m-b-30 m-t-30" >{{__('messages.add_seller')}}</button>
               <div class="table-responsive dtdiv">
                  <table id="sellerTable" class="table table-striped table-bordered dttablewidth">
                     <thead>
                        <tr>
                           <th>{{__('messages.id')}}</th>
                           <th>{{__('messages.name')}}</th>
                           <th>{{__('messages.email')}}</th>
                           <th>{{__('messages.phone')}}</th>
                           <th>{{__('messages.action')}}</th>
                        </tr>
                     </thead>
                  </table>
               </div>
            </div>
         </div>
      </div>
   <div class="modal fade" id="adduser" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
      <div class="modal-dialog " role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="smallmodalLabel">{{__('messages.add_seller')}}</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="col-sm-12 show_email_error" style="margin-top: 10px; margin-bottom: 10px; display: none">
               <div class="alert-danger alert-dismissible fade show"  role="alert" style="padding: 10px" id="email_error">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
            </div>
            <form action="{{url('admin/updateseller')}}" method="post">
               {{csrf_field()}}
               <input type="hidden" name="user_type" value="3"/>
               <input type="hidden" name="id" value="0"/>
               <div class="modal-body">
                  <div class="form-group col-md-12">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span></label>
                     <input id="first_name" name="first_name" type="text" class="form-control" required  placeholder="{{__('messages.first_name')}}">
                  </div>
                   <div class="form-group col-md-12">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.phone')}}<span class="reqfield">*</span></label>
                     <input id="phone" name="phone" type="text" class="form-control"  placeholder="{{__('messages.phone')}}" required="">
                  </div>
                  <div class="col-md-12 form-group">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.email')}}<span class="reqfield">*</span></label>
                     <input id="email" name="email" type="text" onchange="check_email()" id="email" class="form-control"  placeholder="{{__('messages.email')}}">
                     <!-- <span id="email_error"></span> -->
                  </div>
                  <div class="col-md-12 form-group">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.password')}}<span class="reqfield">*</span></label>
                     <input id="password" name="password" type="password"  class="form-control" required placeholder="****">
                  </div>
                  <div class="col-md-12 form-group">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.confirm_password')}}<span class="reqfield">*</span></label>
                     <input id="confirm_password" name="confirm_password" type="password" class="form-control" required placeholder="***" onchange="checkbothpwd(this.value)">
                  </div>
                  
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                     @if(Session::get("is_demo")=='1')
                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary">
                                 {{__('messages.save')}}
                        </button>
                     @else
                        <button type="submit" class="btn btn-primary " id="add-btn">{{__('messages.submit')}}</button>
                     @endif 
               </div>
            </form>
         </div>
      </div>
   </div>
   <div class="modal fade" id="edituser" tabindex="-1" role="dialog" aria-labelledby="smallmodalLabel" aria-hidden="true">
      <div class="modal-dialog " role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="smallmodalLabel">
                  {{__('messages.edit_seller')}}
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form action="{{url('admin/updateseller')}}" method="post">
               {{csrf_field()}}
                 <input type="hidden" name="user_type" value="3"/>
               <input type="hidden" name="id" id="id" value="0"/>
               <div class="modal-body">
                  <div class="form-group col-md-12">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span></label>
                     <input id="edit_first_name" name="first_name" type="text" class="form-control" required  placeholder="{{__('messages.first_name')}}">
                  </div>
                  <div class="form-group col-md-12">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.email')}}<span class="reqfield">*</span></label>
                     <input id="edit_email" name="email" type="text" id="email" class="form-control" readonly placeholder="{{__('messages.email')}}">
                  </div>
                  <div class="form-group col-md-12">
                     <label for="cc-payment" class="control-label mb-1">{{__('messages.phone')}}<span class="reqfield">*</span></label>
                     <input id="edit_phone" name="phone" type="text" class="form-control"  placeholder="{{__('messages.phone')}}" required="">
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('messages.cancel')}}</button>
                  @if(Session::get("is_demo")=='1')
                     <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary">
                              {{__('messages.save')}}
                     </button>
                  @else
                      <button type="submit" class="btn btn-primary edit-btn">{{__('messages.save')}}</button>
                  @endif
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@stop
<script type="text/javascript">
   function  check_email()
    {
      
      var email=$("#email").val();
    
         $.ajax({
            url: $("#url_path").val()+"/admin/check_email" ,
            method:"POST",
            data: {"_token": "{{ csrf_token() }}",'email':email},
            success: function(data) {
              if(data == 1)
              {
                  
                  $("#password").val("");
                  $("#confirm_password").val("");
                  $(".show_email_error").css("display", "");
                  document.getElementById("email_error").innerHTML ="This email id already exist.";
                  
                  $("#add-btn").attr("disabled", true);
              }
              if(data == 0)
              {
                $("#add-btn").attr("disabled", false);
                document.getElementById("email_error").innerHTML ="";
                $(".show_email_error").css("display", "none");
              }
              

            }
        });
    } 
</script>