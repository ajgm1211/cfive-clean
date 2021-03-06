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
                                <th ><b>Total</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($totals as $total)
                                <tr class="text-left color-table">
                                    <td >{{$total['POL']}}</td>
                                    <td >{{$total['POD']}}</td>
                                    <td >{{ isDecimal(@$total['total'], false, true) }}&nbsp;{{@$total['currency']}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>