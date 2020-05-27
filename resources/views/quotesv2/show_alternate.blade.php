@extends('layouts.app')
@section('css')
@parent
<link href="/css/quote.css" rel="stylesheet" type="text/css" />

<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
<style>
.project-tab {
    //
}
.project-tab #tabs{
    background: #007b5e;
    //color: #eee;
}
.project-tab #tabs h6.section-title{
    //color: #eee;
}
.project-tab #tabs .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    color: #0062cc;
    background-color: transparent;
    border-color: transparent transparent #f3f3f3;
    border-bottom: 3px solid !important;
    font-size: 14px;
    font-weight: bold;
}
.project-tab .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
    color: #0062cc;
    font-size: 14px;
    font-weight: 600;
}
.project-tab .nav-link:hover {
    border: none;
}
.project-tab thead{
    background: #f3f3f3;
    color: #333;
}
.project-tab a{
    text-decoration: none;
    //color: #333;
    font-weight: 600;
}
    .pac-container {
        z-index: 10000 !important;
    }
    .bg-quotes {
        background-color: #e3e8ee !important;
    }
    .no-border {
        border: none;
        box-shadow: none;
        background-color: #fff !important;
    }
    .btn-edit {
        border-color: #eee !import;
    }
    .span_draft {
        background-color: #d6ebff !important;
        color: #066efa !important;
    }
    .padding-portlet { 
        padding: 10px 0 !important;
    }
    .padding-portlet .flex-list li {
        margin: 0 !important;
        border-left: 1px solid #eee;
        display: flex;
        align-items: center;
        padding: 0 20px !important;
    }
    .no-border-left {
        border-left: none !important;
    }
    .no-border-left i {
        font-size: 35px;
    }
    .header-charges {
        padding: 13px 30px;
        background-color: #f6fafc;
        height: 40px !important;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }
    .thead {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .no-mg-row {
        margin: 0 !important;
    }
    .header-table {
        background-color: #f5f5f9 !important;
        line-height: 0 !important;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }
    .td-table {
        line-height: 30px;
        text-align: left !important;
        margin-left: 50px;
    }
    td {
        border: none !important;
    }
    td a {
        border-bottom: none !important;
    }
    .td-a {
        border: 1px solid #eee !important;
        border-radius: 5px;
        padding: 6px;
    }
    .tds {
        line-height: 40px;
        text-align: left;
    }
    .btn-edit {
        /*height: 25px;
        font-size: 12px;
        line-height: 15px;
        padding: 5px 10px;*/
    }
    .portlet-padding {
        padding: 10px 2.2rem 2.2rem 2.2rem !important;
    }
    .display-none {
        display: none;
    }
    .btn-primary-v2 {
        background-color: transparent !important;
    }
    .quote-info-mb {
        margin-bottom: 5px;
    }
    .btn-open span {
        font-size: 35px;
    }
    .btn-backto {
        font-size: 16px;
    }
    .select_forms {
        background-color: transparent;
        margin-left: 5px;
        bottom: 2px;
        position: relative;
    }
    .input_form {
        line-height: 28px;
    }
    .input_form:before {
        content: '';
        color: #fff;
        background-color: transparent;
        width: 15px;
        height: 15px;
        border: 1px solid #066efa;
        border-radius: 2px;
        position: absolute;
        top: 5px;
        left: 0;
        text-align: center;
        z-index: 1;
        line-height: 16px;
    }
    .check {
        position: relative;
        z-index: 5;
        opacity: 0;
        margin-right: 5px;
    }
    .check[type="checkbox"]:checked + .input_form:before {
        content: '✔';
        background-color: #066efa;
        color: #fff;
    }
    .form-check {
        display: flex;
        align-items: center;
    }
    /*estilos*/

</style>
@endsection
@section('title', 'Quotes | Details')
@section('content')
<div class="m-content bg-quotes" style="min-height: 650px;">
    @if(Session::has('message.nivel'))
    <div class="col-md-12">
        <br>
        <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show" role="alert">
            <div class="m-alert__icon">
                <i class="la la-warning"></i>
            </div>
            <div class="m-alert__text">
                <strong>
                    {{ session('message.title') }}
                </strong>
                {{ session('message.content') }}
            </div>
            <div class="m-alert__close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <section id="tabs" class="project-tab">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Basic information</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Rate information</a>
                            <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Settings</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent" style="padding-top: 2%">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            @include('quotesv2.partials.show_head')
                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary-v2 btn-edit pull-right" data-toggle="modal" data-target="#createRateModal">
                                        Add rate &nbsp;&nbsp;<i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <br>
                            <!-- Charges -->
                            @if($quote->type=='FCL')
                                @include('quotesv2.partials.ratesByContainer')
                            @else
                                @include('quotesv2.partials.ratesByPackage')
                            @endif
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary-v2 btn-edit pull-right" data-toggle="modal" data-target="#createSaleTermModal">
                                        Add Sale Term &nbsp;&nbsp;<i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <br>
                            <!-- Payments and terms conditions -->
                            @include('quotesv2.partials.payments')
                        
                            <!-- Terms and conditions -->
                            @include('quotesv2.partials.terms')
                        </div>
                        <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                            @include('quotesv2.partials.saleTerms')
                            <!-- PDF Layout -->
                            @include('quotesv2.partials.pdf_layout')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    




</div>
@include('quotesv2.partials.sendQuoteModal')
@include('quotesv2.partials.createRateModal')
@include('quotesv2.partials.createInlandModal')
@include('quotesv2.partials.editRateModal')
@include('quotesv2.partials.createSaleTermModal')
@include('quotesv2.partials.editSaleTermModal')
@endsection

@section('js')
@parent
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="{{asset('js/quotes-v2.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
<script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete" async defer></script>
<script type="text/javascript">

    /*** GOOGLE MAPS API ***/

    var autocomplete;
    function initAutocomplete() {
        var geocoder = new google.maps.Geocoder();
        var autocomplete = new google.maps.places.Autocomplete((document.getElementById('origin_address')));
        var autocomplete_destination = new google.maps.places.Autocomplete((document.getElementById('destination_address')));
        //autocomplete.addListener('place_changed', fillInAddress);
    }

    function codeAddress(address) {
        var geocoder;
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == 'OK') {
                alert(results[0].geometry.location);
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    }

    var editor_config = {
        path_absolute : "/",
        selector: "textarea.editor",
        plugins: ["template"],
        toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
        external_plugins: { "nanospell": "{{asset('js/tinymce/plugins/nanospell/plugin.js')}}" },
        nanospell_server:"php",
        browser_spellcheck: true,
        relative_urls: false,
        remove_script_host: false,
        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
            if (type == 'image') {
                cmsURL = cmsURL + "&type=Images";
            } else {
                cmsURL = cmsURL + "&type=Files";
            }

            tinymce.activeEditor.windowManager.open({
                file: '<?= route('elfinder.tinymce4') ?>',// use an absolute path!
                title: 'File manager',
                width: 900,
                height: 450,
                resizable: 'yes'
            }, {
                setUrl: function (url) {
                    win.document.getElementById(field_name).value = url;
                }
            });
        }
    };

    tinymce.init(editor_config);

    $('.btn-open').on('click', function(){
        var element = $(this).attr('id');

        if(element == 'pay')
        {
            $('.pay').toggle();
        }
        if(element == 'terms')
        {
            $('.terms').toggle();
        }
        if(element == 'remks')
        {
            $('.remks').toggle();
        }

    });

    function AbrirModal(action,id){
        if(action == "edit"){
            var url = '{{ route("quotes-v2.rates.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-rate').load(url,function(){
                $('#editRateModal').modal({show:true});
            });
        }else if(action == "editInland"){
            var url = '{{ route("quotes-v2.inlands.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-rate').load(url,function(){
                $('#editRateModal').modal({show:true});
            });
        }else if(action == "editInlandLcl"){
            var url = '{{ route("quotes-v2.inlands.lcl.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-rate').load(url,function(){
                $('#editRateModal').modal({show:true});
            });
        }else if(action == "editSaleTerm"){
            var url = '{{ route("quotes-v2.saleterm.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-rate').load(url,function(){
                $('#editSaleTermModal').modal({show:true});
            });
        }
    }

    if(history.forward(1)){
        history.replace(history.forward(1));
    }

</script>
@stop