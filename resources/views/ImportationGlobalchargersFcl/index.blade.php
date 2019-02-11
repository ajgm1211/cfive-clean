@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'GlobalCharge')
@section('content')

<div class="m-content">

   @if(Session::has('message.nivel'))

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
   @endif

   <!--Begin::Main Portlet-->
   <div class="m-portlet m-portlet--full-height">
      <!--begin: Portlet Head-->
      <div class="m-portlet__head">
         <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
               <h3 class="m-portlet__head-text">
                  Importation Globalchargers 
               </h3>
            </div>
         </div>

      </div>
      {!! Form::open(['route'=>'Upload.File.New.Contracts','method'=>'PUT','files'=>true, 'id' => 'formupload'])!!}
      <div class="m-portlet__body">
         <div class="tab-content">
            <div class="tab-pane active" id="m_portlet_tab_1_1">
               <div class="row">
                  <div class="col-lg-12">

                     <div class="form-group m-form__group row">

                        <div class="col-lg-2">
                           <label class="col-form-labe"><b>CONTRACT:</b></label>
                        </div>
                        <div class="col-lg-3">
                           <label for="numberid" class=" ">Company User</label>
                           {!!  Form::select('CompanyUserId',$companysUser,null,['id'=>'CompanyUserId',
                           'required',
                           'class'=>'form-control m-input','onchange' => 'selectvalidate()'])!!}
                        </div>
                        <div class="col-lg-3">
                           <label for="nameid" class="">Importation Name</label>
                           {!!  Form::text('name',null,['id'=>'nameid',
                           'placeholder'=>'Contract Name',
                           'required',
                           'class'=>'form-control m-input'])!!}
                        </div>
                        <div class="col-lg-3">
                           <label for="numberid" class=" ">Date Importation</label>
                           {!!  Form::date('number',\Carbon\Carbon::now(),['id'=>'dateid',
                           'placeholder'=>'Number Contract',
                           'required',
                           'class'=>'form-control m-input'])!!}
                        </div>

                     </div>

                     <hr>
                     <!-- <div class="form-group m-form__group row">


<div class="col-3">
<label class="m-option">
<span class="m-option__control">
<span class="m-radio m-radio--brand m-radio--check-bold">
<input name="type" value="1" id="rdRate" type="radio" checked>
<span></span>
</span>
</span>
<span class="m-option__label">
<span class="m-option__head">
<span class="m-option__title">
Rates
</span>
</span>
</span>
</label>
</div>

<div class="col-3">
<label class="m-option">
<span class="m-option__control">
<span class="m-radio m-radio--brand m-radio--check-bold">
<input name="type" value="2" id="rdRateSurcharge" type="radio" >
<span></span>
</span>
</span>
<span class="m-option__label">
<span class="m-option__head">
<span class="m-option__title">
Rates &nbsp; + &nbsp; Surcharges
</span>
</span>
</span>
</label>
</div>

