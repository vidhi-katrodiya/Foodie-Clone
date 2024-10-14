@extends('front.layout')
@section('title')
{{__("messages.Invoice")}}
@endsection
@section('content')

@if($data != "")
<section class="breadcrumb-osahan pt-5 pb-5 bg-dark position-relative text-center">
  <h1 class="text-white">Invoice</h1>
  <h6 class="text-white-50">{{$data->order_no}}</h6>
</section>
<section class="section pt-5 pb-5">
  <div class="container">
    <div class="row">
      <div class="col-md-8 mx-auto">
        <div class="p-5 osahan-invoice bg-white shadow-sm">
          <div class="row mb-5 pb-3 ">
            <div class="col-md-8 col-10">
              <h3 class="mt-0">Thanks for choosing <strong class="text-secondary">Osahan Eat</strong>, {{$user->first_name}} ! Here are your order details: </h3>
            </div>
            <div class="col-md-4 col-2 pl-0 text-right">
              <p class="mb-4 mt-2">
              	<a class="text-primary font-weight-bold" href="{{url('invoice/'.$data->id)}}" download="invoice">
                  <i class="icofont-print"></i> PRINT </a>
              </p>
              <img alt="logo" src="{{asset('public/front/img/favicon.png')}}">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <p class="mb-1 text-black">Order No: <strong>{{$data->order_no}}</strong></p>
              <p class="mb-1">Order placed at: <strong><?php echo date("d-m-Y h:i", strtotime($data->orderplace_datetime))?></strong>
              </p>
              <!-- <p class="mb-1">Order delivered at: <strong><?php echo date("d-m-Y h:i", strtotime($data->complete_datetime))?></strong>
              </p> -->
              <p class="mb-1">Order Status: <strong class="text-success">Delivered</strong>
              </p>
            </div>
            <div class="col-md-6">
              <p class="mb-1 text-black">Delivery To:</p>
              <p class="mb-1 text-primary">
                <strong>{{$user->first_name}}</strong>
              </p>
              <p class="mb-1">{{$user->shipping_address}}</p>
            </div>
          </div>
          <div class="row mt-5">
            <div class="col-md-12">
              <p class="mb-1">Ordered from:</p>
              <h6 class="mb-1 text-black">
                <strong>{{$res->first_name}}</strong>
              </h6>
              <p class="mb-1">{{$res->address}}</p>
              <table class="table mt-3 mb-0">
                <thead class="thead-light">
                  <tr>
                    <th class="text-black font-weight-bold" scope="col">Item Name</th>
                    <th class="text-right text-black font-weight-bold" scope="col">Quantity</th>
                    <th class="text-right text-black font-weight-bold" scope="col">Price</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($order as $val)
                  <tr>
                    <td>{{$val->pro_name}} <br>({{$val->label}})</td>
                    <td class="text-right">{{$val->quantity}}</td>
                    <td class="text-right">₹{{number_format($val->total_amount,2)}}</td>
                  </tr>
                  @endforeach
                  <tr>
                    <td class="text-right" colspan="2">Item Total:</td>
                    <td class="text-right">₹{{number_format($data->total,2)}}</td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="2">Tax:</td>
                    <td class="text-right"> ₹{{number_format($data->tax,2)}}</td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="2">Delivery Charges:</td>
                    <td class="text-right"> ₹{{number_format($data->delivery_charge,2)}}</td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="2">Discount Applied ({{$data->coupon_code}}):</td>
                    <td class="text-right">₹{{number_format($data->coupon_price,2)}}</td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="2">
                      <h6 class="text-success">Grand Total:</h6>
                    </td>
                    <td class="text-right">
                      <h6 class="text-success"> ₹{{number_format($data->sub_total,2)}}</h6>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@else
<section class="breadcrumb-osahan pt-5 pb-5 bg-dark position-relative text-center">
  <h1 class="text-white">Invoice</h1>
</section>
<section class="section pt-5 pb-5">
	<div class="container">
	    <div class="row">
		    <div class="col-md-8 mx-auto">
		        <div class="p-5 osahan-invoice bg-white shadow-sm">
		         	<div class="row mb-5 pb-3 ">
			            <div class="col-md-8 col-10">
			              <h3 class="mt-0">Thanks for choosing <strong class="text-secondary">Osahan Eat</strong>, {{$user->first_name}} ! Here are your order details: </h3>
			            </div>
			            <div class="col-md-4 col-2 pl-0 text-right">
			              <p class="mb-4 mt-2">
			                <a class="text-primary font-weight-bold" href="{{url('invoice/'.$data->id)}}" download>
			                  <i class="icofont-print"></i> PRINT </a>
			              </p>
			              <img alt="logo" src="{{asset('public/front/img/favicon.png')}}">
			            </div>
		          	</div>
			        <div class="row">
			            <div class="col-md-6">
			            	<h6 class="text-success"> Your Order Is Empty</h6>
			            </div>
			        </div>
		    	</div>
			</div>
		</div>
	</div>
</section>
@endif

@endsection
@section('script')
@endsection
