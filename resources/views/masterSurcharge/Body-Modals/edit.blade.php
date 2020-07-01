{{ Form::model($masterSurcharge, array('route' => array('MasterSurcharge.update', $masterSurcharge->id), 'method' => 'put', 'id' => 'frmSurcharges')) }}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Add - Master Surcharge 
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
            {!! Form::label('name', 'Name') !!}
            {{ Form::text('name', $masterSurcharge->name,['id' => 'name_id','class'=>'form-control' ,'required' => 'true']) }}
        </div>
        <div class="col-lg-4">
            {!! Form::label('carrier', 'Carrier') !!}
            {{ Form::select('carrier',$carriers, $masterSurcharge->carrier_id,['id' => 'carrier_id','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
        </div>        
        <div class="col-lg-4">
            {!! Form::label('typedestiny', 'Type Destiny') !!}
            {{ Form::select('typedestiny',$typedestiny, $masterSurcharge->typedestiny_id,['id' => 'typedestiny_id','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-4">
            {!! Form::label('calculationt', 'Calculation Type') !!}
            <div class="m-input-icon m-input-icon--right">
                {{ Form::select('calculationtype', $calculationtype,$masterSurcharge->calculationtype_id,['id' => 'calculationtype_id','class'=>'m-select2-general form-control ' ,'required' => 'true']) }}
            </div>

        </div>
        <div class="col-lg-4">
            {!! Form::label('direction', 'Direction') !!}
            {{ Form::select('direction',$directions, $masterSurcharge->direction_id,['id' => 'direction_id','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
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

    $('.m-select2-general').select2({
    });
</script>