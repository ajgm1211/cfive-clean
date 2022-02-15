
<div class="m-portlet">
  <!--begin::Form-->

  {{ Form::model($detalle, array('route' => array('update.fail.contact', $detalle['id']), 'method' => 'get', 'id' => 'frmFCompany')) }}


  <div class="m-portlet__body">
    <div class="form-group m-form__group row"> 
      <div class="col-lg-4">
        {!! Form::label('firstnamelb', 'First Name',['style' => $detalle['firstnameclass']]) !!}
        {{ Form::text('firstname',$detalle['firstname'],['id' => 'm_firstname_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

      <div class="col-lg-4">
        {!! Form::label('lastnamelb', 'Last Name',['style' => $detalle['lastnameclass']]) !!}
        {{ Form::text('lastname', $detalle['lastname'],['id' => 'm_lastname_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

      <div class="col-lg-4">
        {!! Form::label('phonelb', 'Phone', ['style' => $detalle['phoneclass']]) !!}
        {{ Form::text('phone', $detalle['phone'],['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
    </div>
    <div class="form-group m-form__group row">


      <div class="col-lg-4">
        {!! Form::label('email', 'Email',['style' => $detalle['emailclass']]) !!}
        {{ Form::text('email', $detalle['email'],['id' => 'm_email_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
      <div class="col-lg-4">
        {!! Form::label('positionlb', 'Position',['style' => $detalle['positionclass']]) !!}
        {{ Form::text('position', $detalle['position'],['id' => 'm_position_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
      <div class="col-lg-4">
        {!! Form::label('companylb', 'Company', ['style' => $detalle['companyclass']]) !!}
        {{ Form::select('company',$companies, $detalle['company'],['id' => 'm_company_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
    </div>

    <div class="m-portlet__foot m-portlet__foot--fit">
      <div class="m-form__actions m-form__actions">
        <br />
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
