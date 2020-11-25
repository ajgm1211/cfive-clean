<!-- Freight charges all in -->

<div>

    <p class="title" style="color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}}</b></p>
    <br>

</div>

<table border="0" cellspacing="1" cellpadding="1" >

    <thead class="title-quote text-center header-table">

        <tr >

            <th class="unit"><b>{{__('pdf.pol')}}</b></th>

            <th class="unit"><b>{{__('pdf.pod')}}</b></th>
                       
            <th class="unit" {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>

            <th ><b>TON/M3</b></th>
                       
            @if($service)
                <th class="unit"><b>{{__('pdf.tt')}}</b></th>
                <th class="unit"><b>{{__('pdf.via')}}</b></th>
            @endif
                   
        </tr>
               
    </thead>
               
    <tbody>
        @foreach($freight_charges as$rate)
            <?php
                $total_freight = 0;
                $total_freight_units = 0;
                $total_freight_rates = 0;
                $total_freight_markups = 0; 
                foreach($rate->charge_lcl_air as $value){
                    if($value->type_id==3){
                        $total_freight+=$value->total_freight;
                    }
                }
            ?>
            <tr class="text-center color-table">
                <td >{{@$rate->origin_port->name.', '.@$rate->origin_port->code}}</td>
                <td >{{@$rate->destination_port->name.', '.@$rate->destination_port->code}}</td>
                <td {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                <td >{{@$total_freight.' '.@$rate->currency->alphacode}}</td>
                @if($service)
                    <td>{{$rate->transit_time!='' ? $rate->transit_time:'-'}}</td>
                    <td>{{$rate->via!='' ? $rate->via:'-'}}</td>
                @endif
            </tr>
        @endforeach
    </tbody>           
</table>
