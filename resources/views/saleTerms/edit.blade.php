<div class="m-portlet">
    <!--begin::Form-->
    {!! Form::model($saleterms, ['route' => ['saleterms.update', $saleterms], 'method' => 'PUT']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">                
                @include('saleTerms.partials.add_sale_term')
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