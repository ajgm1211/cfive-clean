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
                        <td>{!! $charge->charge ?? 'Inland' !!}</td>
                        <td>{{  $charge->calculation_type['name'] ?? @$charge->inland_address->address ?? "--" }}</td>
                        <td>{{ $charge->units ?? "--" }}</td>
                        <td>{{ $charge->price ?? "--" }}</td>
                        @if(isset($charge->totals))
                            @php
                                $array_total_inland = json_decode($charge->totals);
                            @endphp
                            @foreach($array_total_inland as $total)
                                <td>{!! isDecimal($total, false, true).' '.$charge->currency->alphacode !!}</td>
                            @endforeach
                        @else
                            <td>{!! isDecimal($charge->total, false, true).' '.$charge->currency->alphacode !!}</td>
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