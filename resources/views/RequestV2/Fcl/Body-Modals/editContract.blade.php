{{ Form::model($contract, array('route' => array('update.contract.edit', $contract->id), 'method' => 'put', 'id' => 'frmcontract')) }}
<div class="modal-header" >
    <h5 class="modal-title" id="exampleModalLongTitle">
        Status Of The Request
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">
            &times;
        </span>
    </button>
</div>
<div  class="modal-body">
    <h6>Contract</h6>
    <div class="form-group row">
        <br>
        <div class="col-lg-4">
            <label for="NameMD" class="form-control-label">
                Name:
            </label>
            <input type="text" name="name" value="{{$contract->name}}" required="required" class="form-control"  id="NameMD">
        </div>
        <div class="col-lg-4">
            {!! Form::label('validation_expire', 'Validation') !!}
            <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="validation_expire" type="text" value="{{$contract->validity.' / '.$contract->expire}}">

        </div>
        <div class="col-lg-4">
            <label for="number" class="form-control-label">
                Company User:
            </label>
            <input type="hidden" name="company_user_id" value="{{$contract->company_user_id}}" required="required" class="form-control"  id="Company">
            <input type="text" name="company_user" value="{{$contract->companyuser->name}}" required="required" class="form-control" disabled id="Company">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-4">

            {!! Form::label('status', 'Status') !!}<br>
            {{ Form::select('status',['publish' => 'Publish','draft' => 'Draft','incomplete' => 'Imcomplete','expired' => 'Expired'],$contract->status,['class'=>'form-control m-select2-general','id' => 'm_select2_2_modal']) }}

        </div>
        <div class="col-lg-4">
            <label for="number" class="form-control-label">
                Direction:
            </label>

            {{ Form::select('direction_id',$directions,$contract->direction_id,['class'=>'form-control m-select2-general','id' => 'm_select2_2_modal']) }}
        </div>
        <div class="col-lg-4">
            <label for="number" class="form-control-label">
                Carrier:
            </label>

            {{ Form::select('carriers_id[]',$carriers,$contract->carriers->pluck('carrier_id'),['class'=>'form-control m-select2-general','id' => 'm_select2_2_modal','multiple']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="submit" class="btn btn-primary"  value="Load">
        
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Close
    </button>
</div>

{{ Form::close() }}

<script>

    $('.m-select2-general').select2({

    });
</script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>