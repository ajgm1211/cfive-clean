<div class="row">
    <div class="col-md-12 ">
        <div class="">
            <div class="header-charges" >
                <div class="row " style="padding-left: 20px;">
                    <h5 class="title-quote size-12px">General Remarks</h5>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="remarks_span_{{$v}}">
                    <button class="btn btn-primary-v2 edit-remarks btn-edit" onclick="edit_remark('remarks_span_{{$v}}','remarks_textarea_{{$v}}','update_remarks_{{$v}}')" style="margin-bottom: 12px;">
                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                    </button>
                    <div class="card card-body bg-light remarks_box_{{$v}}">
                        <span>{!! $rate->remarks !!}</span>
                        <br>
                    </div>
                </div>
                <div class="remarks_textarea_{{$v}}" hidden>
                    <textarea name="remarks_{{$v}}" class="form-control remarks_{{$v}} editor">{!!$rate->remarks!!}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center update_remarks_{{$v}}"  hidden>
                        <br>
                        <button class="btn btn-primary update-remarks_{{$v}}" onclick="update_remark({{$rate->id}},'remarks_{{$v}}',{{$v}},'all')">
                            Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger cancel-remarks_{{$v}}" onclick="cancel_update('remarks_span_{{$v}}','remarks_textarea_{{$v}}','update_remarks_{{$v}}')">
                            Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                        </button>
                        <br>
                    </div>
                </div>
            </div>
        </div>
        <div class="">
            <div class="header-charges" >
                <div class="row " style="padding-left: 20px;">
                    <h5 class="title-quote size-12px">English Remarks</h5>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="remarks_span_english_{{$v}}">
                    <button class="btn btn-primary-v2 edit-remarks btn-edit" onclick="edit_remark('remarks_span_english_{{$v}}','remarks_textarea_english_{{$v}}','update_remarks_english_{{$v}}')" style="margin-bottom: 12px;">
                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                    </button>
                    <div class="card card-body bg-light remarks_box_english_{{$v}}">
                        <span>{!! $rate->remarks_english !!}</span>
                        <br>
                    </div>
                </div>
                <div class="remarks_textarea_english_{{$v}}" hidden>
                    <textarea name="remarks_english_{{$v}}" class="form-control remarks_{{$v}} editor">{!!$rate->remarks_english!!}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center update_remarks_english_{{$v}}"  hidden>
                        <br>
                        <button class="btn btn-primary update-remarks_english_{{$v}}" onclick="update_remark({{$rate->id}},'remarks_english_{{$v}}',{{$v}},'english')">
                            Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger cancel-remarks_{{$v}}" onclick="cancel_update('remarks_span_english_{{$v}}','remarks_textarea_english_{{$v}}','update_remarks_english_{{$v}}')">
                            Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                        </button>
                        <br>
                    </div>
                </div>
            </div>
        </div>
         <div class="">
            <div class="header-charges" >
                <div class="row " style="padding-left: 20px;">
                    <h5 class="title-quote size-12px">Spanish Remarks</h5>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="remarks_span_spanish_{{$v}}">
                    <button class="btn btn-primary-v2 edit-remarks btn-edit" onclick="edit_remark('remarks_span_spanish_{{$v}}','remarks_textarea_spanish_{{$v}}','update_remarks_spanish_{{$v}}')" style="margin-bottom: 12px;">
                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                    </button>
                    <div class="card card-body bg-light remarks_box_spanish_{{$v}}">
                        <span>{!! $rate->remarks_spanish !!}</span>
                        <br>
                    </div>
                </div>
                <div class="remarks_textarea_spanish_{{$v}}" hidden>
                    <textarea name="remarks_spanish_{{$v}}" class="form-control remarks_spanish_{{$v}} editor">{!!$rate->remarks_spanish!!}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center update_remarks_spanish_{{$v}}"  hidden>
                        <br>
                        <button class="btn btn-primary update-remarks_spanish_{{$v}}" onclick="update_remark({{$rate->id}},'remarks_spanish_{{$v}}',{{$v}},'spanish')">
                            Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger cancel-remarks_{{$v}}" onclick="cancel_update('remarks_span_spanish_{{$v}}','remarks_textarea_spanish_{{$v}}','update_remarks_spanish_{{$v}}')">
                            Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                        </button>
                        <br>
                    </div>
                </div>
            </div>
        </div>
         <div class="">
            <div class="header-charges" >
                <div class="row " style="padding-left: 20px;">
                    <h5 class="title-quote size-12px">Portuguese Remarks</h5>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="remarks_span_portuguese_{{$v}}">
                    <button class="btn btn-primary-v2 edit-remarks btn-edit" onclick="edit_remark('remarks_span_portuguese_{{$v}}','remarks_textarea_portuguese_{{$v}}','update_remarks_portuguese_{{$v}}')" style="margin-bottom: 12px;">
                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                    </button>
                    <div class="card card-body bg-light remarks_box_portuguese_{{$v}}">
                        <span>{!! $rate->remarks_portuguese !!}</span>
                        <br>
                    </div>
                </div>
                <div class="remarks_textarea_portuguese_{{$v}}" hidden>
                    <textarea name="remarks_portuguese_{{$v}}" class="form-control remarks_portuguese_{{$v}} editor">{!!$rate->remarks_portuguese!!}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center update_remarks_portuguese_{{$v}}"  hidden>
                        <br>
                        <button class="btn btn-primary update-remarks_portuguese_{{$v}}" onclick="update_remark({{$rate->id}},'remarks_portuguese_{{$v}}',{{$v}},'portuguese')">
                            Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger cancel-remarks_portuguese_{{$v}}" onclick="cancel_update('remarks_span_portuguese_{{$v}}','remarks_textarea_portuguese_{{$v}}','update_remarks_portuguese_{{$v}}')">
                            Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                        </button>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>