<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payment</title>
  <style type="text/css">
          
body {
  background: #f2f4f7;
  color: #28333b;
  font-family: 'DM Sans', sans-serif;
  font-size: 1em;
  padding: 0px 25px;
}
body a {
  color: #28333b;
  text-decoration: none;
  border-bottom: 2px solid rgba(64,179,255,0.5);
  opacity: 0.75;
  transition: all 0.5s ease;
}
body a:hover {
  border-bottom: 2px solid #40b3ff;
  opacity: 1;
}
.field {
  margin-bottom: 25px;
}
.field.full {
  width: 100%;
}
.field.half {
  width: calc(50% - 12px);
}
.field label {
  display: block;
  text-transform: uppercase;
  font-size: 12px;
  margin-bottom: 8px;
}
.field input {
  padding: 12px;
  border-radius: 6px;
  border: 2px solid #e8ebed;
  display: block;
  font-size: 14px;
  width: 100%;
  box-sizing: border-box;
}
.field input:placeholder {
  color: #e8ebed !important;
}
.flex {
  display: flex;
  flex-direction: row wrap;
  align-items: center;
}
.flex.justify-space-between {
  justify-content: space-between;
}
.card {
  padding: 50px;
  margin: 50px auto;
  max-width: 850px;
  background: #fff;
  border-radius: 6px;
  box-sizing: border-box;
  box-shadow: 0px 24px 60px -1px rgba(37,44,54,0.14);
}
.card .container {
  max-width: 700px;
  margin: 0 auto;
}
.card .card-title {
  margin-bottom: 50px;
}
.card .card-title h2 {
  margin: 0;
}
.card .card-body .payment-type,
.card .card-body .payment-info {
  margin-bottom: 25px;
}
.card .card-body .payment-type h4 {
  margin: 0;
}
.card .card-body .payment-type .types {
  margin: 25px 0;
}
.card .card-body .payment-type .types .type {
  /* width: 30%; */
  position: relative;
  background: #f2f4f7;
  border: 2px solid #e8ebed;
  padding: 25px;
  box-sizing: border-box;
  border-radius: 6px;
  cursor: pointer;
  text-align: center;
  transition: all 0.5s ease;
}
.card .card-body .payment-type .types .type:hover {
  border-color: #28333b;
}
.card .card-body .payment-type .types .type:hover .logo,
.card .card-body .payment-type .types .type:hover p {
  color: #28333b;
}
.card .card-body .payment-type .types .type.selected {
  border-color: #3c763d;
    background: rgb(64 255 123 / 10%);
}
.card .card-body .payment-type .types .type.selected .logo {
  color: #40b3ff;
}
.card .card-body .payment-type .types .type.selected p {
  color: #28333b;
}
.card .card-body .payment-type .types .type.selected::after {
  content: '\f00c';
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  position: absolute;
  height: 40px;
  width: 40px;
  top: -21px;
  right: -21px;
  background: #fff;
  border: 2px solid #0c6428;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.card .card-body .payment-type .types .type .logo,
.card .card-body .payment-type .types .type p {
  transition: all 0.5s ease;
}
.card .card-body .payment-type .types .type .logo {
  font-size: 48px;
  color: #8a959c;
}
.card .card-body .payment-type .types .type p {
  margin-bottom: 0;
  font-size: 10px;
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 0.5px;
  color: #8a959c;
}
.card .card-body .payment-info .column {
  width: calc(50% - 25px);
}
.card .card-body .payment-info .title {
  display: flex;
  flex-direction: row;
  align-items: center;
}
.card .card-body .payment-info .title .num {
  height: 24px;
  width: 24px;
  border-radius: 50%;
  border: 2px solid #40b3ff;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  margin-right: 12px;
  font-size: 12px;
}
footer {
  margin: 50px auto;
  max-width: 850px;
  text-align: center;
}
.button {
  text-transform: uppercase;
  font-weight: 600;
  font-size: 12px;
  letter-spacing: 0.5px;
  padding: 15px 25px;
  border-radius: 50px;
  cursor: pointer;
  transition: all 0.5s ease;
  background: transparent;
  border: 2px solid transparent;
}
.button.button-link {
  padding: 0 0 2px;
  margin: 0 25px;
  border-bottom: 2px solid rgba(64,179,255,0.5);
  border-radius: 0;
  opacity: 0.75;
}
.button.button-link:hover {
  border-bottom: 2px solid #40b3ff;
  opacity: 1;
}
.button.button-primary {
  background: #40b3ff;
  color: #fff;
}
.button.button-primary:hover {
  background: #218fd9;
}
.button.button-secondary {
  background: transparent;
  border-color: #e8ebed;
  color: #8a959c;
}
.button.button-secondary:hover {
  border-color: #28333b;
  color: #28333b;
}

  </style>
    <meta charset="UTF-8">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=DM+Sans:400,500,700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css'><link rel="stylesheet" href="./style.css">

</head>
<body>
<article class="card">
  <div class="container">
    <div class="card-title">
      <h2>RazorPay Payment</h2>
    </div>
    <div class="card-body">
      <div class="payment-type">
       
      

        </div>
        

      </div>
      
    </div>
    <div class="card-actions flex justify-space-between">
     
      <div class="flex-end">
       
                      <div id="razorpay_pay"  class="payfrom">
                       
                         <form action="{{url('razor_payment')}}" method="GET" >
                          
                          <input id="book_appointment_id" name="id" type="hidden" value="{{$data->id}}">
                            <script src="https://checkout.razorpay.com/v1/checkout.js?v=weq"
                                    data-key="{{$paymentdetail->payment_key}}"
                                    data-amount="{{(int)$amount*100}}"
                                    data-buttontext='Pay'
                                    data-name="{{env('APP_NAME')}}"
                                    data-description="Payment"
                                    data-image="{{asset('public/image_web/896814.png')}}"
                                    data-prefill.name="name"
                                    data-prefill.email="email"
                                    data-theme.color="#d18217">
                            </script>
                            <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                        </form>
                       
                      </div>
                      
      </div>
    </div>
  
</article>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js?v=1dsqds"></script>

 <script type="text/javascript">
           $(".razorpay-payment-button").click();
        </script>
 
</body>
</html>