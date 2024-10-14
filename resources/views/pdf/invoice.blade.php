<?php

error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{__('messages.Invoice')}}</title>

<style type="text/css">
    * {
        font-family: Verdana, Arial, sans-serif;
    }
    table{
        font-size: x-small;
    }
    tfoot tr td{
        font-weight: bold;
        font-size: x-small;
    }

    .gray {
        background-color: lightgray
    }
</style>

</head>
<body>
  <table width="100%">
    <tr>
        <td valign="top"><img src="{{asset('public/Ecommerce/images/').'/'.$order->setting->logo}}" alt="" width="150"/></td>
        <td align="right">
            <h3>{{__('messages.site_name')}}</h3>
            <pre>
                {{$order->setting->company_name}}
                {{$order->setting->email}}
                {{$order->setting->phone}}
            </pre>
        </td>
    </tr>

  </table>

  <table width="100%">
    <tr>
        <td><strong>{{__("messages.from")}}:</strong>
<pre>
{{$order->user->first_name}}
@if(!empty($useraddress))
{{$order->useraddress->billing_address}}
{{$order->useraddress->billing_city}}
{{$order->useraddress->billing_pincode}}
@endif
</pre>
        </td>
        <td><strong>{{__("messages.to")}}:</strong>
<pre>
{{$order->seller_info->brand_name}}
{{$order->seller_info->email}}
{{$order->seller_info->address}}
</pre>
        </td>
    </tr>

  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
      <tr>
        <th>#</th>
        <th>{{__("messages.Product Name")}}</th>
        <th>{{__("messages.qty")}}</th>
        <th>{{__("messages.unit_price")}}</th>
        <th>{{__("messages.total")}}</th>
      </tr>
    </thead>
    <tbody>
         <?php $sub_amt=0; $i=1;?>
      @foreach($item_data as $value)  

      <tr>
        <th scope="row">1</th>
        <td>{{$value->productdata->name}}</td>
        <td align="right">{{$value->quantity}}</td>
        <td align="right">{{$order->currency}}{{number_format($value->price,2,'.','')}}</td>
        <td align="right"><?php $amt=$value->quantity*$value->price; echo number_format($amt,2,'.','')?></td>
      </tr>
      <?php $sub_amt +=$amt;  $i++ ?>
     @endforeach
    </tbody>

    <tfoot>
         <tr>
            <td colspan="4" class="bg-light-2 text-right"><strong>{{__("messages.subtotal")}} : </strong></td>
            <td class="bg-light-2 text-right"><?= number_format($sub_amt,2,'.','')?></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td align="right">{{__("messages.taxes")}} </td>
            <td align="right"> {{$order->currency}}{{number_format($order->tax,2,'.','')}}</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td align="right">{{__("messages.Delivery")}} </td>
            <td align="right"> {{$order->currency}}{{number_format($order->delivery_charge,2,'.','')}}</td>
        </tr>
       
        <tr>
            <td colspan="3"></td>
            <td align="right">{{__("messages.total")}} </td>
            <td align="right" class="gray">{{$order->currency}} </td>
        </tr>
    </tfoot>
  </table>

</body>
</html>