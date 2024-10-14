<?php  //echo "<pre>";print_r($user);die();?>
@extends('front.layout')


@section('title')
  {{__("messages.address_list")}}
@endsectio

@section('content')
<section class="section pt-5 pb-5">
  <div class="container">
    <div class="col-md-12">
    	 <a href="{{url('add_address/'.$user->id)}}" type="button"  class="btn btn-sm btn-primary mr-2" style="float:right;">ADD NEW ADDRESS</a>
      <h4 class="font-weight-bold mt-0 mb-3">Available Addresses</h4>
    </div>
    <div class="row address_data  mb-4 pb-2">
      
    </div>
  </div>
</section>


<div class="modal fade" id="delete-address-modal" tabindex="-1" role="dialog" aria-labelledby="delete-address" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">

          <form  id="basicform" method="post" action="{{route('delete_address')}}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="delete-address">Delete</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <input type="hidden" value="" class="address_id" name="address_id">
              <p class="mb-0 text-black">Are you sure you want to delete this Address..?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn d-flex w-50 text-center justify-content-center btn-outline-primary" data-bs-dismiss="modal">CANCEL </button>
              <button type="submit" class="btn d-flex w-50 text-center justify-content-center btn-primary">DELETE</button>
            </div>
          </form>
          
        </div>
      </div>
</div>
@endsection

@section('script')
<script type="text/javascript">


$(document).ready(function(){
  address_data(0);
});
</script>

@endsection   
  