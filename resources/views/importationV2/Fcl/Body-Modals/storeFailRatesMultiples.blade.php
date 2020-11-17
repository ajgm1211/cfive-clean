{{ Form::open(array('route' => 'store.Multiples.Rates.Fcl', 'method' => 'post', 'id' => 'frmRates')) }}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Edit Good Rates
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">
            &times;
        </span>
    </button>
</div>
<div id="edit-modal-body" class="modal-body">
    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-6">
                {!! Form::label('origin_port', 'Origin Port') !!}
                {{ Form::select('origin_id', $harbor,null,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;','required']) }} 
            </div>
            <div class="col-lg-6">
                {!! Form::label('destination_port', 'Destination Port') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('destiny_id', $harbor,null,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;','required']) }}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>
            </div>
            <div class="form-group m-form__group row">

                <div class="col-lg-12">
                    <input type="hidden" value="{{$contract_id}}" name="contract_id" id="contract_id" />
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
        <div id="edit-modal-footer" class="modal-footer">
            {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
            <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
    </div>
</div>
{!! Form::close() !!}
<script>
    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });
    $(document).ready(function(e){
        //alert(nameTab);
        // frmRates id del formulario Auto Save TAB
        $("#frmRates").append('<input type="hidden" name="nameTab" value="'+nameTab+'">');
    });
</script>
