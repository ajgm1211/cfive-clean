<?php
    $i=0;
?>
@foreach($rates as $rate)
    @if(($rate->remarks != '' && $rate->remarks!='<br>') || ($rate->remarks_english!= '' && $rate->remarks_english!='<br>') || ($rate->remarks_portuguese!= '' && $rate->remarks_portuguese!='<br>') || ($rate->remarks_spanish!= '' && $rate->remarks_spanish!='<br>'))
        <?php
            $i++;
        ?>
    @endif
@endforeach

@if($i>0)    
    <br>
    <div class="clearfix">
        @switch($quote->pdf_option->language)
            @case("English")
                <span class="color-title text-left"><b>Remarks</b><br><br/></span>
                @break
            @case("Portuguese")
                <span class="color-title text-left"><b>Observações</b><br><br/></span><br/>
                @break
            @case("Spanish")
                <span class="color-title text-left"><b>Observaciones</b><br><br/></span><br/>
                @break
            @default
                <span class="color-title text-left"><b>Remarks</b><br><br/></span><br/>
                @break
        @endswitch

        @foreach($rates as $rate)
            @if($rate->remarks != '')
                <span class="text-justify">{!! $rate->remarks !!}</span><br/>
            @endif
            @switch($quote->pdf_option->language)
                @case("English")
                    <span class="text-justify">{!! $rate->remarks_english !!}</span>
                    @break
                @case("Portuguese")
                    <span class="text-justify">{!! $rate->remarks_portuguese !!}</span>
                    @break
                @case("Spanish")
                    <span class="text-justify">{!! $rate->remarks_spanish !!}</span>
                    @break
            @endswitch
        @endforeach
        <!--<table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
            <thead class="title-quote header-table">
                <tr>
                    <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Remarks</b></th>
                    <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Observaciones</b></th>
                    <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Observações</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:15px;">
                        @foreach($rates as $rate)
                            @if($rate->remarks != '')
                                <span class="text-justify">{!! $rate->remarks !!}</span>
                            @endif
                        @endforeach
                    </td>
                </tr>                        
            </tbody>
        </table>-->
    </div>
@endif
<br>