<?php  //echo "<pre>";print_r($user);die();?>
@extends('front.layout')



@section('content')
<section class="section pt-5 pb-5">
  <div class="container">
    <div class="col-md-12">
    	 
      <h4 class="font-weight-bold mt-0 mb-3">Available Orders</h4>
    </div>
    <div class="order_data  mb-4 pb-2">
      
    </div>
  </div>
</section>
@endsection

@section('script')
<script type="text/javascript">


$(document).ready(function(){
  order_data(0);
});
</script>

@endsection   
  