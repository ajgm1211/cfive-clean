<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Example 1</title>
  <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
</head>
<body>
  <header class="clearfix">
    <div id="logo">
      <img src="{{asset('example1/logo.png')}}">
      <div id="company" class="clearfix">
        <div>
          <span class="color-title"><b>@if($quote->pdf_option->language=='English')Quotation Id:@elseif($quote->pdf_option->language=='Spanish') Cotización: @else Numero de cotação: @endif</b></span> 
          <span style="color: #20A7EE"><b>#{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</b></span>
        </div>
        <div>
          <span class="color-title"><b>@if($quote->pdf_option->language=='English')Date of issue:@elseif($quote->pdf_option->language=='Spanish') Fecha creación: @else Data de emissão: @endif</b></span> {{date_format($quote->created_at, 'M d, Y H:i')}}
        </div>
        @if($quote->validity_start!=''&&$quote->validity_end!='')
        <div>
          <span class="color-title">
            <b>@if($quote->pdf_option->language=='English')Validity:@elseif($quote->pdf_option->language=='Spanish') Validez: @else Validade: @endif </b>
          </span> 
          {{\Carbon\Carbon::parse( $quote->validity_start)->format('d M Y') }} -  {{\Carbon\Carbon::parse( $quote->validity_end)->format('d M Y') }}
        </div>
        @endif
      </div>
      </div>
    </div>
    <br>
    <div id="company" class="clearfix">
      @if($quote->pdf_option->language=='English')
        <div><b>To:</b></div>
      @elseif($quote->pdf_option->language=='Spanish')
        <div ><b>Para:</b></div>
      @else
        <div ><b>Para:</b></div>
      @endif
      <div>{{$quote->user->name}} {{$quote->user->lastname}}</div>
      <div><b>{{$quote->company->business_name}}</b></div>
      <div>{{$quote->company->address}}</div>
      <div>{{@$quote->contact->phone}}</div>
      <div>{{@$quote->contact->email}}</div>
    </div>
    <div id="project">
      @if($quote->pdf_option->language=='English')
        <div><b>From:</b></div>
      @elseif($quote->pdf_option->language=='Spanish')
        <div ><b>De:</b></div>
      @else
        <div ><b>A partir de:</b></div>
      @endif
      <div>{{$quote->user->name}} {{$quote->user->lastname}}</div>
      <div><b>{{$user->companyUser->name}}</b></div>
      <div>{{$user->companyUser->address}}</div>
      <div>{{$user->phone}}</div>
      <div>{{$quote->user->email}}</div>
    </div>
  </header>
  <main>
    <table style="width: 100%">
      <thead>
        <tr>
          <th class="service">POL</th>
          <th class="desc">POD</th>
          <th>20'</th>
          <th>40'</th>
          <th>40' HC</th>
          <th>40' NOR</th>
          <th>45'</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="service">Design</td>
          <td class="desc">Creating a recognizable design solution based on the company's existing visual identity</td>
          <td class="unit">$40.00</td>
          <td class="qty">26</td>
          <td class="total">$1,040.00</td>
        </tr>
        <tr>
          <td class="service">Development</td>
          <td class="desc">Developing a Content Management System-based Website</td>
          <td class="unit">$40.00</td>
          <td class="qty">80</td>
          <td class="total">$3,200.00</td>
        </tr>
        <tr>
          <td class="service">SEO</td>
          <td class="desc">Optimize the site for search engines (SEO)</td>
          <td class="unit">$40.00</td>
          <td class="qty">20</td>
          <td class="total">$800.00</td>
        </tr>
        <tr>
          <td class="service">Training</td>
          <td class="desc">Initial training sessions for staff responsible for uploading web content</td>
          <td class="unit">$40.00</td>
          <td class="qty">4</td>
          <td class="total">$160.00</td>
        </tr>
        <tr>
          <td colspan="4">SUBTOTAL</td>
          <td class="total">$5,200.00</td>
        </tr>
        <tr>
          <td colspan="4">TAX 25%</td>
          <td class="total">$1,300.00</td>
        </tr>
        <tr>
          <td colspan="4" class="grand total">GRAND TOTAL</td>
          <td class="grand total">$6,500.00</td>
        </tr>
      </tbody>
    </table>
    <div id="notices">
      <div>NOTICE:</div>
      <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
    </div>
  </main>
  <footer>
    Invoice was created on a computer and is valid without the signature and seal.
  </footer>
</body>
</html>