@extends('layouts.app')
@section('title', 'Usuario')
@section('content')
<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon m--hide">
                                <i class="la la-gear"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Default Form Layout
                            </h3>
                        </div>
                    </div>
                </div>
                <!--begin::Form-->
                <form class="m-form">
                    <div class="m-portlet__body">
                        <div class="m-form__section m-form__section--first">
                            <div class="form-group m-form__group">
                                <label for="example_input_full_name">
                                    Full Name:
                                </label>
                                <input type="email" class="form-control m-input" placeholder="Enter full name">
                                <span class="m-form__help">
                                    Please enter your full name
                                </span>
                            </div>
                            <div class="form-group m-form__group">
                                <label>
                                    Email address:
                                </label>
                                <input type="email" class="form-control m-input" placeholder="Enter email">
                                <span class="m-form__help">
                                    We'll never share your email with anyone else
                                </span>
                            </div>
                            <div class="form-group m-form__group">
                                <label>
                                    Subscription
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                            $
                                        </span>
                                    </div>
                                    <input type="text" class="form-control m-input" placeholder="99.9">
                                </div>
                            </div>
                            <div class="m-form__group form-group">
                                <label for="">
                                    Communication:
                                </label>
                                <div class="m-checkbox-list">
                                    <label class="m-checkbox">
                                        <input type="checkbox">
                                        Email
                                        <span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="checkbox">
                                        SMS
                                        <span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="checkbox">
                                        Phone
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m-portlet__foot--fit">
                        <div class="m-form__actions m-form__actions">
                            <button type="reset" class="btn btn-primary">
                                Submit
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
     
        </div>
     
    </div>

</div>
@endsection