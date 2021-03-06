                    <!-- Section Title -->
                    <div>
                                    
                        <p class="title" style="margin-bottom: 0px; color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.totals')}}</b></p>

                    </div>
                    
                    <!-- End Section Title -->
                    <table border="0" cellspacing="1" cellpadding="1">
                        <thead class="title-quote text-left header-table">
                            <tr >
                                <th class="unit"><b>{{__('pdf.pol')}}</b></th>
                                <th class="unit"><b>{{__('pdf.pod')}}</b></th>
                                <th class="unit" {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                @foreach ($equipmentHides as $key=>$hide)
                                    @foreach ($containers as $c)
                                        @if($c->code == $key)
                                            <th class="unit" {{$hide}}><b>{{$key}}</b></th>
                                        @endif
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($totals as $total)
                                <tr class="text-left color-table">
                                    <td >{{$total['POL']}}</td>
                                    <td >{{$total['POD']}}</td>
                                    <td {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}>{{$total['carrier']}}</td>
                                    @foreach ($equipmentHides as $key=>$hide)
                                        @foreach ($containers as $c)
                                            @if($c->code == $key)
                                                <td {{$hide}}>{{ isDecimal(@$total['c'.$c->code], false, true) }}&nbsp;{{@$total['currency']}}</td>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>