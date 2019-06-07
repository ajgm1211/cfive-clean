<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quote #{{$quote->quote_id}}</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
    <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
    <style>
      @font-face {
        font-family: "Sailec";
        src: url(public/fonts/sailec.otf);
      }
      p, span {
        font-family: "Sailec", sans-serif;
      }
    </style>
  </head>
  <body style="background-color: white; font-size: 11px;">
    <header class="clearfix">
        <div id="logo">
            @if($user->companyUser->logo!='')
            <img src="{{Storage::disk('s3_upload')->url($user->companyUser->logo)}}" class="img img-fluid" style="width: 100px; height: auto; margin-bottom:25px">
            @endif
        </div>
        <div id="company">
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
    </header>
    <main>
        <div id="details" class="clearfix details">
            <div class="client">
                <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>From:</b></p>
                <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>De:</b></p>
                <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>A partir de:</b></p>
                <span id="destination_input" style="line-height: 0.5">
                    <p>{{$quote->user->name}} {{$quote->user->lastname}}</p>
                    <p><span style="color: #4e4e4e"><b>{{$user->companyUser->name}}</b></span></p>
                    <p>{{$user->companyUser->address}}</p>
                    <p>{{$user->phone}}</p>
                    <p>{{$quote->user->email}}</p>
                </span>
            </div>
            <div class="company text-right" style="float: right; width: 350px;">
                <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>To:</b></p>
                <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Para:</b></p>
                <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Para:</b></p>
                <span id="destination_input" style="line-height: 0.5">
                    @if($quote->pdf_option->show_logo==1)
                      @if($quote->company->logo!='')
                      <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive" width="115" height="auto" style="margin-bottom:20px">
                      @endif
                    @endif
                    <p>{{@$quote->contact->first_name.' '.@$quote->contact->last_name}}</p>
                    <p><span style="color: #4e4e4e"><b>{{$quote->company->business_name}}</b></span></p>
                    <p>{{$quote->company->address}}</p>
                    <p>{{@$quote->contact->phone}}</p>
                    <p>{{@$quote->contact->email}}</p>
                </span>
            </div>
        </div>
        <br>
        @if($quote->kind_of_cargo!='')
            <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}><span class="title" >Kind of cargo:</span> {{$quote->kind_of_cargo}}</p>
            <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}}</p>
            <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}}</p>
        @endif
        @if($quote->commodity!='')
            <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}><span class="title" >Commodity:</span> {{$quote->commodity}}</p>
            <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><span class="title" >Mercancía:</span> {{$quote->commodity}}</p>
            <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><span class="title" >Mercadoria:</span> {{$quote->commodity}}</p>
        @endif        
        <div class="company" style="color: #1D3A6E;">
            <p class="title"><b>Cargo details</b></p>
            <br>
            @if(!empty($package_loads) && count($package_loads)>0)
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>Cargo type</b></th>
                        <th class="unit"><b>Quantity</b></th>
                        <th class="unit"><b>Height</b></th>
                        <th class="unit"><b>Width</b></th>
                        <th class="unit"><b>Large</b></th>
                        <th class="unit"><b>Weight</b></th>
                        <th class="unit"><b>Total weight</b></th>
                        <th class="unit"><b>Volume</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($package_loads as $package_load)
                        <tr class="text-center">
                            <td>{{$package_load->type_cargo==1 ? 'Pallets':'Packages'}}</td>
                            <td>{{$package_load->quantity}}</td>
                            <td>{{$package_load->height}} cm</td>
                            <td>{{$package_load->width}} cm</td>
                            <td>{{$package_load->large}} cm</td>
                            <td>{{$package_load->weight}} kg</td>
                            <td>{{$package_load->total_weight}} kg</td>
                            <td>{{$package_load->volume}} m<sup>3</sup></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <br>
                <div class="row">
                    <div class="col-md-12 pull-right">
                        <b class="title">Total:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                    </div>
                </div>
                @if($quote->chargeable_weight!='' && $quote->chargeable_weight>0)
                  <div class="row">
                      <div class="col-md-12 ">
                          <b class="title">Chargeable weight:</b> {{$quote->chargeable_weight}} kg
                      </div>
                  </div>
                @endif
            @else
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                      <th class="unit"><b>Cargo type</b></th>
                      <th class="unit"><b>Total quantity</b></th>
                      <th class="unit"><b>Total weight</b></th>
                      <th class="unit"><b>Total volume</b></th>
                      <th class="unit"><b>Chargeable weight</b></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="text-center">
                      <td>{{$quote->cargo_type == 1 ? 'Pallets' : 'Packages'}}</td>
                      <td>{{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</td>
                      <td>{{$quote->total_weight != '' ? $quote->total_weight.' Kg' : ''}}</td>
                      <td>{!!$quote->total_volume != '' ? $quote->total_volume.' m<sup>3</sup>' : ''!!}</td>
                      <td>{{$quote->chargeable_weight}} kg</td>
                    </tr>
                  </tbody>
                </table>
            @endif 
        </div>
        <br>
        @if($quote->payment_conditions!='')
            <br>
            <div class="clearfix">
                <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote header-table">
                        <tr>
                            <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Payments conditions</b></th>
                            <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Condiciones de pago</b></th>
                            <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Condições de pagamento</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:20px;">
                                <span class="text-justify">{!! $quote->payment_conditions!!}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
        @if($quote->terms_and_conditions!='')
            <br>
            <div class="clearfix">
                <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote header-table">
                        <tr>
                            <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Terms and conditions</b></th>
                            <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Términos y condiciones</b></th>
                            <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Termos e Condições</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:20px;">
                                <span class="text-justify">{!! $quote->terms_and_conditions!!}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif 
</main>
</body>
</html>