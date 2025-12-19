<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spice Monk Tax Invoice</title>
    <style>
        body {
            font-family: Arial, serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .invoice-container {
            /*width: 900px;*/
            margin: 5px auto;
            /* padding: 10px; */
            /* border: 1px solid #bbb; */
            background: #fff;
            position: relative;
            padding-bottom: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
        }
        .header-left {
            width: 60%;
        }
        .header-right {
            width: 100%;
            text-align: right;
            position: absolute;
            right: 0;
        }
        .header-center {
            width: 100%;
            text-align: center;
        }
        .company-title {
            font-size: 10px;
            font-weight: bold;
            text-decoration: underline;
            text-underline-offset: 6px;
            margin-bottom: 4px;
        }
        .company-name {
            font-size: 12px;
            font-weight: bold;
        }
        .company-info {
            margin-bottom: 3px;
        }
        .qr-placeholder {
            width: 90px;
            height: 90px;
            background: #eee;
            display: inline-block;
            border: 1px solid #bbb;
            text-align: center;
            line-height: 90px;
            color: #bbb;
            font-size: 12px;
            float:right;
        }
        .section {
            margin-top: 0;
        }
        .details-table, .products-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #222;
            /*border-top: 0;*/
        }
        .details-table td {
            padding: 2px 5px;
            /* border: 1px solid #444; */
            vertical-align: top;
        }
        .details-table th {
            border: 1px solid #444;
            border-bottom: 2px solid #222;
            /* background: #f7faff; */
            font-weight: bold;
            text-align: left;
            padding: 7px 7px;
        }   
        .products-table th, .products-table td {
            border: 1px solid #444;
            padding: 2px 6px;
            text-align: center;
            border-bottom: 0;
            border-top: 0;
            font-size: 8px;
        }
        .products-table th {
            border-bottom: 1px solid #444;
            background: #fff;
            font-weight: bold;
        }
        .products-table td.desc {
            text-align: left;
        }
       .terms {
            margin-top: 1px;
            font-size: 7;
            padding: 1px;
        }
        .signature {
            margin-top: 3px;
            text-align: right;
            padding-bottom: 3px;
        }
        .stamp {
            display: inline-block;
            border: 2px solid #115DA9;
            border-radius: 50%;
            color: #115DA9;
            font-weight: bold;
            padding: 5px 5px;
            font-size: 8px;
            margin-right: 10px;
            vertical-align: middle;
        }
        .authorised-sign {
            display: inline-block;
            vertical-align: middle;
            font-size: 8px;
        }
        .details-table td.billing-address, .details-table td.shipping-address {
            /* background: #f7faff; */
            /*min-height: 100px;*/
            /*height: 100px;*/
            border-right: 1px solid #444;
        }
        .terms ol {
            padding: 0 20px;
        }
        .terms ol li {
            padding-bottom: 1px;
            font-size: 7px;
        }
        .terms-signature-row {
            justify-content: space-between;
            border: 1px solid;
            border-top: 0;
        }
        .border-top {
            border-top: 1px solid;
        }
       .left {
    /*width: 60%;*/
        width: 100%;
    border-right: 1px solid;
}
    .left p {
        padding-left: 5px;
    }
    .right {
      width: 25%;
      margin: auto;
      margin-right:0;
    }
    .right table {
      width: 100%;
      border-collapse: collapse;
    }
    .right td, .right th {
        padding: 2px 3px;
    }
    .right td:last-child {
      text-align: right;
    }
    .right th:first-child {
      text-align: left;
    }
    .right th:last-child {
      text-align: right;
    }
    .tax-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5px;
    }
    .tax-table th,
    .tax-table td {
      border: 1px solid #000;
      padding: 2px 3px;
      text-align: right;
      font-size: 8px;

    }
    .tax-table th:first-child,
    .tax-table td:first-child {
      text-align: left;
          border-left: 0;
    }
    .tax-table th:last-child,
    .tax-table td:last-child {
        border-right: 0;
    }
   .section.bill-details {
    display: flex;
    justify-content: space-between;
    border: 1px solid #333;
    border-top: 0;
}
    .right th {
        padding-top: 5px;
    }
    
    .bill_total{
        text-align:left;
    }
    </style>
</head>
<body>

