
@extends('seller.index') @section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.option')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li><a href="{{url('seller/product')}}">{{__('messages.catalog')}}</a></li>
               <li class="active">{{__('messages.save')}} {{__('messages.option')}}</li>
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
               <h4>{{__('messages.save')}} {{__('messages.option')}}<button type="button" data-toggle="modal" data-target="#Set-Customisation" style="float: right;" class="btn btn-outline-secondary fleft" >{{__('messages.add_new_option')}}</button></h4>
            </div>
            <div class="card-body">
               <form action="{{url('seller/saveproductoption')}}" method="post">
                  {{csrf_field()}}
                  <input type="hidden" name="product_id" id="product1" value="{{$product_id}}"/>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="categories-accordion mrg30" uk-accordion="targets: > div > .category-wrap">
                           <div class="categories-sort-wrap uk-sortable uk-margin-top" uk-sortable="handle: .sort-categories" id="optionlist">
                              <?php $i=0; ?>
                              @if(count($data)>0)
                                 @foreach($data as $d)
                                    <?php if($i>=0){$cls_active="uk-open";}else{$cls_active="";}?>
                                     <div class="category-wrap1 <?=$cls_active;?>" data-id="{{$i}}" id="mainoption{{$i}}">
                                      @foreach($lang as $l)
                                       <h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small">
                                          <div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>

                                          {{$d[$l->code]->name}}
                                          <button type="button" class="btn" style="float: right; margin-top: -5px; background:none !important; color: #e9ecef; " onclick="removeoption('{{$i}}','{{$l->code}}')"><i class="fa fa-trash f-s-25"></i>
                                          </button>
                                       </h3>
                                       <input required name="options[{{$i}}][{{$l->code}}][name]" type="hidden" class="form-control" aria-required="true" aria-invalid="false" value="{{$d[$l->code]->name}}">
                                        <input required name="options[{{$i}}][{{$l->code}}][min_item_selection]" type="hidden" class="form-control" aria-required="true" aria-invalid="false" value="{{$d[$l->code]->min_item_selection}}">
                                        <input required name="options[{{$i}}][{{$l->code}}][max_item_selection]" type="hidden" class="form-control" aria-required="true" aria-invalid="false" value="{{$d[$l->code]->max_item_selection}}">
                                      @endforeach
                                       <div class="uk-accordion-content categories-content ">
                                          
                                             <div class="custom-tab">
                                                <nav class="col-md-12 tabcatlog" style="display: none;">
                                                      <?php $k=0;?>
                                                      @foreach($lang as $l)
                                                   <div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist">
                                                      <a class="nav-item nav-link {{$k==0?'active':'tabdiv'}}" id="step_tab_attr{{$l->code}}{{$i}}" data-toggle="tab" href="#stepattr{{$l->code}}{{$i}}" role="tab" aria-controls="stepattr{{$l->code}}{{$i}}" aria-selected="true">{{$l->name}}</a>  
                                                      
                                                   </div>
                                                      <?php $k++; ?>
                                                      
                                                      @endforeach  
                                                </nav>
                                                <div class="tab-content col-md-12 p-0 " id="nav-tabContent">
                                                   <?php $k=0;?>
                                                   @foreach($lang as $l)

                                                   <div class="tab-pane fade {{$k==0?'in show active':''}}" id="stepattr{{$l->code}}{{$i}}" role="tabpanel" aria-labelledby="step_tab_attr{{$l->code}}{{$i}}" >
                                                         <div class="edit-p-list-u">
                                                            
                                                         </div>
                                                         <div id="valuesection_{{$i}}_{{$l->code}}">
                                                            
                                                            <?php $lab = explode("#",$d[$l->code]->label);
                                                                  $pri = explode("#",$d[$l->code]->price);
                                                            ?>

                                                           
                                                               <input type="hidden" name="total_option_{{$l->code}}_{{$i}}" id="total_option_{{$l->code}}_{{$i}}" value="{{count($lab)-1}}"/>

                                                               <div class="uk-sortable " uk-sortable="handle: .sort-questions" id="option_{{$l->code}}_{{$i}}">
                                                                  <?php $j=0; ?>
                                                                  @foreach($lab as $lb)
                                                                  <div class="questions-row" id="row_{{$l->code}}_{{$i}}_{{$j}}">
                                                                     <div class="uk-grid-small uk-margin-small-bottom uk-margin-small-top" uk-grid><div class="uk-width-auto"> <span class="uk-sortable-handle sort-questions uk-margin-small-right" uk-icon="icon: table"></span></div><div class="uk-width-auto" style="width:40%"><input placeholder="Enter Label" class="form-control" type="text" required name="options[{{$i}}][{{$l->code}}][label][]" value="{{$lb}}"/></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" placeholder="Enter Price" name="options[{{$i}}][{{$l->code}}][price][]" value="{{$pri[$j]}}"/></div><div class="uk-width-auto"><button type="button" class="btn btn-danger" onclick="removeoptionrow('{{$i}}','{{$j}}','{{$l->code}}')"><i class="fa fa-trash f-s-25"></i></button></div></div></div>
                                                                  <?php $j++;?>
                                                                  @endforeach
                                                                  </div><button type="button" class="btn btn-primary" onclick="addnewoptionvalue('{{$i}}','{{$l->code}}')">{{__("messages.add_new_row")}}</button>
                                                         </div>
                                                   </div>
                                                   <?php $k++; ?>
                                                   @endforeach
                                                </div>
                                             </div>

                                       </div>
                                    </div>
                                    <?php $i++; ?>
                                 @endforeach
                              @endif
                           </div>
                        </div>
                        <input type="hidden" name="totaloption" id="totaloption" value="{{$i}}" />
                        <div class="edit-p-blc">
                           <div class="col-md-12 p-0 orderdiv">
                              <div class="row">
                                 @if(count($data)>0)
                                 <div class="col-md-7 florig">
                                  
                                    <button type="submit" class="btn btn-primary fleft">{{__('messages.save')}}</button>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                                 
                     </div>
                  </div>
               </form>
               <input type="hidden" id="fixed" value='{{__("messages.Fixed")}}'>
               <input type="hidden" id="percentage" value="{{__('messages.percentage')}}">
               <input type="hidden" id="label" value='{{__("messages.label")}}'>
               <input type="hidden" id="pricemsg" value='{{__("messages.price")}}'>
               <input type="hidden" id="price_type" value='{{__("messages.price_type")}}'>
               <input type="hidden" id="add_new_row" value='{{__("messages.add_new_row")}}'>
               <input type="hidden" id="new_option" value='{{__("messages.new_option")}}'>
               <input type="hidden" id="namedis" value='{{__("messages.name")}}'>
               <input type="hidden" id="select_type" value='{{__("messages.select")}} {{__("messages.type")}}'>
               <input type="hidden" id="dropdown" value='{{__("messages.dropdown")}}'>
               <input type="hidden" id="checkbox" value='{{__("messages.checkbox")}}'>
               <input type="hidden" id="radiobutton" value='{{__("messages.radiobutton")}}'>
               <input type="hidden" id="multiple_select" value='{{__("messages.multiple_select")}}'>
               <input type="hidden" id="requireddis" value='{{__("messages.required")}}'>
               <input type="hidden" id="ple_sel_option" value="{{__('messages_error_success.ple_sel_option')}}">
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="Set-Customisation" tabindex="-1" role="dialog" aria-labelledby="edit-profile" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="justify-content: space-around;">
        <h4 class="modal-title" style="text-align: center !important;font-weight: 600;" id="edit-profile">Add Customisation</h4>
        
      </div>
      <div class="modal-body">
        <form action="{{url('seller/add_customisation')}}" post='post'>
          <div class="form-row">
            <input type="hidden" name="cut_pro_id" id='cut_pro_id' value="{{$product_id}}">
            <div class="form-group col-md-12">
              <label>Name</label>
              <input type="text" value="" name="cut_name" id='cut_name' class="form-control" placeholder="Enter Customisation Name">
            </div>
            <div class="form-group col-md-12">
              <label>Minimum</label>
              <input type="number" value="" name="min_item_selection" id='min_item_selection' class="form-control" placeholder="Enter Minimum Item Selection
                              ">
            </div>
            <div class="form-group col-md-12 mb-0">
              <label>Maximum</label>
              <input type="number" value="" name="max_item_selection" id='max_item_selection' class="form-control" placeholder="Enter Maximum Item Selection">
            </div>
          </div>
        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn d-flex w-50 text-center justify-content-center btn-primary">Add</button>
        <button type="button" class="btn d-flex w-50 text-center justify-content-center btn-outline-secondary" data-dismiss="modal">CANCEL </button>
      </div>
      </form>
    </div>
  </div>
