<div class="m-portlet">
    {{ Form::model($quote, array('route' => array('quotes.update.status', $quote->id), 'method' => 'POST')) }}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                @include('quotes.partials.changeStatusModal')
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>