<div class="" style="display:flex;">
<div class="invoice-container  p-0 " style="margin: 25px;">
    <div class="header m-0 p-0">
        <div class="header-center m-0 p-0">
            <div class="company-title">TAX INVOICE</div>
            <div class="company-info company-name">Spice Monk</div>
            <div class="company-info">139, Santosh Nagar, NS Road Jaipur-302019</div>
            <div class="company-info">Jaipur, Rajasthan, India</div>
            <div class="company-info"><strong>Phone No: 9928892404 | Email: spicemonk@outlook.com</strong></div>
            <div class="company-info"><strong>GSTIN: 08DVSP5354E1ZS</strong></div>
            <div class="company-info">FSSAI License No.: 12222026001982</div>
        </div>
    </div>
    <div class="section">
        <table class="details-table">
            <tr style="border-top:2px solid #222; border-bottom:2px solid #222;">
                <td style="font-weight:bold; text-align:left; width:18%; border-right:none;">Invoice No.</td>
                <td style="text-align:left; width:32%;"><span style="font-weight:bold;">:</span>{{isset($orderList->id)?$orderList->order_id:'N/A'}}</td>
                <td style="font-weight:bold; text-align:left; width:18%; border-right:none;">Invoice Date</td>
                <td style="text-align:left; width:32%;"><span style="font-weight:bold;">:</span> {{isset($orderList->date)?$orderList->date:'N/A'}}</td>
            </tr>
            <tr>
                <th colspan="2">Customer Name & Billing Address</th>
                <th colspan="2">Shipping Address</th>
            </tr>
            <tr>
                <td colspan="2" class="billing-address" style="vertical-align:top; padding:0;">
                  <div style="display:flex; flex-direction:column; justify-content:space-between; height:125px; padding:7px 7px;">
                    <div>
                      <strong>{{isset($orderList->seller->name)?$orderList->seller->name:'N/A'}}</strong><br>
                      {{isset($orderList->seller->address)?$orderList->seller->address:'N/A'}}
                    </div>
                    <div style="margin-top:auto;">
                      Phone : {{isset($orderList->seller->mobile)?$orderList->seller->mobile:'N/A'}}
                    </div>
                  </div>
                </td>
                <td colspan="2" class="shipping-address" style="vertical-align:top; padding:0;">
                  <div style="display:flex; flex-direction:column; justify-content:space-between; height:125px; padding:7px 7px;">
                    <div>
                      <strong>{{isset($orderList->seller->name)?$orderList->seller->name:'N/A'}}</strong><br>
                      {{isset($orderList->seller->address)?$orderList->seller->address:'N/A'}}
                    </div>
                    <div style="margin-top:auto;">
                      Phone : {{isset($orderList->seller->mobile)?$orderList->seller->mobile:'N/A'}}
                    </div>
                  </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="section">
        <table class="products-table">
            <tr>
                <th>S No</th>
                <th>Description</th>
                <th>HSN / SAC</th>
                <th>UOM</th>
                <th>Qty.</th>
                <th>Rate</th>
                <th>Tax</th>
                <th>Amount</th>
            </tr>
            
              @php
                    $subTotal =0;
                    $gstTax =0;
                    $cgst = 0;
                    $sgst = 0;
                     $textamt = 0.25;
                    
                    @endphp
                    @if(sizeof($orderList->orderDetails))
                    @foreach($orderList->orderDetails as $orderdetail)
                    @php
                    $subTotal +=$orderdetail->productDetail->retailer_price*$orderdetail->qty;
                    
                   $gstTax = $subTotal * ($orderdetail->productDetail->gst / 100);
                     $cgst = $subTotal * ( ($orderdetail->productDetail->gst / 100) / 2 );
                     $sgst = $subTotal * ( ($orderdetail->productDetail->gst / 100) / 2 );
                    
                    @endphp
                       <tr>
                           <td>1</td>
                           <td class="desc">{{isset($orderdetail->product->name)?$orderdetail->product->name:''}}</td>
                       <td>{{isset($orderdetail->product->hsn_code)?$orderdetail->product->hsn_code:''}}</td>
                       <td>PAC</td>
                        <td>{{isset($orderdetail->qty)?$orderdetail->qty:''}}</td>
                       <td>{{config('constants.INDIAN_RUPEE_SYMBOL').' '.$orderdetail->productDetail->retailer_price}}</td>
                      
                       <td>5%</td>
                       <td>   {{config('constants.INDIAN_RUPEE_SYMBOL').' '.$orderdetail->productDetail->retailer_price*$orderdetail->qty}}
                       </td>
                       </tr>

                    @endforeach
           @endif
            
         
        </table>
    </div>
    
           @php
        function numberToWords($num) {
            $ones = [
                "", "one", "two", "three", "four", "five", "six", "seven",
                "eight", "nine", "ten", "eleven", "twelve", "thirteen",
                "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"
            ];
        
            $tens = [
                "", "", "twenty", "thirty", "forty", "fifty",
                "sixty", "seventy", "eighty", "ninety"
            ];
        
            if ($num < 20) return $ones[$num];
            if ($num < 100) return $tens[intval($num / 10)] . ($num % 10 ? " " . $ones[$num % 10] : "");
            if ($num < 1000) return $ones[intval($num / 100)] . " hundred" . ($num % 100 ? " " . numberToWords($num % 100) : "");
            if ($num < 1000000) return numberToWords(intval($num / 1000)) . " thousand" . ($num % 1000 ? " " . numberToWords($num % 1000) : "");
        
            return $num; // fallback
        }
        
        $amount = round($orderList->total_price ?? 0);
        $amountInWords = ucfirst(numberToWords($amount));
        @endphp

    
    <div class="section bill-details">
       <div class="left">
          <p class="mb-0"><strong>Bill Amount : {{$amountInWords}}.  </strong></p>
          <p class="mb-0"><strong>Narration :</strong> Being Goods Sold To {{$orderList->seller->shop_name}}</p>
    
          <table class="tax-table">
            <tr>
              <th colspan="2">Tax Rate</th>
              <th >Taxable</th>
              <th>CGST</th>
              <th>SGST</th>
              <th colspan="2">Total Tax</th>
            </tr>
            <tr>
              <td colspan="2">TAX@ {{$orderdetail->productDetail->gst}}%</td>
              <td>{{config('constants.INDIAN_RUPEE_SYMBOL')}} {{$subTotal}}</td>
              <td>{{config('constants.INDIAN_RUPEE_SYMBOL')}} {{$cgst}}</td>
              <td>{{config('constants.INDIAN_RUPEE_SYMBOL')}} {{$sgst}}</td>
              <td colspan="2">{{config('constants.INDIAN_RUPEE_SYMBOL')}} {{$gstTax}}</td>
            </tr>
          </table>
        </div>

        <div class="right">
          <table>
            <tr>
              <td><strong>Sub Total</strong></td>
              <td><strong>{{config('constants.INDIAN_RUPEE_SYMBOL').' '.$subTotal}}</strong></td>
            </tr>
            <tr>
              <td><strong>Taxable Amt</strong></td>
              <td><strong>{{config('constants.INDIAN_RUPEE_SYMBOL').' '.$subTotal}}</strong></td>
            </tr>
            <tr>
              <td>CGST </td>
              <td>{{config('constants.INDIAN_RUPEE_SYMBOL')}} {{$cgst}}</td>
            </tr>
            <tr>
              <td>SGST/UTGST </td>
              <td>{{config('constants.INDIAN_RUPEE_SYMBOL')}} {{$sgst}}</td>
            </tr>
            <tr>
              <td>Round Off</td>
              <td>{{config('constants.INDIAN_RUPEE_SYMBOL')}}  {{round($orderList->total_price)}}</td>
            </tr>
             <tr>
              <td>Total Discount</td>
              <td>- {{config('constants.INDIAN_RUPEE_SYMBOL').' '.$orderList->discount}}</td>
            </tr>
            <tr>
              <td><strong>Bill Total</strong></td>
              <td><strong>{{config('constants.INDIAN_RUPEE_SYMBOL')}} {{round($orderList->total_price)}} </strong></td>
            </tr>
          </table>
        </div>
    </div>
    <div class="terms-signature-row" style="display: flex;">
      <div class="terms border-none" style="width: 100%;">
        <strong>Terms and Conditions:</strong>
        <ol style="margin-top: 6px;">
            <li>Goods once sold will not be taken back.</li>
            <li>Interest @18% p.a. will be charged if the payment is not made within the stipulated time.</li>
            <li>Please make cheques in favor of Spice Monk.</li>
            <li>Subject to Jaipur, Rajasthan Jurisdiction only.</li>
            <li>Cheque bounce charges rupees 500/- per case will be applicable.</li>
        </ol>
      </div>
      <div style="width:1px; background:#444; margin: 0 0;"></div>
      <div class="signature" style="flex: 0 0 200px; display: flex; flex-direction: column; justify-content: space-between; align-items: center; height: 30px;">
        <div style="font-weight: bold;">For Spice Monk</div>
        <div class="authorised-sign" style="margin-top: auto;">Authorised Signatory</div>
      </div>
    </div>
</div>



</div>

</body>
</html> 