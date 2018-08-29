<div class="m-portlet__body">
    <div class="form-group m-form__group row">
        <div class="col-md-3">
            <label>Select sub-user:</label>
            {{ Form::select('users[]', $users, null,['class'=>'custom-select form-control']) }}
        </div>
        <div class="col-md-3">
            <label>Pick up date</label>
            <div class="input-group date">
                {!! Form::text('pick_up_date', null, ['placeholder' => 'Select date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1']) !!}
                <div class="input-group-append">
                   <span class="input-group-text">
                      <i class="la la-calendar-check-o"></i>
                   </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill button">
                Filter
            </button>
        </div>
    </div>
</div>