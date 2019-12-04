


<div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-max-height="200">

    {{ Form::open(array('route' => 'store.Multiples.Rates.Fcl', 'method' => 'post', 'id' => 'frmRates')) }}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-6">
                {!! Form::label('origin_port', 'Origin Port') !!}
                {{ Form::select('origin_id', $harbor,null,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;','multiple']) }} 
            </div>
            <div class="col-lg-6">
                {!! Form::label('destination_port', 'Destination Port') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('destiny_id', $harbor,null,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;','multiple']) }}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>
            </div>
            <div class="form-group m-form__group row">

                <div class="col-lg-12">
                    @foreach($arreglo as $ids)
                    <input type="hidden" value="{{$ids}}" name="arreglo[]" id="arreglo_id" />
                    @endforeach
                    <style>
                        .scrollStyle
                        {
                            overflow-x:auto;
                        }
                    </style>
                </div>
            </div>
        </div>  
        <br>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">Cancel</span>
                </button>
            </div>
            <br>
        </div>

        {!! Form::close() !!}

    </div>

    <script>

        $('.m-select2-general').select2({
            placeholder: "Select an option"
        });





    </script>
