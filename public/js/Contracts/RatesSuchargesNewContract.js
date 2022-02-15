    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });

    function showbox(id){
        $(".tdAB"+id).attr('hidden','hidden');
        $(".tdIn"+id).removeAttr('hidden');
    }

    function hidebox(id){
        $(".tdIn"+id).attr('hidden','hidden');
        $(".tdAB"+id).removeAttr('hidden');
    }

    function SaveCorrectRate(idtr,idcontract){
        //alert('tdIn'+idtr+' '+idrate);
        var idrate = $("#idf"+idtr).val();
        var origin = $("#origin"+idtr).val();
        var destination = $("#destination"+idtr).val();
        var carrier = $("#carrier"+idtr).val();
        var twuenty = $("#twuenty"+idtr).val();
        var forty = $("#forty"+idtr).val();
        var fortyhc = $("#fortyhc"+idtr).val();
        var currency = $("#currency"+idtr).val();
        var accion = $("#accion"+idtr).val();
        //alert('A.'+origin+' B.'+ destination+' C.'+ carrier+' D.'+ twuenty+' E.'+ forty+' F.'+fortyhc +' G.'+ currency);
        if(accion == 1){
            jQuery.ajax({
                method:'get',
                data:{rate_id:idrate,
                      contract_id:idcontract,
                      origin:origin,
                      destination:destination,
                      carrier:carrier,
                      twuenty:twuenty,
                      forty:forty,
                      fortyhc:fortyhc,
                      currency:currency,
                     },
                url:'/contracts/CorrectedRateForContracts',
                success:function(data){
                    //console.log(data);
                    if(data.response == 0){
                        //campo errado
                        swal("Error!", "wrong field in the rate!", "error");
                    }
                    else if(data.response == 1){
                        //exito
                        swal("Good job!", "Updated rate!", "success");
                        $(".icon"+idtr).attr('style','color:green');
                        $(".lb"+idtr).removeAttr('style');
                        hidebox(idtr);
                        var a = $('#strfailinput').val();
                        var b = $('#strgoodinput').val();
                        a--;
                        b++;
                        $('#strfail').text(a);
                        $('#strgood').text(b);
                        $('#strfailinput').attr('value',a);
                        $('#strgoodinput').attr('value',b);

                        $('#originlb'+idtr).text(data.origin);
                        $('#destinylb'+idtr).text(data.destiny);
                        $('#carrierlb'+idtr).text(data.carrier);
                        $('#twuentylb'+idtr).text(data.twuenty);
                        $('#fortylb'+idtr).text(data.forty);
                        $('#fortyhclb'+idtr).text(data.fortyhc);
                        $('#currencylb'+idtr).text(data.currency);
                        $('#idf'+idtr).attr('value',data.idrate);

                        $("#accion"+idtr).attr('value',2);

                    }
                    else if(data.response == 2){
                        //duplicado
                        swal("Error!", "Error Rate!", "warning");
                    }

                }
            });
        }
        else if( accion == 2){
            // para actualizar campos
            //alert('A.'+origin+' B.'+ destination+' C.'+ carrier+' D.'+ twuenty+' E.'+ forty+' F.'+fortyhc +' G.'+ currency+' .'+idrate+' ..'+idcontract);
            jQuery.ajax({
                method:'get',
                data:{
                    rate_id:idrate,
                    contract_id:idcontract,
                    origin:origin,
                    destination:destination,
                    carrier:carrier,
                    twuenty:twuenty,
                    forty:forty,
                    fortyhc:fortyhc,
                    currency:currency,
                },
                url:'/contracts/UpdateRatesForContracts',
                success:function(data){
                    //console.log(data);
                    if(data.response == 0){
                        //campo errado
                        swal("Error!", "wrong field in the rate!", "error");
                    }
                    else if(data.response == 1){
                        //exito
                        swal("Good job!", "Updated rate!", "success");

                        hidebox(idtr);

                        $('#originlb'+idtr).text(data.origin);
                        $('#destinylb'+idtr).text(data.destiny);
                        $('#carrierlb'+idtr).text(data.carrier);
                        $('#twuentylb'+idtr).text(data.twuenty);
                        $('#fortylb'+idtr).text(data.forty);
                        $('#fortyhclb'+idtr).text(data.fortyhc);
                        $('#currencylb'+idtr).text(data.currency);
                        $("#accion"+idtr).attr('value',2);

                    }
                    else if(data.response == 2){
                        //duplicado
                        swal("Error!", "Error Rate!", "warning");
                    }

                }
            });
        }
        //alert(idcontract);
    }

    function DestroyRate(idtr,idrate){
        var accion = $('#accion'+idtr).val();

        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.value) {


                jQuery.ajax({
                    method:'get',
                    data:{
                        rate_id:idrate,
                        accion:accion
                    },
                    url:'/contracts/DestroyRatesFailCorrectForContracts',
                    success:function(data){
                        if(data == 1){
                            swal("Good job!", "Deletion fail rate!", "success");
                            var a = $('#strfailinput').val();
                            a--;
                            $('#strfail').text(a);
                            $('#strfailinput').attr('value',a);

                        }
                        else if( data == 2){
                            swal("Good job!", "Deletion rate!", "success");
                            var b = $('#strgoodinput').val();
                            b--;
                            $('#strgoodinput').attr('value',b);
                            $('#strgood').text(b);
                        }
                        $('.tdBTU'+idtr).attr('hidden','hidden');
                        $('.icon'+idtr).attr('style','color:gray');

                        $('#originlb'+idtr).attr('style','color:red');
                        $('#destinylb'+idtr).attr('style','color:red');
                        $('#carrierlb'+idtr).attr('style','color:red');
                        $('#twuentylb'+idtr).attr('style','color:red');
                        $('#fortylb'+idtr).attr('style','color:red');
                        $('#fortyhclb'+idtr).attr('style','color:red');
                        $('#currencylb'+idtr).attr('style','color:red');
                        $('#trR'+idtr).attr('hidden','hidden');
                    }
                });
            }
        });
        /* idtr--;
        var myTable = $('#html_table');
        myTable.find( 'tbody tr:eq('+idtr+')' ).remove();
        alert(idtr+' '+accion);*/


    }
    
    function SaveCorrectSurcharge(idtr,idcontract){


        //alert('tdIn'+idtr+' '+idrate);
        var idSurcharge = $("#idfSur"+idtr).val(); //id que representa el registro en la base datos localcharge
        var surcharge = $("#surcharge"+idtr).val(); // id de los 'surchage list'
        var origin = $("#Surorigin"+idtr).val();
        var destination = $("#Surdestination"+idtr).val();
        var typedestiny = $("#typedestiny"+idtr).val();
        var calculationtype = $("#calculationtype"+idtr).val();
        var ammount = $("#ammount"+idtr).val();
        var currency = $("#Surcurrency"+idtr).val();
        var carrier = $("#Surcarrier"+idtr).val();
        var accion = $("#accionSur"+idtr).val();
        if(accion == 1){
            jQuery.ajax({
                method:'get',
                data:{
                    surcharge:surcharge,
                    idSurcharge:idSurcharge,
                    contract_id:idcontract,
                    origin:origin,
                    destination:destination,
                    typedestiny:typedestiny,
                    calculationtype:calculationtype,
                    ammount:ammount,
                    currency:currency,
                    carrier:carrier,
                },
                url:'/contracts/CorrectedSurchargeForContracts',
                success:function(data){
                    console.log(data);
                    if(data.response == 0){
                        //campo errado
                        swal("Error!", "wrong field in the Surcharge!", "error");
                    }
                    else if(data.response == 1){
                        //exito
                        swal("Good job!", "Updated Surcharge!", "success");
                        $(".icon"+idtr).attr('style','color:green');
                        $(".lb"+idtr).removeAttr('style');
                        hidebox(idtr);
                        var a = $('#strfailinputSur').val();
                        var b = $('#strgoodinputSur').val();
                        a--;
                        b++;
                        $('#strfailSur').text(a);
                        $('#strgoodSur').text(b);
                        $('#strfailinputSur').attr('value',a);
                        $('#strgoodinputSur').attr('value',b);

                        $('#surchargelb'+idtr).text(data.surchargeLB);
                        $('#Suroriginlb'+idtr).text(data.port_origLB);
                        $('#Surdestinylb'+idtr).text(data.port_destLB);
                        $('#typedestinylb'+idtr).text(data.typedestinyLB);
                        $('#calculationtypelb'+idtr).text(data.calculationtypeLB);
                        $('#ammountlb'+idtr).text(data.ammount);
                        $('#Surcurrencylb'+idtr).text(data.currencyLB);
                        $('#Surcarrierlb'+idtr).text(data.carrier);

                        $("#idfSur"+idtr).attr('value',data.surcharge_id);
                        $("#accionSur"+idtr).attr('value',2);

                    }
                    else if(data.response == 2){
                        //duplicado
                        swal("Error!", "Alrready Surcharge!", "warning");
                    }

                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }


            });
        }   
        else if( accion == 2){
            // para actualizar campos
            // alert('A.'+surcharge+' / '+origin+' B.'+ destination+' C.'+ typedestiny+' D.'+ calculationtype+' E.'+ ammount+' F.'+currency +' G.'+ carrier+' .'+idSurcharge);

            jQuery.ajax({
                method:'get',
                data:{
                    surcharge:surcharge,
                    idSurcharge:idSurcharge,
                    contract_id:idcontract,
                    origin:origin,
                    destination:destination,
                    typedestiny:typedestiny,
                    calculationtype:calculationtype,
                    ammount:ammount,
                    currency:currency,
                    carrier:carrier,
                },
                url:'/contracts/UpdateSurchargeForContracts',

                success:function(data){
                                    
                    console.log(data);
                   
                    if(data.response == 0){
                        //campo errado
                        swal("Error!", "wrong field in the Surcharge!", "error");
                    }
                    else if(data.response == 1){
                        //exito
                        swal("Good job!", "Updated Surcharge!", "success");

                        hidebox(idtr);

                        $('#surchargelb'+idtr).text(data.surchargeLB);
                        $('#Suroriginlb'+idtr).text(data.port_origLB);
                        $('#Surdestinylb'+idtr).text(data.port_destLB);
                        $('#typedestinylb'+idtr).text(data.typedestinyLB);
                        $('#calculationtypelb'+idtr).text(data.calculationtypeLB);
                        $('#ammountlb'+idtr).text(data.ammount);
                        $('#Surcurrencylb'+idtr).text(data.currencyLB);
                        $('#Surcarrierlb'+idtr).text(data.carrier);
                        $("#accionSur"+idtr).attr('value',2);

                    }

                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });
        }
        //alert(idcontract);
    }

    function DestroySurcharge(idtr){
        var idSurcharge = $("#idfSur"+idtr).val();
        var accion = $('#accionSur'+idtr).val();

        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.value) {


                jQuery.ajax({
                    method:'get',
                    data:{
                        surcharge_id:idSurcharge,
                        accion:accion
                    },
                    url:'/contracts/DestroySurchargeFailCorrectForContracts',
                    success:function(data){
                        if(data == 1){
                            swal("Good job!", "Deletion fail Surcharge!", "success");
                            var a = $('#strfailinputSur').val();
                            a--;
                            $('#strfailSur').text(a);
                            $('#strfailinputSur').attr('value',a);

                        }
                        else if( data == 2){
                            swal("Good job!", "Deletion Surcharge!", "success");
                            var b = $('#strgoodinputSur').val();
                            b--;
                            $('#strgoodinputSur').attr('value',b);
                            $('#strgoodSur').text(b);
                        }
                        //$('.tdBTU'+idtr).attr('hidden','hidden');
                        //$('.icon'+idtr).attr('style','color:gray');

                        $('#trRSur'+idtr).attr('hidden','hidden');
                        if( data == 3){
                            swal("Error!", "wrong field in the Surcharge!", "error");
                            $('#trRSur'+idtr).removeAttr('hidden','hidden');
                        }
                    }
                });
            }
        });
        /* idtr--;
        var myTable = $('#html_table');
        myTable.find( 'tbody tr:eq('+idtr+')' ).remove();
        alert(idtr+' '+accion);*/


    }

    function prueba(){
        idtr=1;
        var a = $("#origin"+idtr).val();
        //var ab = $("#origin"+idtr).val('value',12);
        var ac = $("#originlb"+idtr).attr('value',a);
        alert(a);

    }