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
        <h4 style="width: 100%;    text-align: center;">Success</h4>
      </div>

      <!-- Modal body -->
      <div class="modal-body" style="text-align: center;padding-top: 0px;">
          <img src="{{asset('public/tick.png')}}"/>
          <p style="text-align: center;">Thank you for shopping</p>
          <span>Your items has been placed and is on its way to being processed.</span>
          <div class="row" style="margin-top:10px;">
              <div class="col-md-6">
                <a href="{{route('my_account')}}"type="button" class="btn btn-primary btn-lg btn-block" style=" font-size: 18px;">View Order</a>
              </div>
              <div class="col-md-6">
                <a href="{{url('/')}}" type="button" class="btn btn-primary btn-lg btn-block" style=" font-size: 18px;">Continue Shopping</a>
              </div>
            </div>
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
