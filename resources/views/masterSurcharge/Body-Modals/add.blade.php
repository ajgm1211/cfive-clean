{!! Form::open(['route' => 'MasterSurcharge.store', 'method' => 'post','class' => 'form-group m-form__group','id' => 'frmSurcharges']) !!}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Add - Surcharge Detail
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">
            &times;
        </span>
    </button>
</div>
<div class="modal-body" id="global-body">
    <div class="form-group m-form__group row">
        <div class="col-lg-4">
            {!! Form::label('surcharger', 'Surcharger') !!}
            {{ Form::select('surcharger',$surchargers, null,['id' => 'surcharger_id','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
        </div>
        <div class="col-lg-4">
            {!! Form::label('carrier', 'Carrier') !!}
            {{ Form::select('carrier',$carriers, null,['id' => 'carrier_id','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
        </div>        
        <div class="col-lg-4">
            {!! Form::label('typedestiny', 'Type Destiny') !!}
            {{ Form::select('typedestiny',$typedestiny, null,['id' => 'typedestiny_id','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-4">
            {!! Form::label('equiment', 'Equiment') !!}
            <div class="m-input-icon m-input-icon--right">
                {!! Form::select('equiment_id',@$equiments,null,['class'=>'m-select2-general form-control equiment_id','id'=>'equiment_id','onchange' => 'loadContainersAdd()','placeholder'=>'Select type Equiment'])!!}
            </div>
        </div>
        <div class="col-lg-4">
            {!! Form::label('calculationt', 'Calculation Type') !!}
            <div class="m-input-icon m-input-icon--right">
                {{ Form::select('calculationtype', $calculationtype,null,['id' => 'calculationtype_ids','class'=>'m-select2-general form-control calculationtype_id' ,'required' => 'true']) }}
            </div>

        </div>
        <div class="col-lg-4">
            {!! Form::label('direction', 'Direction') !!}
            {{ Form::select('direction',$directions, null,['id' => 'direction_id','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="submit" class="btn btn-primary"  value="Save">

    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Close
    </button>
</div>

{!! Form::close() !!}

<script>

    $('.m-select2-general').select2({});

    function loadContainersAdd(){
        var equiment = $(".equiment_id").select2('val');
        $('.calculationtype_id').val(null).trigger('change');
        $('.calculationtype_id').select2({
            placeholder: "Select a Calculation Type",
            ajax: {
                url: '{!! route("get.calculations.equiment") !!}',
                data:{equiment},
                dataType: 'json'
            }
        });
    }
</script>