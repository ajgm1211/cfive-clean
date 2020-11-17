
<div class="m-portlet">
  <!--begin::Form-->

  {{ Form::model($detalle, array('route' => array('update.fail.company', $detalle['id']), 'method' => 'get', 'id' => 'frmFCompany')) }}


  <div class="m-portlet__body">
    <div class="form-group m-form__group row"> 
      <div class="col-lg-4">
        {!! Form::label('businessnamelb', 'Business Name',['style' => $detalle['classbusiness']]) !!}
        {{ Form::text('businessname',$detalle['businessname'],['id' => 'm_businessname_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

      <div class="col-lg-4">
        {!! Form::label('phonelb', 'Phone',['style' => $detalle['classphone']]) !!}
        {{ Form::text('phone', $detalle['phone'],['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

      <div class="col-lg-4">
        {!! Form::label('addresslb', 'Address') !!}
        {{ Form::text('address', $detalle['address'],['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
    </div>
    <div class="form-group m-form__group row">


      <div class="col-lg-4">
        {!! Form::label('email', 'Email',['style' => $detalle['classemail']]) !!}
        {{ Form::text('email', $detalle['email'],['id' => 'm_email_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
      <div class="col-lg-4">
        {!! Form::label('compnyuserlb', 'compnyuser') !!}
        {{ Form::text('compnyuser', $detalle['compnyuser'],['id' => 'm_compnyuser_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;','readonly'=>'readonly']) }} 
        <input type="hidden" name="compnyuserid" value="{{$detalle['compnyuserid']}}" />
      </div>
      <div class="col-lg-4">
        {!! Form::label('ownerlb', 'Owner') !!}
        {{ Form::text('owner', $detalle['owner'],['id' => 'm_ownerlb_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;','readonly'=>'readonly']) }} 
        <input type="hidden" name="ownerid" value="{{$detalle['ownerid']}}" />
      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        {!! Form::label('taxnumberlb', 'Tax Number') !!}
        {{ Form::text('taxnumber', $detalle['taxnumber'],['id' => 'm_taxnumber_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

    </div>
  </div>  



  <br>
  <hr>
  <div class="m-portlet__foot m-portlet__foot--fit">
    <div class="m-form__actions m-form__actions">
      {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
      <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
      </button>
    </div>
  </div>
</div>
{!! Form::close() !!}


<!--end::Form-->
</div>
<script>


  $('.m-select2-general').select2({

  });


</script>
