@extends('front.layout')
@section('title')
{{__("message.restuarnt detail")}}
@endsection
@section('content')
<style type="text/css">

.pick-order-bar-box-active{
	width: 190px;
    height: 3px;
    background-color: #dd646e;
    margin-top: 35px;
}

.pick-order-bar-box-de-active{
	width: 190px;
    height: 3px;
    background-color: #B3B3B3;
    margin-top: 35px;
}

.order-heading-box {
    display: flex;
    justify-content: space-evenly;
    padding: 0px 30px 0px 0px;
}

.pick-order-process-main-box {
    height: auto;
    display: flex;
    text-align: center;
    padding: 10px 150px 30px 140px;
}
</style>



<section class="section pt-4 pb-4 osahan-account-page">
  	<div class="container">
	    <div class="row">
	    	<div class="col-md-12">
		        <div class="osahan-account-page-right rounded shadow-sm bg-white p-4 h-100">
			        <div class="tab-content" id="myTabContent">
			            <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
			            	<span class="float-right font-weight-bold"> OrderId :
	                        	{{$order_record->order_no}}
	                        </span>
			                <h4 class="font-weight-bold mt-0 mb-4"> Order Detail</h4>

			              	<div class="order-main-box" >
		              		
		              		@if($order_record->shipping_method == 0)
								
								<div class="pick-order-process-main-box" style="display:flex; ">
									<div class="order-icons-box">
										<?php
											$status=$order_record->status;
										?>
										@if($status >= 0)

											<img style="height:80px;" src="{{asset('public/front/img/check-out/clipboard-list.png')}}">
											</div>
											<div class="pick-order-bar-box-active" ></div>
										@else
											<img style="height:80px;" src="{{asset('public/front/img/check-out/Order placed none.png')}}">
												<div class="pick-order-bar-box-de-active"></div>
										@endif
											<div class="order-icons-box">
										@if($status >= 1 )
											<img style="height:80px;" src="{{asset('public/front/img/check-out/time-left.png')}}" >
											</div>
											<div class="pick-order-bar-box-active"></div>
										@else
											<img style="height:80px;" src="{{asset('public/front/img/check-out/Preparing none.png')}}">
											</div>
											<div class="pick-order-bar-box-de-active"></div>
										@endif
											<div class="order-icons-box">
										@if($status >= 5)
											<img style="height:80px;" src="{{asset('public/front/img/check-out/location.png')}}">
											</div>
												<div class="pick-order-bar-box-active"></div>
										@else
											<img style="height:80px;" src="{{asset('public/front/img/check-out/dispatching none.png')}}">
											</div>
											<div class="pick-order-bar-box-de-active"></div>
										@endif
											<div class="order-icons-box">
										@if($status == 7)
											<img style="height:80px;" src="{{asset('public/front/img/check-out/cash-on-delivery.png')}}" >
										@else
											<img style="height:80px;" src="{{asset('public/front/img/check-out/dilivered none.png')}}" >
										@endif
									</div>
								</div>
								<div class="order-heading-box">
						        	<div class="order-head-part-box">
										
										<h5>{{__('messages.order_place')}}</h5>
										<p class="main-text">
											 <?php if($order_record->orderplace_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->orderplace_datetime));}else{echo "";}?>
										</p>
									</div>
								
						        	<div class="order-head-part-box">
										
										<h5>Preparing</h5>
										<p class="main-text"><?php if($order_record->prepare_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->prepare_datetime));}else{echo "";}?></p>
									</div>
						      
						        	<div class="order-head-part-box">
										
										<h5>Wait for pickup</h5>
										<p class="main-text"><?php if($order_record->reject_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->reject_datetime));}else{echo "";}?></p>
									</div>
								
						        	<div class="order-head-part-box">
										
										<h5>Pickup</h5>
										<p class="main-text"><?php if($order_record->pickup_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->pickup_datetime));}else{echo "";}?></p>
									</div>
								</div>
							@else
								
								<div class="pick-order-process-main-box" style="display:flex; ">
									<div class="order-icons-box">
										<?php
											$status=$order_record->status;
										?>
										@if($status >= 0)
											<img style="height:80px;" src="{{asset('public/front/img/check-out/clipboard-list.png')}}">
											</div>
											<div class="pick-order-bar-box-active" ></div>
										@else
											<img style="height:80px;" src="{{asset('public/front/img/check-out/Order placed none.png')}}">
												<div class="pick-order-bar-box-de-active"></div>
										@endif
											<div class="order-icons-box">
										@if($status >= 1)
											<img style="height:80px;" src="{{asset('public/front/img/check-out/shopping-bag.png')}}">
											</div>
											<div class="pick-order-bar-box-active" ></div>
										@else
											<img style="height:80px; border:2px solid #b3b3b3; border-radius:50%;" src="{{asset('public/front/img/check-out/shopping-bag (1).png')}}">
											</div>
												<div class="pick-order-bar-box-de-active"></div>
										@endif
											<div class="order-icons-box">
										@if($status >= 2)
											<img style="height:80px;" src="{{asset('public/front/img/check-out/time-left.png')}}" >
											</div>
											<div class="pick-order-bar-box-active"></div>
										@else
											<img style="height:80px;" src="{{asset('public/front/img/check-out/Preparing none.png')}}">
											</div>
											<div class="pick-order-bar-box-de-active"></div>
										@endif
											<div class="order-icons-box">
										@if($status >= 6)
											<img style="height:80px;" src="{{asset('public/front/img/check-out/location.png')}}">
											</div>
												<div class="pick-order-bar-box-active"></div>
										@else
											<img style="height:80px;" src="{{asset('public/front/img/check-out/dispatching none.png')}}">
											</div>
											<div class="pick-order-bar-box-de-active"></div>
										@endif
											<div class="order-icons-box">
										@if($status == 7)
											<img style="height:80px;" src="{{asset('public/front/img/check-out/fast-delivery.png')}}" >
										@else
											<img style="height:80px; border:2px solid #b3b3b3; border-radius:50%;" src="{{asset('public/front/img/check-out/fast-delivery (1).png')}}" >
										@endif
									</div>
								</div>
								<div class="order-heading-box">
									<div class="order-head-part-box">
										
										<h5>{{__('messages.order_place')}}</h5>
										<p class="main-text">
											<?php if($order_record->orderplace_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->orderplace_datetime));}else{echo "";}?>
										</p>
									</div>
									<div class="order-head-part-box">
										<h5>Confirmed</h5>
										<p class="main-text"><?php if($order_record->accept_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->accept_datetime));}else{echo "";}?></p>
									</div>
									<div class="order-head-part-box">
										<h5>Prepared</h5>
										<p class="main-text"><?php if($order_record->prepare_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->prepare_datetime));}else{echo "";}?></p>
									</div>
						        	<div class="order-head-part-box">
										<h5>Dispatched</h5>
										<p class="main-text"><?php if($order_record->out_for_delivery_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->out_for_delivery_datetime));}else{echo "";}?></p>
									</div>
									<div class="order-head-part-box">
										<h5>Delivered</h5>
										<p class="main-text"><?php if($order_record->complete_datetime >0){echo date("d-m-Y h:i", strtotime($order_record->complete_datetime));}else{echo "";}?></p>
									</div>
								</div>
							@endif
							</div>

							
							

				            <!-- <div class="progress" style="margin-bottom:20px; height:12px;">
							  	<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%; background-color: #dd646e !important">
							  	</div>
							</div> -->
      
			                <div class="order_data  mb-4 pb-2">
			                	@foreach($order_data as $value)
			                		@php
			                			$sum = array_sum( explode( '-', $value->option_price ) );
			                		@endphp
			                		<div class="bg-white card mb-4 order-list shadow-sm">
						                <div class="gold-members p-4">
						                  	<a href="#">
							                    <div class="media">
							                      	<img class="mr-4" src="{{asset('public/upload/product/'.$value->productdata->basic_image)}}" alt="Generic placeholder image" style="width:183px; height:183px;">
								                    <div class="media-body">
								                    	<span class="float-right font-weight-bold"> Item Price :
								                        	₹{{number_format($value->total_amount,2)}}
								                        	<i class="icofont-check-circled text-success"></i>
								                        </span>
								                        
								                        <h6 class="mb-2">
								                          <a href="detail.html" class="text-black font-weight-bold" style=" font-size: 23px;">{{$value->productdata->name}}</a>
								                        </h6><hr>
								                        <p class="text-gray mb-3">
								                        	<span class="text-black font-weight-bold">
								                          	Quantity : </span>{{$value->quantity}} 
								                        </p>
								                        <p class="text-gray mb-3">
								                       		<span class="text-black font-weight-bold">
								                          	Profduct Price : </span> ₹{{number_format($value->price,2)}}
								                        </p>
								                        <p class="text-gray mb-3">
								                          <span class="text-black font-weight-bold">{{$value->label}}</span>
								                        </p>
								                        <p class="text-gray mb-3">
								                          <span class="text-black font-weight-bold">Option Price :  </span> ₹{{number_format($sum,2)}}
								                        </p>
								                    </div>
							                    </div>
						                  	</a>
						                </div>
						            </div>
			                	@endforeach
			                	<div class="bg-white card mb-4 order-list shadow-sm">
			                		<div class="gold-members p-4">
					                	<p> 
				                			<span class="text-black" style="font-weight: 700; font-size: 15px;">Total : </span> 
			                				<span style="color:#dd646e; font-weight: 700; font-size: 15px;" class="float-right">₹{{number_format($order_record->total,2)}}</span>
			                			</p>
					                  	@if($order_record->coupon_price != "0")
					                  		<p>
					                  			<span class="text-black" style="font-weight: 700; font-size: 15px;">Discount ({{$order_record->coupon_code}}) : </span>
					                  			<span style="color:#dd646e; font-weight: 700; font-size: 15px;" class="float-right">₹{{number_format($order_record->coupon_price,2)}}</span>
					                  		</p>
					                  	@else
					                  		<p>
					                  			<span class="text-black" style="font-weight: 700; font-size: 15px;">Discount : </span>
					                  			<span style="color:#dd646e; font-weight: 700; font-size: 15px;" class="float-right">₹0.00</span>
					                  		</p>
					                  	@endif
					                  	@if($order_record->delivery_charge != "0")
					                  		<p>
					                  			<span class="text-black" style="font-weight: 700; font-size: 15px;">Delivery Charges : </span>
					                  			<span style="color:#dd646e; font-weight: 700; font-size: 15px;" class="float-right">₹{{number_format($order_record->delivery_charge,2)}}</span>
					                  		</p>
					                  		<p >
					                  			<span class="text-black" style="font-weight: 700; font-size: 15px;">Tax : </span>
					                  			<span style="color:#dd646e; font-weight: 700; font-size: 15px;" class="float-right">₹{{number_format($order_record->tax,2)}}</span>
					                  		</p>
					                  	@endif
					                  	<hr>
					                  	<p >
					                  		<span class="text-black" style="font-weight: 700; font-size: 15px;">Bill Total :  </span>
					                  		<span style="color:#dd646e; font-weight: 700; font-size: 15px;" class="float-right">₹{{number_format($order_record->sub_total, 2)}}</span>
					                  	</p>
						                	
						            </div>

						        </div>
						        <a class="btn btn-sm btn-outline-primary float-right" href="{{route('my_account')}}" style="margin-bottom: 16px; ">
						                    	<i class="icofont-refresh"></i> Back </a>
							</div>
							
   						</div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</section>
		

@endsection
@section('script')
@endsection
