@if(@$quote->pdf_options['selectPDF']['id'] ==2 || @$quote->pdf_options['selectPDF']['id'] ==3 )
<!-- Destinations detailed -->
@if($destination_charges->count()>0)
    @foreach($destination_charges as $port => $value)
        <br>
        <!-- Section Title -->
        <div>

            <p class="title" style="margin-bottom: 0px; color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.destination_charges')}} - {{$port}}</b></p>

        </div>
        <!-- End Section Title -->

        <!-- Table -->
        <table border="0" cellspacing="1" cellpadding="1">

            <!-- Table Header -->
            <thead class="title-quote text-left header-table">

                <tr>
                    <th class="unit"><b>{{__('pdf.charge')}}</b></th>

                    <th class="unit"><b>{{__('pdf.detail')}}</b></th>

                    <th class="unit"><b>{{__('pdf.units')}}</b></th>

                    <th class="unit"><b>{{__('pdf.price')}}</b></th>

                    <th class="unit"><b>{{__('pdf.total')}}</b></th>

                </tr>

            </thead>
            <!-- End Table Header -->

            <!-- Table Body -->
            <tbody>
                @foreach($value as $key => $charge)
                    <tr>
                        <td>{!! $charge->charge !!}</td>
                        <td>{{  $charge->calculation_type['name'] ?? @$charge->address ?? "--" }}</td>
                        <td>{{ ($charge->units != 0 || $charge->units != "")? isDecimal($charge->units, false, true):1 }}</td>

                        @if($charge->price != 0 || $charge->price != "")
                            <td>{{ isDecimal($charge->total/$charge->units, false, true) ?? "--" }}</td>
                        @elseif(isset($charge->totals))
                            @php
                                $array_total_inland = json_decode($charge->totals);
                            @endphp
                            @foreach($array_total_inland as $total)
                                <td>{!! isDecimal($total, false, true) !!}</td>
                            @endforeach
                        @else
                            <td>{!! isDecimal(@$charge->sum_total, false, true) !!}</td>
                        @endif

                        @if(isset($charge->totals))
                            @php
                                $array_total_inland = json_decode($charge->totals);
                            @endphp
                            @foreach($array_total_inland as $total)
                                <td>{!! isDecimal($total, false, true).' '.$charge->currency->alphacode !!}</td>
                            @endforeach
                        @elseif(isset($charge->sum_total))
                            <td>{!! isDecimal($charge->sum_total, false, true).' '.$charge->currency->alphacode !!}</td>
                        @else
                            @if(is_object($charge->total))
                                @foreach($charge->total as $total)
                                    <td>{!! isDecimal($total, false, true).' '.$charge->currency->alphacode !!}</td>
                                @endforeach
                            @else
                                <td>{!! isDecimal($charge->total, false, true).' '.$charge->currency->alphacode !!}</td>
                            @endif
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <!-- End Table Body -->
        </table>
        <!-- End Table -->
    @endforeach
    <br>
@endif
@endif