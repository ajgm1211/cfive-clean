<!-- Origins detailed -->
@if($origin_charges->count()>0)
    @foreach($origin_charges as $port => $value)
        <br>
        <!-- Section Title -->
        <div>

            <p class="title" style="color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.origin_charges')}} - {{$port}}</b></p>

            

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
                    <tr>
                        <td>{!! $charge->charge ?? 'Inland' !!}</td>
                        <td>{{  @$charge->calculation_type['name'] ?? @$charge->inland_address->address ?? "--" }}</td>
                        @foreach ($charge->total as $total)
                            <td>{!!  $total !!} {!! @$charge->currency->alphacode !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <!-- End Table Body -->
        </table>
        <!-- End Table -->
    @endforeach
    <br>
@endif