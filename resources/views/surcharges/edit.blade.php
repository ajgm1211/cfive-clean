

<div class="m-portlet">


    <!--begin::Form-->
    {!! Form::model($surcharges, ['route' => ['surcharges.update', $surcharges], 'method' => 'PUT']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                            @include('surcharges.partials.form_surcharges')


            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Save', ['class'=> 'btn btn-primary  btn-sm']) !!}
                <a class="btn btn-success btn-sm" href="{{url()->previous()}}">
                    Cancel
                </a>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <!--end::Form-->
</div>