</div>-->
                     <div class="form-group m-form__group row"  id="divvaluescurren">
                        <div class="col-lg-2">
                           <label class="col-form-label"><b>TYPE:</b></label>
                        </div>

                        <div class="col-3">
                           <label class="m-option">
                              <span class="m-option__control">
                                 <span class="m-radio m-radio--brand m-radio--check-bold">
                                    <input name="valuesCurrency" value="1"  type="radio" >
                                    <span></span>
                                 </span>
                              </span>
                              <span class="m-option__label">
                                 <span class="m-option__head">
                                    <span class="m-option__title">
                                       Values Only
                                    </span>
                                 </span>
                              </span>
                           </label>
                        </div>
                        <div class="col-3">
                           <label class="m-option">
                              <span class="m-option__control">
                                 <span class="m-radio m-radio--brand m-radio--check-bold">
                                    <input name="valuesCurrency" value="2"  type="radio" checked>
                                    <span></span>
                                 </span>
                              </span>
                              <span class="m-option__label">
                                 <span class="m-option__head">
                                    <span class="m-option__title">
                                       Values With Currency
                                    </span>
                                 </span>
                              </span>
                           </label>
                        </div>
                     </div>
                     <hr>
                     <div class="form-group m-form__group row">

                        <div class="col-lg-2">
                           <label class="col-form-label"><b>DATA:</b></label>
                        </div>


                        <div class="col-3">
                           <label class="m-option">
                              <span class="m-option__control">
                                 <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                    <input name="DatOri" id="originchk" type="checkbox">
                                    <span></span>
                                 </span>
                              </span>
                              <span class="m-option__label">
                                 <span class="m-option__head">
                                    <span class="m-option__title">
                                       Origin Port Not Included
                                    </span>
                                 </span>
                              </span>
                           </label>
                           <div class="col-form-label" id="origininp" hidden="hidden" >
                              {!! Form::select('origin[]',$harbor,null,['class'=>'m-select2-general form-control  ','id'=>'origin','multiple'=>'multiple'])!!}
                           </div>
                        </div>

                        <div class="col-3">
                           <label class="m-option">
                              <span class="m-option__control">
                                 <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                    <input name="DatDes" id="destinychk" type="checkbox">
                                    <span></span>
                                 </span>
                              </span>
                              <span class="m-option__label">
                                 <span class="m-option__head">
                                    <span class="m-option__title">
                                       Destiny Port Not Included
                                    </span>
                                 </span>
                              </span>
                           </label>
                           <div class="col-form-label" id="destinyinp" hidden="hidden" >
                              {!! Form::select('destiny[]',$harbor,null,['class'=>'m-select2-general form-control  ','id'=>'destiny','multiple'=>'multiple'])!!}
                           </div>
                        </div>
                        <div class="col-3">
                           <label class="m-option">
                              <span class="m-option__control">
                                 <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                    <input name="DatCar" id="carrierchk" type="checkbox">
                                    <span></span>
                                 </span>
                              </span>
                              <span class="m-option__label">
                                 <span class="m-option__head">
                                    <span class="m-option__title">
                                       Carrier Not Included
                                    </span>
                                 </span>
                              </span>
                           </label>
                           <div class="col-form-label" hidden="hidden" id="carrierinp">
                              {!! Form::select('carrier',$carrier,null,['class'=>'m-select2-general form-control','id'=>'carrier'])!!}
                           </div>
                        </div>
                     </div>
                     <div class="form-group m-form__group row">
                        <div class="col-lg-2"></div>

                        <div class="col-3">
                           <label class="m-option">
                              <span class="m-option__control">
                                 <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                    <input name="Datftynor" id="fortynorchk" type="checkbox">
                                    <span></span>
                                 </span>
                              </span>
                              <span class="m-option__label">
                                 <span class="m-option__head">
                                    <span class="m-option__title">
                                       Includes 40'NOR Column
                                    </span>
                                 </span>
                              </span>
                           </label>
                        </div>

                        <div class="col-3">
                           <label class="m-option">
                              <span class="m-option__control">
                                 <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                    <input name="Datftyfive" id="fortyfivechk" type="checkbox">
                                    <span></span>
                                 </span>
                              </span>
                              <span class="m-option__label">
                                 <span class="m-option__head">
                                    <span class="m-option__title">
                                       Includes 45 Column
                                    </span>
                                 </span>
                              </span>
                           </label>
                        </div>
                        <div class="col-3" id="divtyped">
                           <label class="m-option">
                              <span class="m-option__control">
                                 <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                    <input name="DatTypeDes" id="typedestinychk" type="checkbox">
                                    <span></span>
                                 </span>
                              </span>
                              <span class="m-option__label">
                                 <span class="m-option__head">
                                    <span class="m-option__title">
                                       Type Destiny Not Included
                                    </span>
                                 </span>
                              </span>
                           </label>
                           <div class="col-form-label" hidden="hidden" id="typedestinyinp">
                              {!! Form::select('typedestiny',$typedestiny,null,['class'=>'m-select2-general form-control','id'=>'typedestiny'])!!}
                           </div>
                        </div>
                     </div>
                     <div class="form-group m-form__group row">

                     </div>
                     <br>
                     <div class="form-group m-form__group row">
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-6">
                           <input type="file" name="file" required>
                        </div>
                     </div>
                     <br>
                     <br>
                     <div class="form-group m-form__group row">
                        <div class="col-lg-12 col-lg-offset-12" id="scrollToHere">
                           <center>
                              <button type="submit" id="loadbutton" class="btn btn-success col-2 form-control">
                                 Load
                              </button>

                              <a href="#" id="validatebutton" onclick="validar()" class="btn btn-primary col-2 form-control"> Validate</a>
                           </center>
                        </div>
                     </div>

                  </div>

               </div>
            </div>
         </div>
      </div>
      {!! Form::close()!!}
      <!--end: Form Wizard-->
   </div>
   <!--End::Main Portlet-->



</div>

@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="{{asset('js/Globalchargers/ImporttationGlobalchargersFcl.js')}}"></script>

<script>
   $(document).ready(function(){
      $('#loadbutton').hide();
   });

   function selectvalidate(){
      var id = $('#CompanyUserId').val();
      //alert(id);
      $('#validatebutton').show();
      $('#loadbutton').hide();
   }

   function validar(){
      var id = $('#CompanyUserId').val();

      url='{!! route("validate.import",":id") !!}';
      url = url.replace(':id', id);
      // $(this).closest('tr').remove();
      $.ajax({
         url:url,
         method:'get',
         success: function(data){
            swal({
               title: 'Are you sure?',
               text: "Selected company: "+data.name,
               type: 'warning',
               showCancelButton: true,
               confirmButtonText: 'Yes, select it!',
               cancelButtonText: 'No, cancel!',
               reverseButtons: true
            }).then(function(result){
               if (result.value) {

                  $('#validatebutton').hide();
                  $('#loadbutton').show();

                  $('html,body').animate({
                     scrollTop: $("#scrollToHere").offset().top
                  }, 2000);

               } else if (result.dismiss === 'cancel') {
                  swal(
                     'Cancelled',
                     'You can validate again :)',
                     'error'
                  )
               }
            });
         }
      });






   }

</script>

@stop