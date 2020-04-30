@if($quote->payment_conditions!='')
    <div class="clearfix">
        <table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
            <thead class="title-quote header-table">
                <tr>
                    <th class="unit text-left"><b>&nbsp;&nbsp;&nbsp;{{__('pdf.payment_conditions')}}</b></th>
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