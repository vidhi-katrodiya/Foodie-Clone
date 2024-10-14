@extends('front.layout')

@section('title')
  {{__("messages.WishList")}}
@endsection

@section('content')

<style type="text/css">
 
  .img-fluid{
    width: 508px !important;
    height: 200px;
  }
</style>

 <section class="breadcrumb-osahan pt-5 pb-5 bg-dark position-relative text-center">
      <h1 class="text-white">WishList For You</h1>
      <h6 class="text-white-50">Best Product at your favourite restaurants</h6>
    </section>
    <section class="section pt-5 pb-5 products-listing">
      <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div class="row">
              @foreach($data as $res_val)
                <div class="col-md-3 col-sm-4 mb-4 pb-2">
                  <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
                    <div class="list-card-image">
                      <div class="star position-absolute">
                        <span class="badge badge-success">
                          @if ($res_val->review_count == 0)
                              <i class="icofont-star"></i> {{$res_val->rating}} ({{$res_val->review_count}}) </span>
                          @else
                              <i class="icofont-star"></i> {{$res_val->rating}} ({{$res_val->review_count}}+) </span>
                          @endif
                      </div>
                      <div class="favourite-heart text-danger position-absolute" style="height: 20px !important;width:20px !important; font-size: 18px !important;padding-right: :40px !important;">
                        <i class="fa fa-times-circle" aria-hidden="true" onclick="delete_fav_plant(<?= $res_val->id ?>)"></i>
                        
                      </div>
                      
                      <a href="{{url('res_detail/')}}/{{$res_val->id}}">
                        <img class="img-fluid item-img" src="{{url('public/upload/restaurant/')}}/{{$res_val->res_image}}" alt="">
                        </a>
                    </div>
                    <div class="p-3 position-relative">
                      <div class="list-card-body">
                        <h6 class="mb-1">
                          <a href="detail.html" class="text-black">{{$res_val->first_name}}</a>
                        </h6>
                        <p class="text-gray mb-3">
                           <?php echo $cat_str=implode(" • ", $res_val->access_cat);?>
                        </p>
                        <p class="text-gray mb-3 time">
                          <span class="bg-light text-dark rounded-sm pl-2 pb-1 pt-1 pr-2">
                            <i class="icofont-wall-clock"></i>{{date("i", strtotime($res_val->delivery_time))}} min </span>
                          <span class="float-right text-black-50">₹{{$res_val->two_person_cost}} FOR TWO </span>
                        </p>
                      </div>
                      <div class="list-card-badge">
                        <span class="badge badge-success">OFFER</span>
                        <small>65% off | Use Coupon OSAHAN50</small>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
              
              <div class="col-md-12 text-center load-more">
                <button class="btn btn-primary" type="button" disabled>
                  <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Loading... </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
@endsection

<script type="text/javascript">
  function delete_fav_plant(id){
    
            $.ajax({
            url: $("#front_path").val()+"/remove_fav_res" ,
            method:"POST",
            data: {"_token": "{{ csrf_token() }}",'id':id},
            success: function(data) {
                window.location.reload();
            }
        });

    }
</script>