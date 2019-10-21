@if($quote->payment_conditions!='')
<div class="clearfix">
    <table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
        <thead class="title-quote header-table">
            <tr>
                <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Payments conditions</b></th>
                <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Condiciones de pago</b></th>
                <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Condições de pagamento</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding:20px;">
                    <span class="text-justify">{!! $quote->payment_conditions!!}</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endif
<br>