@if(@$quote->pdf_options['selectPDF']['id'] ==2 || @$quote->pdf_options['selectPDF']['id'] ==3 )
<!-- Origins detailed -->
@if($origin_charges->count()>0)
    @foreach($origin_charges as $port => $value)
        <br>
        <!-- Section Title -->
        <div>

            <p class="title" style="margin-bottom: 0px; color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.origin_charges')}} - {{$port}}</b></p>
            
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

                </tr>

            </thead>
            <!-- End Table Header -->

            <!-- Table Body -->
            <tbody>
                @foreach($value as $key => $charge)
                    @php $amounts = is_array($charge->total) ? $charge->total:json_decode(json_decode($charge->total)) @endphp
                    <tr>
                        <td>{!! $charge->charge !!}</td>
                        <td>{{  @$charge->calculation_type['name'] ?? @$charge->address ?? "--" }}</td>
                        @if(isset($charge->sum_total))
                            @foreach ($charge->sum_total as $total)
                                <td>{!! isDecimal($total, false, true) !!} {!! @$charge->currency->alphacode !!}</td>
                            @endforeach
                        @elseif(isset($amounts))
                            @foreach ($amounts as $total)
                                <td>{!! isDecimal($total, false, true) !!} {!! @$charge->currency->alphacode !!}</td>
                            @endforeach
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