</div>
@stop 
@section('footer')

 <script type="text/javascript">
      function addoptionvalue(opval,lang){  

         var txt='<ul class="valul"><li class="td2"></li><li class="td6">'+$("#label").val()+'</li><li class="td6">'+$("#pricemsg").val()+'</li><li class="td2"></li></ul><input type="hidden" name="total_option_'+lang+'_'+opval+'" id="total_option_'+lang+'_'+opval+'" value="1"/><div class="uk-sortable " uk-sortable="handle: .sort-questions" id="option_'+lang+'_'+opval+'"><div class="questions-row" id="row_'+lang+'_'+opval+'_1"><div class="uk-grid-small uk-margin-small-bottom uk-margin-small-top" uk-grid><div class="uk-width-auto"> <span class="uk-sortable-handle sort-questions uk-margin-small-right" uk-icon="icon: table"></span></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" required name="options['+opval+']['+lang+'][label][]" placeholder="Enter Label" value=""/></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" name="options['+opval+']['+lang+'][price][]" placeholder="Enter Price" value=""/></div><div class="uk-width-auto"><button type="button" class="btn btn-danger" onclick="removeoptionrow('+opval+',1,\'' + lang + '\')"><i class="fa fa-trash f-s-25"></i></button></div></div></div></div><button type="button" class="btn btn-primary" onclick="addnewoptionvalue('+opval+',\'' + lang + '\')">'+$("#add_new_row").val()+'</button>';
         document.getElementById("valuesection_"+opval+"_"+lang).innerHTML=txt;
      }

       function addnewoptionvalue(opval,lang){
          var lastrow=$("#total_option_"+lang+"_"+opval).val();
          var nextrow=parseInt(lastrow)+1;
          var txt='<div class="questions-row" id="row_'+lang+'_'+opval+'_'+nextrow+'"><div class="uk-grid-small uk-margin-small-bottom uk-margin-small-top" uk-grid><div class="uk-width-auto"> <span class="uk-sortable-handle sort-questions uk-margin-small-right" uk-icon="icon: table"></span></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text"  required  name="options['+opval+']['+lang+'][label][]" placeholder="Enter Label" value=""/></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text"  name="options['+opval+']['+lang+'][price][]" placeholder="Enter Price" value=""/></div><div class="uk-width-auto"><button type="button" class="btn btn-danger" onclick="removeoptionrow('+opval+','+nextrow+',\'' + lang + '\')"><i class="fa fa-trash f-s-25"></i></button></div></div></div>';
          $("#total_option_"+lang+"_"+opval).val(nextrow);
          $('#option_'+lang+'_'+opval).append(txt);
      }

      function removeoptionrow(opval,valrow,lang){
         if(valrow!=1){
             $("#row_"+lang+"_"+opval+"_"+valrow).remove();
         }
      }


       function addoption(){
          var lastoption=$("#totaloption").val();
          var nextoption=parseInt(lastoption)+1;
          $.ajax({
                url: $("#url_path").val()+"/seller/addoptionrow" + "/" + nextoption,
                data: {},
                success: function(data) {
                    $("#totaloption").val(nextoption);
                    $("#optionlist").append(data);
                }
           });
          
      }

      function removeoption(opval,lang){
         if(opval!=""){
            $("#mainoption"+opval).remove();
         }         
      }
 </script>
@stop