{{ Form::model($account, array('route' => array('disp.change.equiment.Account.gcfcl', $account->id), 'method' => 'put', 'id' => 'frmcontract')) }}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Change Equiment / DRY To  {{$data['request']['equiement']}}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">
            &times;
        </span>
    </button>
</div>
<div id="modal-body" class="modal-body">
    <div class="form-group row">
        <div class="col-lg-2">
            <label for="id_account" class="form-control-label">
                ID Account
            </label>
            <input type="text" name="id_account" value="{{$data['id']}}" required="required" class="form-control" disabled id="id_account">
        </div>
        <div class="col-lg-2">
            <label for="id_request" class="form-control-label">
                ID Request
            </label>
            <input type="text" name="id_request" value="{{$data['request']['id']}}" required="required" class="form-control" disabled id="id_request">
        </div>
        <div class="col-lg-4">
            <label for="id_company" class="form-control-label">
                Company User
            </label>
            <input type="text" name="id_company" value="{{$data['companyuser']}}" required="required" class="form-control" disabled id="id_company">
        </div>
        <div class="col-lg-4">
            <label for="id_date" class="form-control-label">
                Fecha
            </label>
            <input type="text" name="id_date" value="{{$data['date']}}" required="required" class="form-control" disabled id="id_date">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            <label for="id_name" class="form-control-label">
                Name
            </label>
            <input type="text" name="id_name" value="{{$data['name']}}" required="required" class="form-control" disabled id="id_name">
        </div>
        <div class="col-lg-3">
            <label for="id_status" class="form-control-label">
                Status
            </label>
            <input type="text" name="id_status" value="{{$data['status']}}" required="required" class="form-control" disabled id="id_status">
        </div>
        <div class="col-lg-3">
            <label for="id_equiment" class="form-control-label">
                Equiment
            </label>
            <input type="text" style="color:{{$data['request']['color']}}" name="number" value="{{$data['request']['equiement']}}" required="required" class="form-control" disabled id="id_equiment">
        </div>
    </div>
    <hr>
    <div class="form-group row">
        <div class="col-lg-3"></div>
        <div class="col-lg-5">
            <input type="hidden" name="equiment_id" value="{{$data['request']['equiement_id']}}">
            <label for="groupContainers" class="form-control-label">
                Equiment
            </label>
            {!! Form::select('equiment',$data['equiment'],$data['request']['equiement_id'],['class'=>'m-select2-general form-control','required','inpName' => 'Equipments Type','id'=>'groupContainers'])!!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="submit" class="btn btn-primary" value="Update" >
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Close
    </button>
</div>
{{ Form::close() }}
<script>

    $('.m-select2-general').select2({

    });

</script>