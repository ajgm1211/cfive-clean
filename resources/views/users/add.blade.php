
<div class="modal fade" id="m_modal_6" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    Add user
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m-portlet">

                    <!--begin::Form-->
                    {!! Form::open(['route' => 'users.create']) !!}
                    <div class="m-portlet__body">
                        <div class="m-form__section m-form__section--first">
                            <div class="form-group m-form__group">
                                @include('users.partials.form_users')


                            </div>
                        </div>
                        <div class="m-portlet__foot m-portlet__foot--fit">
                            <div class="m-form__actions m-form__actions">
                                {!! Form::submit('Save', ['class'=> 'btn btn-primary  btn-sm']) !!}
                                <a class="btn btn-success btn-sm" href="{{url()->previous()}}">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <!--end::Form-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>
