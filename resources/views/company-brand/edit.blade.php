@extends('layouts.app')
@section('title', 'Settings')
@section('content')
    <div class="m-content">
        <div class="row">
            <div class="col-md-4">
                <div class="m-portlet m-portlet--mobile">
                    <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        Company's Profile
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                        
                            
                            {!! Form::model($company, ['route' => ['preferences.update', $company], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
                                
                                    <div class="row text-left" style="font-size: 12px !important;">
                                        <div class="col-md-12">
                                            <div class="form-group m-form__group ">
                                                <label for="name">Name</label>
                                                <input type="hidden" value="{{$company->id}}" id="company_id" name="company_id" class="form-control"/>
                                                <input type="text" value="{{$company->name}}" id="name" name="name" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group m-form__group text-left">
                                                <label for="phone">Phone</label>
                                                <input type="text" value="{{$company->phone}}" id="phone" name="phone" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group m-form__group">
                                                <label for="address">Address</label>
                                                <textarea class="form-control" name="address" cols="4">{{$company->address}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group m-form__group">
                                                <label for="currency_id">Currency</label>
                                                {{ Form::select('currency_id',$currencies,$company->currency_id,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'currency_id']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group m-form__group">
                                                <label for="currency_id">Logo</label>
                                                <input type="file" class="form-control-file" name="image">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group m-form__group">
                                                <button type="submit" id="default-currency-submit" class="btn btn-primary btn-block">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                
                                {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @parent
    <script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
    <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
    <script>
        $('#currency_id').select2({
            placeholder: "Select an option"
        });
    </script>
@stop