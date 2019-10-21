<?php
    $i=0;
?>
@foreach($rates as $rate)
    @if($rate->remarks != '' && $rate->remarks!='<br>')
        <?php
            $i++;
        ?>
    @endif
@endforeach

@if($i>0)    
    <br>
    <div class="clearfix">
        <table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
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
        </table>
    </div>
@endif
<br>