<div class="form-group m-form__group">
    {!! Form::label('status_quote_id', 'Status') !!}<br>
    {{ Form::select('status_quote_id',$status_quotes,$quote->status_quote_id,['placeholder' => 'Please choose a status','class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
</div>