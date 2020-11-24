<div class="modal fade" id="createContractModal" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="min-width: 700px; right: 95px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Create Contract</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background: #f7f7f7; padding: 0px">

                <div class="m-portlet mb-0 pb-0" style="background: #f7f7f7">
                    <!--begin: Form Wizard-->
                    <div class="m-wizard m-wizard--1 m-wizard--success" id="m_wizard">
                        <!--begin: Message container -->
                        <div class="m-portlet__padding-x">
                            <!-- Here you can put a message or alert -->
                        </div>
                        <!--end: Message container -->
                        <!--begin: Form Wizard Head -->
                        <div class="m-wizard__head m-portlet__padding-x mb-2">
                            <!--begin: Form Wizard Progress -->
                            <div class="m-wizard__progress">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                        aria-valuemax="100" style="background: #0072fc"></div>
                                </div>
                            </div>
                            <!--end: Form Wizard Progress -->
                            <!--begin: Form Wizard Nav -->
                            <div class="m-wizard__nav">
                                <div class="m-wizard__steps">
                                    <div class="m-wizard__step m-wizard__step--current"
                                        data-wizard-target="#m_wizard_form_step_1">
                                        <div class="m-wizard__step-info d-flex flex-column align-items-center justify-content-center">
                                            <a href="#" class="m-wizard__step-number">
                                                <span>
                                                    <span>
                                                        1
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="m-wizard__step-label mt-3">
                                                Created Contract
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-wizard__step" data-wizard-target="#m_wizard_form_step_2">
                                        <div class="m-wizard__step-info d-flex flex-column align-items-center justify-content-center">
                                            <a href="#" class="m-wizard__step-number">
                                                <span>
                                                    <span>
                                                        2
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="m-wizard__step-label mt-2">
                                                Update Ocean Freight
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-wizard__step" data-wizard-target="#m_wizard_form_step_3">
                                        <div class="m-wizard__step-info d-flex flex-column align-items-center justify-content-center">
                                            <a href="#" class="m-wizard__step-number">
                                                <span>
                                                    <span>
                                                        3
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="m-wizard__step-label mt-2">
                                                Surcharges
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-wizard__step" data-wizard-target="#m_wizard_form_step_4">
                                        <div class="m-wizard__step-info d-flex flex-column align-items-center justify-content-center">
                                            <a href="#" class="m-wizard__step-number">
                                                <span>
                                                    <span>
                                                        4
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="m-wizard__step-label mt-2">
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

                            <form method="POST" action="{{ route('search-add.contract') }}"  enctype="multipart/form-data" class="m-form m-form--label-align-left- m-form--state-"  id="m_form">
                                <!--begin: Form Body -->
                                <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
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
                                                        <label class="col-12 col-form-label">
                                                            Reference
                                                            <input type="text" name="referenceC"
                                                                class="form-control m-input mt-1" placeholder="">
                                                        </label>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <label class="col-12 col-lg-6 col-form-label">
                                                            Validity: <br>
                                                            <input type="text" name="validityC" class="form-control mt-1" id="m_daterangepicker_1_modal" readonly="" placeholder="Select time">
                                                        </label>
                                      
                                                        <label class="col-12 col-lg-6 col-form-label">
                                                            Carrier: <br>
                                                            {{ Form::select('carrierC', $carrierC, null, ['class' => 'mt-1 m-select2-general ']) }}
                                                        </label>


                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <label class="col-12 col-lg-6 col-form-label">
                                                            Equipment: <br>
                                                            {{ Form::select('group_containerC', $group_containerC, null, ['class' => 'm-select2-general mt-1']) }}
                                                        </label>
                                               
                                                        <label class="col-12 col-lg-6 col-form-label">
                                                            Direction: <br>
                                                            {{ Form::select('directionC', $directionC, null, ['class' => 'm-select2-general mt-1']) }}
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
                                                        <label class="col-12 col-sm-6 col-form-label">
                                                            Origin Port: <br>
                                                            {{ Form::select('origin_port', $harborsR, null, ['class' => 'm-select2-general mt-2']) }}
                                                        </label>
                                      
                                                        <label class="col-12 col-sm-6 col-form-label">
                                                            Destination Port: <br>
                                                            {{ Form::select('destination_port', $harborsR, null, ['class' => 'm-select2-general mt-2']) }}
                                                        </label>


                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <label class="col-12 col-sm-6 col-form-label">
                                                            Carrier : <br>
                                                            {{ Form::select('carrierR', $carrierC, null, ['class' => 'm-select2-general mt-2']) }}
                                                        </label>
                                      
                                                        <label class="col-12 col-sm-6 col-form-label">
                                                            Currency: <br>
                                                            {{ Form::select('currencyR', $currencies, null, ['class' => 'm-select2-general mt-2']) }}
                                                        </label>


                                                    </div>
                                                    <div id='containerDinamic'>

                                                    </div>
                                            
                                                </div>
                                                
                                         
                                            </div>
                                        </div>
                                    </div>
                                    <!--end: Form Wizard Step 2-->
                                    <!--begin: Form Wizard Step 3-->
                                    <div class="m-wizard__form-step" id="m_wizard_form_step_3">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="m-form__section m-form__section--first">
                                                    <div class="m-form__heading d-flex justify-content-between align-ites-center">
                                                        <h3 class="m-form__heading-title">
                                                        Surcharges
                                                        </h3>

                                                        <button type="button" id='addSurcharge' class="btn btn-sm" style="color: #0072fc; font-weight: 900">
                                                                    <i class="la  la-plus-circle"></i>
                                                                    &nbsp;&nbsp; Add Surcharge
                                                        </button>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-lg-12" id="colSurcharge">
                                                            <!--<div class="form-group m-form__group row">
                                                                <label class="col-lg-2 col-lg-2 col-form-label">
                                                                    * Type : <br>
                                                                    {{ Form::select('type[]', $surchargesS, null, ['class' => 'm-select2-general ']) }}
                                                                </label>
                                                                <label class="col-lg-3 col-lg-1 col-form-label">
                                                                    * Calculation Type : <br>
                                                                    {{ Form::select('calculation[]', $calculationTypeS, null, ['class' => 'm-select2-general ']) }}
                                                                </label>
                                              
                                                                <label class="col-lg-2 col-lg-2 col-form-label">
                                                                    * Currency : <br>
                                                                    {{ Form::select('currency[]', $currencies, null, ['class' => 'm-select2-general ']) }}
                                                                </label>
                                                                
                                                                    
                                                                <label class="col-lg-3 col-lg-2 col-form-label">
                                                                    * Amount : <br>
                                                                    <input type="text" name="amount[]" class="form-control m-input" placeholder="" value="0">
                                                                </label>                                                         
                                                            </div>-->

                                                        
                                                        </div>
                                                    </div>

                                                            <!--Clone Row -->
                                                    <div class="form-group m-form__group row hide "  id="cloneSurcharge">
                                                        <label class="col-lg-3 col-form-label">
                                                            Type : <br>
                                                            {{ Form::select('typeC[]', $surchargesS, null, ['class' => 'typeC  form-control' ]) }}
                                                        </label>
                                                        <label class="col-lg-3 col-lg-1 col-form-label">
                                                            Calculation Type : <br>
                                                            {{ Form::select('calculationC[]', $calculationTypeS, null, ['class' => 'calculationC form-control ']) }}
                                                        </label>
                                      
                                                        <label class="col-lg-3 col-lg-2 col-form-label">
                                                            Currency : <br>
                                                            {{ Form::select('currencyC[]', $currencies, null, ['class' => 'currencyC form-control ']) }}
                                                        </label>
                                                        
                                                            
                                                        <label class="col-lg-2 col-lg-2 col-form-label">
                                                            Amount : <br>
                                                            <input type="text" name="amountC[]" class="form-control m-input amountC" placeholder="" value="0">
                                                        </label>    
                                                        <label class="col-lg-1 col-form-label d-flex align-items-center">
                                                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                <span>
                                                                    <a  class="removeSurcharge" data-container="body" data-toggle="m-tooltip" data-placement="top" title="" data-original-title="Delete Row" aria-describedby="tooltip964649"> <i class="la la-minus-circle btn-plus__form" style="color:red; font-size: 18px;cursor:pointer"></i></a>
                                                                </span>
                                                            </span>
                                                          
                                                        </label>                                                         
                                                    </div>
                                                         <!-- END Clone Row -->
                                          
                                                </div>
                                           
                                            </div>
                                        </div>
                                    </div>
                                    <!--end: Form Wizard Step 3-->
                                    <!--begin: Form Wizard Step 4-->
                                    <div class="m-wizard__form-step" id="m_wizard_form_step_4">
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
                                               
                                                            <div class="m-portlet__body">
                                                                <!--begin::Section-->
                                                                <div class="m-section m-section--last">
                                                                  <div class="m-section__content">
                                                                    <!--begin::Preview-->
                                                                    <div class="m-demo">
                                                                      <div class="m-demo__preview" style="border: none !important;">
                                                                        <div class="d-flex justify-content-center">
                                                                          <div class="m-dropzone dropzone m-dropzone--success"  id="document-dropzone">
                                                                            <div class="m-dropzone__msg dz-message needsclick">
                                                                              <h3 class="m-dropzone__msg-title">
                                                                                Drop files here or click to upload.
                                                                              </h3>
                                                                              <span class="m-dropzone__msg-desc">
                                                                                Only image, pdf and psd files are allowed for upload
                                                                              </span>
                                                                            </div>
                                                                          </div>
                                                                        </div>
                                            
                                            
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                              </div>
                                                        </div>
                                                    </div>
                                          
                                                </div>
                                           
                                            </div>
                                        </div>
                                    </div>
                                    <!--end: Form Wizard Step 4-->
                                </div>
                                <!--end: Form Body -->
                                <!--begin: Form Actions -->
                                <div class="m-portlet__foot m-portlet__foot--fit " style="background: white">
                                    <div class="m-form__actions m-form__actions">
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-end align-items-center">
                                                <a href="#" class="btn m-btn m-btn--custom m-btn--icon"
                                                    data-wizard-action="prev">
                                                    <span>
                                                        <i class="la la-arrow-left"></i>
                                                        &nbsp;&nbsp;
                                                        <span>
                                                            Back
                                                        </span>
                                                    </span>
                                                </a>
                                                <a href="#" class="ml-3 btn btn-primary m-btn m-btn--custom m-btn--icon"
                                                    data-wizard-action="submit" style="background-color: #0072fc !important;border: none !important;">
                                                    <span>
                                                        <i class="la la-check"></i>
                                                        &nbsp;&nbsp;
                                                        <span>
                                                            Submit
                                                        </span>
                                                    </span>
                                                </a>
                                                <a href="#" class="ml-3 btn btn-primary m-btn m-btn--custom m-btn--icon"
                                                    data-wizard-action="next" style="background-color: #0072fc !important;border: none !important;">
                                                    <span>
                                                        <span>
                                                            Continue
                                                        </span>
                                                        &nbsp;&nbsp;
                                                        <i class="la la-arrow-right"></i>
                                                    </span>
                                                </a>
                                            </div>                                            
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