@extends('seller.index') @section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.attribute')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li><a href="{{url('admin/product')}}">{{__('messages.catalog')}}</a></li>
               <li class="active">{{__('messages.save')}} {{__('messages.attribute')}}</li>
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
               <h4>{{__('messages.save')}} {{__('messages.attribute')}}</h4>
            </div>
            <div class="card-body">
               <form action="{{url('seller/saveproductattibute')}}" method="post">
                  {{csrf_field()}}
                  <input type="hidden" name="product_id" id="product1" value
                     ="{{$product_id}}"/>

                  <div class="categories-accordion mrg30" uk-accordion="targets: > div > .category-wrap">
                     <div class="categories-sort-wrap uk-sortable uk-margin-top" uk-sortable="handle: .sort-categories" id="attributelist">
                        <?php $i=0;?>
                        @if(count($data)>0)
                          @foreach($data as $d)
                             <div class="category-wrap" data-id="{{$i}}" id="mainattr_{{$i}}">
                           <h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small">
                              <div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>
                              {{__('messages.New Attributes')}}
                           </h3>
                           <div class="uk-accordion-content categories-content " style="margin-top: 0px;padding:0px">
                              <div class="custom-tab">
                                 <nav class="col-md-12 tabcatlog">
                                    <div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist">
                                       <?php $k=0;?>
                                       @foreach($lang as $l)
                                       <a class="nav-item nav-link {{$k==0?'active':'tabdiv'}}" id="step_tab_attr{{$l->code}}{{$i}}" data-toggle="tab" href="#stepattr{{$l->code}}{{$i}}" role="tab" aria-controls="stepattr{{$l->code}}{{$i}}" aria-selected="true">{{$l->name}}</a>  
                                       <?php $k++; ?>
                                       @endforeach  
                                    </div>
                                 </nav>
                                 <div class="tab-content col-md-12 p-0 " id="nav-tabContent">
                                    <?php $k=0;?>
                                    @foreach($lang as $l)

                                    <div class="tab-pane fade {{$k==0?'show active':'tabdiv'}}" id="stepattr{{$l->code}}{{$i}}" role="tabpanel" aria-labelledby="step_tab_attr{{$l->code}}{{$i}}" >
                                       <table class="table table-striped table-bordered">
                                          <tbody>
                                             <tr>
                                                <td>

                                                   <input type="text" required name="attributeset[{{$i}}][{{$l->code}}][set]" class="form-control" placeholder="Enter Attribute Set" value="{{$d[$l->code]->attributeset}}">
                                                   <table class="table table-striped table-bordered cmr1">
                                                      <thead>
                                                         <tr>
                                                            <th>Attribute</th>
                                                            <th>Value</th>
                                                            <th></th>
                                                         </tr>
                                                      </thead>
                                                      <?php $la = explode(",",$d[$l->code]->label);
                                                            $pa = explode(",",$d[$l->code]->value);
                                                      ?>
                                                      <tbody id="morerow_{{$l->code}}_{{$k}}">
                                                         <?php $j=0;?>
                                                         <?php for($j=0;$j<count($pa);$j++) { ?>
                                                         <tr id="attrrow_{{$l->code}}_{{$k}}_0">
                                                            <td><input required class="form-control" type="text" name="attributeset[{{$i}}][{{$l->code}}][label][]" value="{{isset($la[$j])?$la[$j]:''}}"></td>
                                                            <td><input required class="form-control" type="text" name="attributeset[{{$i}}][{{$l->code}}][value][]"
                                                               value="{{isset($pa[$j])?$pa[$j]:''}}"></td>
                                                            <td><button type="button" onclick="removeattrrow('{{$i}}','{{$j}}','{{$l->code}}')" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td>
                                                         </tr>
                                                      <?php } ?>
                                                      </tbody>
                                                   </table>
                                                   <input type="hidden" name="totalattr_{{$l->code}}_{{$i}}" id="totalattr_{{$l->code}}_{{$i}}" value="{{$j}}"/>
                                                   <button type="button" class="btn btn-primary fleft" onclick="addattrrow('{{$i}}','{{$l->code}}')"><i class="fa fa-plus"></i>Add New Row</button>
                                                </td>
                                                <td>
                                                   <button onclick="removerowmain('{{$i}}')" type="button" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </div>
                                    <?php $k++; ?>
                                    @endforeach
                                 </div>
                              </div>
                           </div>
                        </div>
                        <?php $i++;?>
                          @endforeach
                        @else
                        <div class="category-wrap" data-id="0" id="mainattr_0">
                           <h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small">
                              <div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>
                              {{__('messages.New Attributes')}}
                           </h3>
                           <div class="uk-accordion-content categories-content " style="margin-top: 0px;padding:0px">
                              <div class="custom-tab">
                                 <nav class="col-md-12 tabcatlog">
                                    <div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist">
                                       <?php $k=0;?>
                                       @foreach($lang as $l)
                                       <a class="nav-item nav-link {{$k==0?'active':'tabdiv'}}" id="step_tab_attr{{$l->code}}" data-toggle="tab" href="#stepattr{{$l->code}}" role="tab" aria-controls="stepattr{{$l->code}}" aria-selected="true">{{$l->name}}</a>  
                                       <?php $k++; ?>
                                       @endforeach  
                                    </div>
                                 </nav>
                                 <div class="tab-content col-md-12 p-0 " id="nav-tabContent">
                                    <?php $k=0;?>
                                    @foreach($lang as $l)
                                    <div class="tab-pane fade {{$k==0?'show active':'tabdiv'}}" id="stepattr{{$l->code}}" role="tabpanel" aria-labelledby="step_tab_attr{{$l->code}}" >
                                       <table class="table table-striped table-bordered">
                                          <tbody>
                                             <tr>
                                                <td>
                                                   <input type="text" required name="attributeset[0][{{$l->code}}][set]" class="form-control" placeholder="Enter Attribute Set">
                                                   <table class="table table-striped table-bordered cmr1">
                                                      <thead>
                                                         <tr>
                                                            <th>Attribute</th>
                                                            <th>Value</th>
                                                            <th></th>
                                                         </tr>
                                                      </thead>
                                                      <tbody id="morerow_{{$l->code}}_0">
                                                         <tr id="attrrow_{{$l->code}}_0_0">
                                                            <td><input required class="form-control" type="text" name="attributeset[0][{{$l->code}}][label][]"></td>
                                                            <td><input required class="form-control" type="text" name="attributeset[0][{{$l->code}}][value][]"></td>
                                                            <td><button type="button" onclick="removeattrrow(0,0,'{{$l->code}}')" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td>
                                                         </tr>
                                                      </tbody>
                                                   </table>
                                                   <input type="hidden" name="totalattr_{{$l->code}}_0" id="totalattr_{{$l->code}}_0" value="0"/>
                                                   <button type="button" class="btn btn-primary fleft" onclick="addattrrow(0,'{{$l->code}}')"><i class="fa fa-plus"></i>Add New Row</button>
                                                </td>
                                                <td>
                                                   <button onclick="removerowmain(0)" type="button" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </div>
                                    <?php $k++; ?>
                                    @endforeach
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif

                     </div>
                  </div>
                  <div id="container"></div>
                  <div class="col-md-12 p-0">
                     <input type="hidden" name="totalrow" id="totalrow" value='{{$i}}' />
                     <button type="button" class="btn btn-outline-secondary fleft" onclick="addrow()">{{__('messages.add_new_row')}}</button>
                       <button type="submit" class="btn btn-primary florig">{{__('messages.save')}}</button>
                  </div>
            </div>
            <div class="row">
       
      </div>
      </form>
         </div>
      </div>
      
   </div>
</div>

@stop 
@section('footer')
<script type="text/javascript" src="{{asset('public/js/attributeseller.js').'?v=wewe3'}}"></script>


<script type="text/javascript">
   function addrow(){
      var row = $("#totalrow").val();
      var newrow = parseInt(row)+parseInt(1);
      $.ajax({
                url: $("#url_path").val()+"/seller/addattributerow" + "/" + newrow,
                data: {},
                success: function(data) {
                     $("#attributelist").append(data);
                     $("#totalrow").val(newrow);
                }
      });
   }
</script>
@stop