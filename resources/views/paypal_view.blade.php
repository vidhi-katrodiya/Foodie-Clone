<!DOCTYPE html>
<!--<html>
<head>
	<title>Stripe Payment</title>
</head>
<body>
	<div id="pay_3_div">
         
      </div>
</body>
</html>-->

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Payment Success</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
 
</head>
<body>



<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header" style="border-bottom: 0px;padding-bottom: 0px;">
        <h4 style="width: 100%;    text-align: center;">Process To Your Payment</h4>
        
      </div>

      <!-- Modal body -->
      <div class="modal-body" style="text-align: center;padding-top: 0px;">
       
           
            <p>Order Id: {{isset($data->order_id)?$data->order_id:''}}</p>
            
            <a href="{{url('payWithpaypal?dec_id='.$dec_id)}}" class="btn btn-primary">Process</a>
         
      </div>

      <!-- Modal footer -->
     

    </div>
  </div>
</div>
 <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
   $('#myModal').modal({
			    backdrop: 'static',
			    keyboard: false
			});
</script>
</body>
</html>
