<!DOCTYPE html>
<html>
<head>
	<title>Paypal Payment</title>
</head>
<body>
	<div id="pay_2_div">
	    <form action="{{url('paypal')}}" method="POST">
	        {{ csrf_field() }}
	        <input type="hidden" name="dec_id" id="dec_id" required="" value="{{$dec_id}}" />
	       
	        <button type="submit">
	        <span style="">{{__('messages.place_order')}}</span>
	        </button>
	    </form>
    </div>

</body>
</html>