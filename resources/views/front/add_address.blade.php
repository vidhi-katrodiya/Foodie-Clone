@extends('front.layout')

@section('title')
  {{__("messages.restuarnt detail")}}
@endsection

@section('content')

<style type="text/css">
 
.selector{
    position:relative;
    width:100%;
    background-color:var(--smoke-white);
    height:55px;
    display:flex;
    justify-content:space-around;
    align-items:center;
    border-radius:10px;
    box-shadow:0 0 16px rgba(0,0,0,.2);
}
.selecotr-item{
    position:relative;
    flex-basis:calc(80% / 3);
    height:100%;
    margin-bottom:-10px;
    display:flex;
    justify-content:center;
    align-items:center;
}
.selector-item_radio{
    appearance:none;
    display:none;
}
.selector-item_label{
    position:relative;
    height:64%;
    width:100%;
    text-align:center;
    border-radius:10px;
    line-height:229%;
    font-weight:600;
    transition-duration:.5s;
    transition-property:transform, color, box-shadow;
    transform:none;
}
.selector-item_radio:checked + .selector-item_label{
  font-size: 16px;
    background-color:#db5359;
    color:var(--white);
    box-shadow:0 0 4px rgba(0,0,0,.5),0 2px 4px rgba(0,0,0,.5);
    transform:translateY(-2px);
}
@media (max-width:480px) {
  .selector{
    width: 90%;
  }
}

</style>
<div class="departmentpg-main-box" style="background-color: #f3f7f8;">
  <div class="container"> 
    <div class="global-heading">
      <div class="departmentpg-main-box" style="background-color: #f3f7f8;  padding-top:50px; padding-bottom:50px;">
        <div class="container"> 
          
          <div class="row" style="background-color: #fff; padding: 10px; padding-top:25px;">
            <div class="col-lg-6 col-md-6 col-sm-6">        
                <div id="map" style=" height: 565px; width: 100%;"></div>
                <div id="infowindow-content">
                  <span id="place-name" class="title"></span><br />
                  <span id="place-address"></span>
                </div>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h4 class="panel-title">Add Your Address</h4>
                </div>
                <div class="panel-body">
                  
                  <input id="address" placeholder="Add Your Address" type="text" class="location-input form-control">
                  <span id="reg_name_error" class="dangerrequired"></span>
                  <br>
                  <div id="address">
                    <div class="row">
                      <div class="col-md-6">
                        <label class="control-label">Floor / Tower</label>
                        <input class="location-input form-control" name="floor" id="floor"   placeholder="Enter your  floor / Tower">
                      </div>
                      <div class="col-md-6">
                        <label class="control-label">Mobile No.</label>
                        <input class="form-control" name="mobile_no" id="mobile_no" value="{{$user->phone}}" >
                      </div>
                    </div><br>
                      
                    <div class="row">
                      <div class="col-md-6">
                        <label class="control-label">Area</label>
                        <input type="" class="form-control" name="area" id="area" disabled="true" placeholder="Enter your area">
                      </div>
                      <div class="col-md-6">
                        <label class="control-label">City</label>
                        <input type="" class="form-control field" name="city" id="city" disabled="true" placeholder="Enter your City">
                      </div>
                      
                    </div><br>
                     <div class="row">
                      <div class="col-md-6">
                        <!--<label class="control-label">Lattitude</label>-->
                        <input type="hidden" class="location-input form-control" name="lat" id="lat" disabled="true" placeholder="Enter your Lattitude">
                      </div>
                      <div class="col-md-6">
                        <!--<label class="control-label">Longitude</label>-->
                        <input type="hidden" class="form-control" name="lng" id="lng" disabled="true" placeholder="Enter your Longitude">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6"> 
                        <label class="control-label">State</label>
                        <input type="" class="form-control" name="state" id="state"  disabled="true" placeholder="Enter your State">
                      </div>
                      <div class="col-md-6">
                        <label class="control-label">country</label>
                        <input type="" class="form-control" name="country" id="country" disabled="true" placeholder="Enter your country">
                      </div>
                      <div class="col-md-6">
                        <!--<label class="control-label">User Id</label>-->
                        <input type="hidden" class="form-control" disabled="true" name="user_id" id="user_id" value="{{Auth::user()?Auth::user()->id:''}}">
                      </div>
                    </div><br>
                    <div class="row">
                      <div class="col-md-6">
                        <label class="control-label">Zip code</label>
                        <input type="" class="form-control" name="pincode" id="pincode" disabled="true" placeholder="Enter your Zip code">
                      </div>
                    </div>
                    <br><label class="control-label">Address Type</label>
                    <div class="selector">
                        
                        <div class="selecotr-item">
                            <input type="radio" id="radio1" name="add_name" class="selector-item_radio" value="Home" checked >
                            <label for="radio1" class="selector-item_label">Home</label>
                        </div>
                        <div class="selecotr-item">
                            <input type="radio" id="radio2" name="add_name" class="selector-item_radio" value="Office">
                            <label for="radio2" class="selector-item_label">Office</label>
                        </div>
                        <div class="selecotr-item">
                            <input type="radio" id="radio3" name="add_name" class="selector-item_radio" value="Other">
                            <label for="radio3" class="selector-item_label">Other</label>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12"><br>
                      <button class="btn btn-lg btn-outline-primary btn-block btn-login text-uppercase font-weight-bold mb-2" onclick="add_address_data()" type="submit">ADD ADDRESS</button>
                       
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

@endsection

@section('script')
<script type="text/javascript">
    function add_address_data(){
      var address=$('#address').val();
      var floor=$('#floor').val();
      var pincode=$('#pincode').val();
      var city=$('#city').val();
      var state=$('#state').val();
      var lat=$('#lat').val();
      var lng=$('#lng').val();
      var user_id=$('#user_id').val();
      var mobile_no=$('#mobile_no').val();
      var area=$('#area').val();
      var country=$('#country').val();
      var add_name=$('input[name="add_name"]:checked').val();
    
      $.ajax({
             url: $("#front_path").val()+"/add_address_data",
             data: { 
                      'address': address,
                      'floor':floor,
                      'pincode':pincode,
                      'city':city,
                      'state':state,
                      'lat':lat,
                      'lng':lng,
                      'user_id':user_id,
                      'add_name':add_name,
                      'mobile_no':mobile_no,
                      'area':area,
                      'country':country,
                    },
             success: function(data)
             {
                 window.location.replace($("#front_path").val()+"/checkout");
             }
        });
    }
</script>
@endsection 