
<div class="m-portlet">
    <!--begin::Form-->
    {{ Form::model($contract, array('route' => array('contract.duplicated.from.request.store', $contract->id), 'method' => 'post', 'id' => 'frmDpAC')) }}


    <div class="m-portlet__body">
        <h4 style="color:#031B4E">Duplicate to another company</h4>
        <div class="form-group m-form__group row"> 
            <div class="col-lg-6">
                {!! Form::label('reference', 'Reference') !!}
                {{ Form::text('reference',$contract->name,['id' => 'reference','required' => 'required','class'=>' form-control', 'style' => 'width:100%;']) }} 
            </div>
            <div class="col-lg-6">
                {!! Form::label('Carriers', 'Carriers') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('carrier_id[]', $carrier,$contract->carriers->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','required' => 'required','multiple' => 'multiple']) }}

                </div>

            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-6">
                {!! Form::label('validation_expire', 'Validation') !!}
                {!! Form::text('validation_expire', $contract->validity.' / '.$contract->expire, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input datePikc','id'=>'m_daterangepicker_1','required' => 'required']) !!}

            </div>
            <div class="col-lg-6">
                {!! Form::label('Direction', 'Direction') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('direction_id', $directions,$contract->direction_id,['id' => 'direction','required' => 'required','class'=>'m-select2-general form-control' ]) }}
                </div>
            </div>


        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-6">
                <strong>
                    {!! Form::label('Company', 'Company',['style'=>'color:#b90000']) !!}
                </strong>
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('company_user_id',$companyUsers,'',['id' => 'company_user','required' => 'required','class'=>'m-select2-general form-control' ]) }}
                </div>
            </div>
            <div class="col-lg-6">
                <strong>
                    {!! Form::label('Request', 'Request To Duplicate',['style'=>'color:#b90000']) !!}
                </strong>
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('request_id',[],null,['id' => 'js-data-example-ajax','required' => 'required', 'onchange'=>'changeForm()','class'=>'js-data-example-ajax form-control' ]) }}
                </div>
            </div>
        </div>

        <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions m-form__actions">
                <br>
                {!! Form::button('Update', ['class'=> 'btn btn-primary submitForm']) !!}
                <button class="btn btn-success" type="button" class="close" data-dismiss="modal" oninput="" aria-label="Close">
                    <span aria-hidden="true">Cancel</span>
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>  
</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>

<!--end::Form-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script>


    function changeForm(){
        var id = $('#js-data-example-ajax').val();
        url='{!! route("get.request.fcl",":id") !!}';
        url = url.replace(':id', id);

        $.ajax({
            url:url,
            method:'get',
            success: function(response){
                if(response.success == true){
                    console.log(response);
                    $('#reference').val(response.data.namecontract);
                    var carriers = response.carriers;
                    $('#localcarrier').val(carriers).trigger('change');
                    $('#company_user').val(response.data.company_user_id).trigger('change');
                    $('#direction').val(response.data.direction_id).trigger('change');
                    var date =response.data.validation;
                    date = date.split(' / ');
                    $(".datePikc").data('daterangepicker').setStartDate(date[0]);
                    $(".datePikc").data('daterangepicker').setEndDate(date[1]);

                }else {
                    toastr.error('Error');
                }
                ///console.log(response);
            }
        });
    }

    $('.js-data-example-ajax').select2({
        ajax: {
            require:true,
            url: '{{route("select.request.fcl.dp")}}',
            dataType: 'json',
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            processResults: function (data) {
                //  console.log(data);
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.id+' - '+item.namecontract,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
        }
    });




    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });

    $('.submitForm').click(function(){
        var company_name = $('#company_user option:selected').text();
        //alert(company_name);
        swal({

            title: 'Are you sure? '+company_name,
            text: "You want to double this contract to this company: "+company_name,
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Duplicate it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then(function(result){
            if (result.value) {
                $('#frmDpAC').submit();
            } else if (result.dismiss === 'cancel') {
                /*swal(
                    'Cancelled',
                    'your action was canceled :)',
                    'error'
                )*/
            }
        });
    });
</script>
