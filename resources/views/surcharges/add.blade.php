<!--begin::Form-->
{!! Form::open(['route' => 'surcharges.store']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('surcharges.partials.form_surcharges')
        <div class="form-group m-form__group">
            {!! Form::label('sale_term_id', 'Sale Terms') !!}<br> 
            {{ Form::select('sale_term_id',$sale_terms,null,['class'=>'custom-select form-control','id' => 'sale_term_id','placeholder'=>'Select an option']) }}
        </div>
        <hr>
        <div class="form-group m-form__group">
            <div class="row">
                <div class="col-12">
                    {!! Form::label('extra_fields', 'Extra fields') !!}
                    <button type="button" class="btn btn-primary btn-sm pull-right" onclick="addExtraField()">Add <i
                            class="fa fa-plus"></i></button>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-6">
                    {!! Form::text('key_name[]', null, ['placeholder' => 'Please enter a key name','class' =>
                    'form-control
                    m-input']) !!}
                </div>
                <div class="col-6">
                    {!! Form::text('key_value[]', null, ['placeholder' => 'Please enter a value','class' =>
                    'form-control
                    m-input']) !!}
                </div>
            </div>
            <div class="hide" id="hide_extra_field">
                <div class="row" style="margin-top:3px;">
                    <div class="col-6">
                        {!! Form::text('key_name[]', null, ['placeholder' => 'Please enter a key name','class' =>
                        'form-control
                        m-input']) !!}
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-3">
                            {!! Form::text('key_value[]', null, ['placeholder' => 'Please enter a value','class' =>
                            'form-control
                            m-input']) !!}
                            <div class="input-group-append">
                                <a href="#" class="btn btn-danger btn-sm deleter"><i class="fa fa-close"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="m-form__actions m-form__actions">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
</div>
<br>
{!! Form::close() !!}
<!--end::Form-->

<script src="/js/users.js"></script>
<script type="text/javascript">
    $('#sale_term_id').select2({
        placeholder: "Select an option"
    });
</script>