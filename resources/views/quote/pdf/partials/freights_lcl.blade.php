@if(@$quote->pdf_options['selectPDF']['id'] ==2 || @$quote->pdf_options['selectPDF']['id'] ==3 )
<!-- Freight charges all in -->

<div>

    <p class="title" style="margin-bottom: 0px; color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}}</b></p>
    

</div>

<table border="0" cellspacing="1" cellpadding="1" >

    <thead class="title-quote text-center header-table">

        <tr >

            <!--<th class="unit" style="{{isset($freight_charges->hasOriginAddress) && $freight_charges->hasOriginAddress == 1 ? '':'display:none;'}}">
                <b>{{__('pdf.origin')}}</b>
            </th>-->
            <th class="unit"><b>{{__('pdf.pol')}}</b></th>
            <th class="unit"><b>{{__('pdf.pod')}}</b></th>
            <!--<th class="unit" style="{{isset($freight_charges->hasDestinationAddress) && $freight_charges->hasDestinationAddress == 1 ? '':'display:none;'}}">
                <b>{{__('pdf.destination')}}</b>
            </th>-->
                       
            <th class="unit" {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>

            <th ><b>Total</b></th>
                       
            @if($service)
                <th class="unit"><b>{{__('pdf.tt')}}</b></th>
                <th class="unit"><b>{{__('pdf.via')}}</b></th>
            @endif
                   
        </tr>
               
    </thead>
               
    <tbody>
        @foreach($freight_charges as $rate)
            <?php
                $total_freight = 0;
                $total_freight_units = 0;
                $total_freight_rates = 0;
                $total_freight_markups = 0;
                if(!is_array($rate->total)){
                    $total = json_decode($rate->total);
                }else{
                    $total = $rate->total;
                }
            ?>
            <tr class="text-center color-table">
                <!--<td style="{{isset($freight_charges->hasOriginAddress) && $freight_charges->hasOriginAddress == 1 ? '':'display:none;'}}">
                    {{$rate->origin_address ?? "--" }}
                </td>-->
                <td >{{@$rate->origin_port->name.', '.@$rate->origin_port->code}}</td>
                <td >{{@$rate->destination_port->name.', '.@$rate->destination_port->code}}</td>
                <!--<td style="{{isset($freight_charges->hasDestinationAddress) && $freight_charges->hasDestinationAddress == 1 ? '':'display:none;'}}">
                    {{$rate->destination_address ?? "--" }}
                </td>-->
                <td {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                <td >{{isDecimal(@$total->total, false, true).' '.@$rate->currency->alphacode}}</td>
                @if($service)
                    <td>{{$rate->transit_time!='' ? $rate->transit_time:'-'}}</td>
                    <td>{{$rate->via!='' ? $rate->via:'-'}}</td>
                @endif
            </tr>
        @endforeach
    </tbody>           
</table>
@endif
