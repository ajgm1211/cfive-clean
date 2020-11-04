<div class="modal fade" id="createContractModal" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="min-width: 900px; right: 200px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Create Contract</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="m-portlet">
                    <!--begin: Portlet Head-->
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                    + Add New Contract

                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <a href="#" data-toggle="m-tooltip"
                                        class="m-portlet__nav-link m-portlet__nav-link--icon" data-direction="left"
                                        data-width="auto" title="Get help with filling up this form">
                                        <i class="flaticon-info m--icon-font-size-lg3"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--end: Portlet Head-->
                    <!--begin: Form Wizard-->
                    <div class="m-wizard m-wizard--1 m-wizard--success" id="m_wizard">
                        <!--begin: Message container -->
                        <div class="m-portlet__padding-x">
                            <!-- Here you can put a message or alert -->
                        </div>
                        <!--end: Message container -->
                        <!--begin: Form Wizard Head -->
                        <div class="m-wizard__head m-portlet__padding-x">
                            <!--begin: Form Wizard Progress -->
                            <div class="m-wizard__progress">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                            <!--end: Form Wizard Progress -->
                            <!--begin: Form Wizard Nav -->
                            <div class="m-wizard__nav">
                                <div class="m-wizard__steps">
                                    <div class="m-wizard__step m-wizard__step--current"
                                        data-wizard-target="#m_wizard_form_step_1">
                                        <div class="m-wizard__step-info">
                                            <a href="#" class="m-wizard__step-number">
                                                <span>
                                                    <span>
                                                        1
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="m-wizard__step-line">
                                                <span></span>
                                            </div>
                                            <div class="m-wizard__step-label">
                                  Created Contract
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-wizard__step" data-wizard-target="#m_wizard_form_step_2">
                                        <div class="m-wizard__step-info">
                                            <a href="#" class="m-wizard__step-number">
                                                <span>
                                                    <span>
                                                        2
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="m-wizard__step-line">
                                                <span></span>
                                            </div>
                                            <div class="m-wizard__step-label">
                                                Update Ocean Freight
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-wizard__step" data-wizard-target="#m_wizard_form_step_3">
                                        <div class="m-wizard__step-info">
                                            <a href="#" class="m-wizard__step-number">
                                                <span>
                                                    <span>
                                                        3
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="m-wizard__step-line">
                                                <span></span>
                                            </div>
                                            <div class="m-wizard__step-label">
                                                Add Files
                                            </div>
                                        </div>
                                    </div>
           
                                </div>
                            </div>
                            <!--end: Form Wizard Nav -->
                        </div>
                        <!--end: Form Wizard Head -->
                        <!--begin: Form Wizard Form-->
                        <div class="m-wizard__form">

                            <form class="m-form m-form--label-align-left- m-form--state-" id="m_form">
                                <!--begin: Form Body -->
                                <div class="m-portlet__body">
                                    <!--begin: Form Wizard Step 1-->
                                    <div class="m-wizard__form-step m-wizard__form-step--current"
                                        id="m_wizard_form_step_1">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="m-form__section m-form__section--first">
                                                    <div class="m-form__heading">
                                                        <h3 class="m-form__heading-title">
                                                            Contract
                                                        </h3>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <label class="col-xl-2 col-lg-3 col-form-label">
                                                            * Reference
                                                        </label>
                                                        <div class="col-xl-10 col-lg-9">
                                                            <input type="text" name="reference"
                                                                class="form-control m-input" placeholder=""
                                                                value="1-541-754-3010">
                                                        </div>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <label class="col-lg-5 col-lg-1 col-form-label">
                                                            * Validity: <br>
                                                            <input type="text" class="form-control" id="m_daterangepicker_1_modal" readonly="" placeholder="Select time">
                                                        </label>
                                      
                                                        <label class="col-xl-5 col-lg-2 col-form-label">
                                                            * Carrier: <br>
                                                            {{ Form::select('group_container', $carrierC, null, ['class' => 'm-select2-general ']) }}
                                                        </label>


                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <label class="col-xl-5 col-lg-2 col-form-label">
                                                            * Equipment: <br>
                                                            {{ Form::select('group_container', $group_containerC, null, ['class' => 'm-select2-general ']) }}
                                                        </label>
                                               
                                                        <label class="col-xl-5 col-lg-2 col-form-label">
                                                            * Direction: <br>
                                                            {{ Form::select('group_container', $directionC, null, ['class' => 'm-select2-general ']) }}
                                                        </label>


                                                    </div>

                                                </div>


                                            </div>
                                        </div>
                                    
                                    </div>
                                    <!--end: Form Wizard Step 1-->
                                    <!--begin: Form Wizard Step 2-->
                                    <div class="m-wizard__form-step" id="m_wizard_form_step_2">
                                        <div class="row">
                                            <div class="col-xl-12 ">
                                                <div class="m-form__section m-form__section--first">
                                                    <div class="m-form__heading">
                                                        <h3 class="m-form__heading-title">
                                                           Update Ocean Freigh
                                                        </h3>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <label class="col-lg-5 col-lg-1 col-form-label">
                                                            * Origin Port: <br>
                                                            {{ Form::select('origin_port', $harbors, null, ['class' => 'm-select2-general ']) }}
                                                        </label>
                                      
                                                        <label class="col-xl-5 col-lg-2 col-form-label">
                                                            * Destination Port: <br>
                                                            {{ Form::select('destination_port', $harbors, null, ['class' => 'm-select2-general ']) }}
                                                        </label>


                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <label class="col-lg-5 col-lg-1 col-form-label">
                                                            * Carrier : <br>
                                                            {{ Form::select('origin_port', $carrierC, null, ['class' => 'm-select2-general ']) }}
                                                        </label>
                                      
                                                        <label class="col-xl-5 col-lg-2 col-form-label">
                                                            * Currency: <br>
                                                            {{ Form::select('destination_port', $currencies, null, ['class' => 'm-select2-general ']) }}
                                                        </label>


                                                    </div>
                                                    <div id='containerDinamic'>
                                                        
                                                    </div>
                                            
                                                </div>
                                                <div class="m-separator m-separator--dashed m-separator--lg"></div>
                                         
                                            </div>
                                        </div>
                                    </div>
                                    <!--end: Form Wizard Step 2-->
                                    <!--begin: Form Wizard Step 3-->
                                    <div class="m-wizard__form-step" id="m_wizard_form_step_3">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="m-form__section m-form__section--first">
                                                    <div class="m-form__heading">
                                                        <h3 class="m-form__heading-title">
                                                           Add Files
                                                        </h3>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-lg-12">
                                                            <label class="form-control-label">
                                                                * Cardholder Name:
                                                            </label>
                                                            <input type="text" name="billing_card_name"
                                                                class="form-control m-input" placeholder=""
                                                                value="Nick Stone">
                                                        </div>
                                                    </div>
                                          
                                                </div>
                                           
                                            </div>
                                        </div>
                                    </div>
                                    <!--end: Form Wizard Step 3-->
                                    <!--begin: Form Wizard Step 4-->
                        
                                    <!--end: Form Wizard Step 4-->
                                </div>
                                <!--end: Form Body -->
                                <!--begin: Form Actions -->
                                <div class="m-portlet__foot m-portlet__foot--fit m--margin-top-40">
                                    <div class="m-form__actions m-form__actions">
                                        <div class="row">
                                            <div class="col-lg-2"></div>
                                            <div class="col-lg-4 m--align-left">
                                                <a href="#" class="btn btn-secondary m-btn m-btn--custom m-btn--icon"
                                                    data-wizard-action="prev">
                                                    <span>
                                                        <i class="la la-arrow-left"></i>
                                                        &nbsp;&nbsp;
                                                        <span>
                                                            Back
                                                        </span>
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="col-lg-4 m--align-right">
                                                <a href="#" class="btn btn-primary m-btn m-btn--custom m-btn--icon"
                                                    data-wizard-action="submit">
                                                    <span>
                                                        <i class="la la-check"></i>
                                                        &nbsp;&nbsp;
                                                        <span>
                                                            Submit
                                                        </span>
                                                    </span>
                                                </a>
                                                <a href="#" class="btn btn-primary m-btn m-btn--custom m-btn--icon"
                                                    data-wizard-action="next">
                                                    <span>
                                                        <span>
                                                            Save & Continue
                                                        </span>
                                                        &nbsp;&nbsp;
                                                        <i class="la la-arrow-right"></i>
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="col-lg-2"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--end: Form Actions -->
                            </form>
                        </div>
                        <!--end: Form Wizard Form-->
                    </div>
                    <!--end: Form Wizard-->
                </div>
                <!--End::Main Portlet-->

            </div>
        </div>
    </div>
</div>



<script>
    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });

</script>
