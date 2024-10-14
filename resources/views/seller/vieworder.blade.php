@extends('seller.index')
@section('content')
<style type="text/css">
    tbody tr {
        background: white;
    }
</style>
<div class="breadcrumbs">
    <div class="col-sm-4">
        <div class="page-header float-left">
            <div class="page-title">
                <h1>{{__('messages.view_order')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li>{{__('messages.orders')}}</li>
                    <li class="active">{{__('messages.view_order')}}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content mt-3">
    <div class="col-md-12">
        <div class="card">
             <div class="card-header">
                {{__('messages.view_order')}}
                <a href="{{url('seller/generateorderpdf').'/'.$order->id}}" target="_blank" class="btn btn-primary" style="color:white !important;float: right">{{__("messages.Download Invoice")}}</a>
            </div>
            <div class="card-body">
                <div class="container-fluid invoice-container">
                    <header>
                        <div class="row align-items-center">
                            <div class="col-sm-7 text-center text-sm-left mb-3 mb-sm-0">
                                <img id="logo" src="{{asset('public/Ecommerce/images/').'/'.$setting->logo}}" style="width:126px" title="Koice" alt="Koice" />
                            </div>
                            <div class="col-sm-5 text-center text-sm-right">
                                <h4 class="text-7 mb-0">{{__("messages.orders")}}  {{$order->id}}</h4>
                            </div>
                        </div>
                        <hr />
                    </header>
                    <main>
                        <div class="row">
                            <div class="col-sm-6"><strong>{{__("messages.date")}}:</strong> <?php 
                                $date=date_create($order->orderdate);
                                echo date_format($date,"Y/m/d H:i:s");
                            ?></div>
                            <div class="col-sm-6 text-sm-right"><strong>{{__("messages.Invoice No")}}:</strong> {{$order->orderdatals->order_no}}</div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-6 text-sm-right order-sm-1">
                                <strong>{{__("messages.Pay To")}} : </strong>
                                <address><br />
                                    @if(!empty($order->sellerinfo->address))
                                    {{$order->sellerinfo->address}}<br />
                                    @endif
                                    {{$order->sellerinfo->email}}
                                 
                                </address>
                            </div>
                            <div class="col-sm-6 order-sm-0">
                                <strong>{{__("messages.Invoiced To")}} : </strong>
                                <address>
                                    {{$user->first_name}}<br />
                                    @if(!empty($useraddress))
                                    {{$useraddress->address}}<br />
                                    {{$useraddress->city}}
                                </br>
                                  {{$useraddress->pincode}}</br>
                                   {{$useraddress->mobile_no}}
                                   @endif
                                </address>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header px-2 py-0">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <td><strong>#</strong></td>
                                            <td><strong>{{__("messages.item")}}</strong></td>
                                            <td><strong>{{__("messages.qty")}}</strong></td>
                                            <td style="text-align: right"><strong>{{__("messages.price")}}</strong></td>
                                            <td style="text-align: right"><strong>{{__("messages.total")}}</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sub_amt=0; $i=1;?>
                                        @foreach($item_data as $value)
                                        
                                        <tr>
                                         <td><?= $i;?></td>
                                                <td>{{$value->productdata->name}}</td>
                                                <td>{{$value->quantity}}</td>
                                                <td style="text-align: right">{{number_format($value->price,2,'.','')}}</td>
                                                <td style="text-align: right"><!-- {{number_format($value->quantity*$value->price,2,'.','')}} --><?php $amt=$value->quantity*$value->price; echo number_format($amt,2,'.','')?></td> 
                                         </tr>
                                                 <?php $sub_amt +=$amt;  $i++ ?>

                                        @endforeach
                                         
                                             <tr>
                                                <td colspan="4" class="bg-light-2 text-right"><strong>{{__("messages.subtotal")}} : </strong></td>
                                                <td class="bg-light-2 text-right"><?= number_format($sub_amt,2,'.','')?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="bg-light-2 text-right"><strong>{{__("messages.taxes")}}:</strong></td>
                                                <td class="bg-light-2 text-right">
                                                    {{number_format($order->tax,2,'.','')}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="bg-light-2 text-right"><strong>
                                               {{__("messages.Delivery")}}
                                               
                                                   ({{$shipping->label}})
                                                   :</strong></td>
                                                <td class="bg-light-2 text-right">{{number_format($order->delivery_charge,2,'.','')}}</td>
                                            </tr>
                                            @if($order->coupon_code!=""&&$order->coupon_code!='0')
                                            <tr>
                                                <td colspan="4" class="bg-light-2 text-right"><strong>
                                                {{__("messages.Coupon Code")}} ({{$order->coupon_code}})
                                                   :</strong></td>
                                                <td class="bg-light-2 text-right">{{number_format($order->coupon_price,2,'.','')}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td colspan="4" class="bg-light-2 text-right"><strong>{{__("messages.total")}}:</strong></td>
                                                <td class="bg-light-2 text-right">
                                                    <?php $total_amt=$sub_amt+$order->delivery_charge+$order->tax; echo number_format($total_amt,2,'.','')?>
                                                </td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                           
                        </div>
                    </main>

                    <footer class="text-center mt-4">
                        
                    </footer>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
