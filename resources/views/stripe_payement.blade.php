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

<div class="container mt-3">
  <h3>Modal Example</h3>
  <p>Click on the button to open the modal.</p>
  

</div>

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
        <form action="{{url('pay_stripe')}}" method="get">
            {{ csrf_field() }}
            <p>Order Id: {{isset($data->order_id)?$data->order_id:''}}</p>
            <input type="hidden" name="dec_id" id="dec_id" required="" value="{{$dec_id}}" />
            <script
               src="https://checkout.stripe.com/checkout.js" class="stripe-button btn-primary"
              
               data-key="{{$payment->payment_key}}"
               data-amount=""
               data-id="stripid"
               data-name="Grocery E-commerce App with Website, User, Seller & driver app with Admin panel"
               data-label="Process"
               data-description=""
               data-image="https://customise.freaktemplate.com/grocery/public/home_logo.png"
               data-locale="auto"></script>
         </form>
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
