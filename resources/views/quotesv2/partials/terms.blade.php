<div class="row">
    <div class="col-md-12">
        <div class="m-portlet custom-portlet no-border">
            <div class="m-portlet__head">
                <div class="row" style="padding-top: 20px;">
                    <h3 class="title-quote size-14px">Terms & conditions</h3>
                </div>
                <div class="m-portlet__head-tools">
                    <div class="btn-open" id="terms"><span class="fa fa-angle-down"></span></div>
                </div>
            </div>
            <div class="m-portlet__body display-none portlet-padding terms"><!-- aqui-->
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line-danger" role="tablist" style="border-bottom: none; margin-bottom: 10px">
                            <li class="nav-item m-tabs__item" id="edit_li">
                                <a class="btn btn-primary-v2 btn-edit" id="edit-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    Edit Spanish&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <br>
                <div class="card card-body bg-light">
                    <span class="terms_and_conditions_span"><p><b>Spanish:</b></p> {!! $quote->terms_and_conditions !!}</span>
                    <div class="terms_and_conditions_textarea" hidden>
                        <textarea name="terms_and_conditions" class="form-control terms_and_conditions editor" id="terms_and_conditions">{!!$quote->terms_and_conditions!!}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center" id="update_terms" hidden>
                            <br>
                            <a class="btn btn-danger" id="cancel-terms">
                                Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                            </a>
                            <a class="btn btn-primary" id="update-terms">
                                Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                            </a>
                            <br>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line-danger" role="tablist" style="border-bottom: none; margin-bottom: 10px">
                            <li class="nav-item m-tabs__item" id="edit_li">
                                <a class="btn btn-primary-v2 btn-edit" id="edit-terms-english" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    Edit English&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <br>
                <div class="card card-body bg-light">
                    <span class="terms_and_conditions_english_span"><p><b>English:</b></p> {!! $quote->terms_english !!}</span>
                    <div class="terms_and_conditions_english_textarea" hidden>
                        <textarea name="terms_and_conditions_english" class="form-control terms_and_conditions_english editor" id="terms_and_conditions_english">{!!$quote->terms_english!!}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center" id="update_terms_english" hidden>
                            <br>
                            <a class="btn btn-danger" id="cancel-terms-english">
                                Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                            </a>
                            <a class="btn btn-primary" id="update-terms-english">
                                Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                            </a>
                            <br>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line-danger" role="tablist" style="border-bottom: none; margin-bottom: 10px">
                            <li class="nav-item m-tabs__item" id="edit_li">
                                <a class="btn btn-primary-v2 btn-edit" id="edit-terms-portuguese" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    Edit Portuguese&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <br>
                <div class="card card-body bg-light">
                    <span class="terms_and_conditions_portuguese_span"><p><b>Portuguese:</b></p> {!! $quote->terms_portuguese !!}</span>
                    <div class="terms_and_conditions_portuguese_textarea" hidden>
                        <textarea name="terms_portuguese" class="form-control terms_and_conditions_portuguese editor" id="terms_and_conditions_portuguese">{!!$quote->terms_portuguese!!}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center" id="update_terms_portuguese" hidden>
                            <br>
                            <a class="btn btn-danger" id="cancel-terms-portuguese">
                                Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                            </a>
                            <a class="btn btn-primary" id="update-terms-portuguese">
                                Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                            </a>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
