@if($quote->terms_and_conditions!='')
    <div class="clearfix">
        <table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
            <thead class="title-quote header-table">
                <tr>
                    <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Terms and conditions</b></th>
                    <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Términos y condiciones</b></th>
                    <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Termos e Condições</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:15px;">
                        <span class="text-justify">{!! @$quote->terms_and_conditions !!}</span>
                    </td>
                </tr>                        
            </tbody>
        </table>
    </div>
@endif
<br>