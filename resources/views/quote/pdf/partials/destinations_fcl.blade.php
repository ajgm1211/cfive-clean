<!-- Origins detailed -->
@if($destination_charges->count()>0)
    @foreach($destination_charges as $port => $value)
        <!-- Section Title -->
        <div>

            <p class="title" style="color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.destination_charges')}} - {{$port}}</b></p>

            <br>

        </div>
        <!-- End Section Title -->

        <!-- Table -->
        <table border="0" cellspacing="1" cellpadding="1">

            <!-- Table Header -->
            <thead class="title-quote text-left header-table">

                <tr>
                    <th class="unit"><b>{{__('pdf.charge')}}</b></th>

                    <th class="unit"><b>{{__('pdf.detail')}}</b></th>

                    @foreach ($equipmentHides as $key=>$hide)
                        @foreach ($containers as $c)
                            @if($c->code == $key)

                                <th {{ $hide }}><b>{{ $key }}</b></th>

                            @endif
                        @endforeach
                    @endforeach

                    <!--<th class="unit"><b>{{__('pdf.currency')}}</b></th>-->

                </tr>

            </thead>
            <!-- End Table Header -->

            <!-- Table Body -->
            <tbody>
                @foreach($value as $key => $charge)
                    @if(is_int($key))
                        <tr>
                            <td>{!! is_int($key) ? $charge->charge:'<b>'.__('pdf.total').'</b>' !!}</td>
                            <td>{{ $charge->calculation_type['name'] }}</td>
                            @foreach ($charge->total as $total)
                                <td>{!! is_int($key) ? $total:'<b>'.$total.'</b>' !!} {!! is_int($key) ? $charge->currency->alphacode:'<b>'.$charge->currency->alphacode.'</b>' !!}</td>
                            @endforeach
                            <!--<td>{!! is_int($key) ? $charge->currency->alphacode:'<b>'.$charge->currency->alphacode.'</b>' !!}</td>-->
                        </tr>
                    @else
                        @php
                            $totals = json_decode($charge->totals);
                            
                            foreach ($containers as $c){
                                ${'c'.$c->code} = 'c'.$c->code;
                            }
                        @endphp
                        <tr>
                            <td>INLAND</td>
                            <td>--</td>
                            @foreach ($equipmentHides as $key=>$hide)
                                @foreach ($containers as $c)
                                    @if($c->code == $key)
                                        
                                        <td {{ $hide }}>{{ @$totals->${'c'.$c->code} .' '. @$charge->currency->alphacode }}</td>

                                    @endif
                                @endforeach
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <!-- End Table Body -->

            <tfoot>

            </tfoot>

        </table>
        <!-- End Table -->
    @endforeach
    <br>
@endif