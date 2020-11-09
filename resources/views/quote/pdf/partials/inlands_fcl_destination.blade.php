            <!-- Inland -->    

            @foreach($inlands as $type => $inland)
                @foreach($inland as $port => $item)
                    @if($type == 'Destination')
                        <!-- Section Title -->
                        <div>
                            
                            <p class="title" style="color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.inland')}} - {{$type}} | {{$port}}</b></p>
                            
                            

                        </div>
                        <!-- End Section Title -->

                        <!-- Table -->
                        <table border="0" cellspacing="1" cellpadding="1">

                            <!-- Table Header -->
                            <thead class="title-quote text-left header-table" >

                                <tr>

                                    <th class="unit" colspan="2"><b>{{__('pdf.charge')}}</b></th>
                                    
                                    @foreach ($equipmentHides as $key=>$hide)
                                        @foreach ($containers as $c)
                                            @if($c->code == $key)

                                                <th class="unit" {{$hide}}><b>{{$key}}</b></th>

                                            @endif
                                        @endforeach
                                    @endforeach   

                                    <!--<th class="unit"><b>{{__('pdf.currency')}}</b></th>-->

                                </tr>

                            </thead>
                            <!-- End Table Header -->

                            <!-- Table Body -->
                            <tbody>
                                
                                @foreach($item as $rate)

                                    <?php       
                                        foreach ($containers as $c){
                                            ${'total_sum_'.$c->code} = 'total_sum_'.$c->code;
                                        }
                                    ?>
                                
                                    <tr class="text-left color-table">
                                
                                        <td colspan="2">{{@$rate->charge ?? '--'}}</td>
                                
                                            @foreach ($equipmentHides as $key=>$hide)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                
                                                        <td {{ $hide }}>{{ @$rate->${'total_sum_'.$c->code} }} {{@$rate->currency->alphacode}}</td>
                                
                                                    @endif
                                                @endforeach
                                            @endforeach
                                
                                
                                        <!--<td >{{$rate->currency->alphacode}}</td>-->
                                
                                    </tr>
                                @endforeach

                                <tr>


                                </tr>

                            </tbody>
                            <!-- End Table Body -->

                        </table>
                        <!-- End Table -->
                    @endif
                @endforeach
            @endforeach
            <br>