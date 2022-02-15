function display_l(id) {

    $("#tr_l" + id + " .val").attr('hidden', 'true');
    $("#tr_l" + id + " .in").removeAttr('hidden');
    $("#tr_l" + id + " .in input , #tr_l" + id + " .in select ").prop('disabled', false);


    $("#save_l" + id).removeAttr('hidden');
    $("#cancel_l" + id).removeAttr('hidden');
    $("#remove_l" + id).removeAttr('hidden');
    $("#edit_l" + id).attr('hidden', 'true');

}

function cancel_l(id) {

    $("#tr_l" + id + " .val").removeAttr('hidden');
    $("#tr_l" + id + " .in").attr('hidden', 'true');
    $("#tr_l" + id + " .in input , #tr_l" + id + " .in select ").prop('disabled', true);

    $("#save_l" + id).attr('hidden', 'true');
    $("#cancel_l" + id).attr('hidden', 'true');
    $("#remove_l" + id).attr('hidden', 'true');
    $("#edit_l" + id).removeAttr('hidden');

}

function save_l(id, idval) {

    $.ajax({
        type: 'GET',
        url: 'globalcharges/updateGlobalCharge/' + idval,
        data: {
            'surcharge_id': $("#type" + id).val(),
            'port_orig': $("#port_orig" + id).val(),
            'port_dest': $("#port_dest" + id).val(),
            'changetype': $("#changetype" + id).val(),
            'carrier_id': $("#localcarrier" + id).val(),
            'calculationtype_id': $("#calculationtype" + id).val(),
            'ammount': $("#ammount" + id).val(),
            'currency_id': $("#localcurrency" + id).val()

        },
        success: function(data) {


            swal(
                'Updated!',
                'Your local charge has been updated.',
                'success'
            )
            $("#save_l" + id).attr('hidden', 'true');
            $("#cancel_l" + id).attr('hidden', 'true');
            $("#remove_l" + id).attr('hidden', 'true');
            $("#edit_l" + id).removeAttr('hidden');

            $("#tr_l" + id + " .val").removeAttr('hidden');
            $("#tr_l" + id + " .in").attr('hidden', 'true');
            $("#tr_l" + id + " .in input , #tr_l" + id + " .in select ").prop('disabled', true);
            var selText = "";
            var porText = "";
            var porTextDest = "";
            $("#localcarrier" + id + " option:selected").each(function() {
                var $this = $(this);
                if ($this.length) {
                    selText += $this.text() + ", ";

                }
            });
            $("#port_orig" + id + " option:selected").each(function() {
                var $this = $(this);
                if ($this.length) {
                    porText += $this.text() + ", ";

                }
            });
            $("#port_dest" + id + " option:selected").each(function() {
                var $this = $(this);
                if ($this.length) {
                    porTextDest += $this.text() + ", ";

                }
            });


            $("#divtype" + id).html($("#type" + id + " option:selected").text());
            $("#divport" + id).html(porText);
            $("#divportDest" + id).html(porTextDest);

            $("#divchangetype" + id).html($("#changetype" + id + " option:selected").text());
            $("#divcarrier" + id).html(selText);
            $("#divcalculation" + id).html($("#calculationtype" + id + " option:selected").text());
            $("#divammount" + id).html($("#ammount" + id).val());
            $("#divcurrency" + id).html($("#localcurrency" + id + " option:selected").text());

        },
        error: function(request, status, error) {
            alert(request.responseText);
        }

    });

}



$("#new").on("click", function() {


    $('#buttons').removeAttr('hidden');
    var $template = $('#globalclone');
    $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
    $myClone.addClass('closetr');
    $myClone.find("select").select2();

    $ids = $(".port_orig").length;
    $myClone.find(".port_orig").attr('name', 'port_orig' + $ids + '[]');
    $myClone.find(".port_dest").attr('name', 'port_dest' + $ids + '[]');
    $myClone.find(".carrier").attr('name', 'localcarrier' + $ids + '[]');
    $("#sample_editable_2").append($myClone);
    // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
    // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$(document).on('click', '.m_sweetalert_demo_8', function(e) {
    var res = $("i", this).attr('id');

    var theElement = $(this);
    var idval = res.substr(4);

    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.value) {

            $.ajax({
                type: 'get',
                url: 'globalcharges/deleteGlobalCharge/' + idval,
                success: function(data) {
                    swal(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                    $(theElement).closest('tr').remove();

                }
            });

        }

    });

});


$(document).on('click', '.remove', function() {
    $(this).closest('tr').remove();
    $i = 1;
    $('.closetr').each(function() {
        var res = $(".port_orig", this).removeAttr('name').attr('name', 'port_orig' + $i + '[]');
        var resDest = $(".port_dest", this).removeAttr('name').attr('name', 'port_dest' + $i + '[]');
        var car = $(".carrier", this).removeAttr('name').attr('name', 'localcarrier' + $i + '[]');
        $i++;
    });
});

$(document).on('click', '.cancel', function() {
    $('.closetr').closest('tr').remove();
    $('#buttons').attr('hidden', 'true');
});

function hola(hi) {

    alert(hi);
}



function activarCountryOld(act){
    var divCountry = $( ".divcountry");
    var divport = $( ".divport");
    var divportcountry = $( ".divportcountry");
    var divcountryport = $( ".divcountryport");



    var idPortOrig = $( "#port_orig"); 
    var idCountryOrig = $( "#country_orig");  


    var idPortDest = $( "#port_dest"); 
    var idCountryDest = $( "#country_dest"); 


    var portcountry_orig =  $( "#portcountry_orig") ;
    var portcountry_dest =   $( "#portcountry_dest") ;

    var countryport_orig=   $( "#countryport_orig"); 
    var countryport_dest=    $( "#countryport_dest"); 


    if(act == 'divcountry'){
        divport.attr('hidden','true');
        divportcountry.attr('hidden','true');
        divcountryport.attr('hidden','true');

        divCountry.removeAttr('hidden');

        idCountryOrig.attr('required','true');
        idCountryDest.attr('required','true');

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');



    }else if(act == 'divport'){
        divCountry.attr('hidden','true');
        divportcountry.attr('hidden','true');
        divcountryport.attr('hidden','true');

        divport.removeAttr('hidden');

        idPortOrig.attr('required','true');
        idPortDest.attr('required','true');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');


    }else if(act == 'divportcountry'){
        divCountry.attr('hidden','true');
        divport.attr('hidden','true');
        divcountryport.attr('hidden','true');
        // Activo
        divportcountry.removeAttr('hidden');
        // Required
        portcountry_orig.attr('required','true');
        portcountry_dest.attr('required','true');
        // No required 

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');

    }else if(act == 'divcountryport'){
        divCountry.attr('hidden','true');
        divport.attr('hidden','true');
        divportcountry.attr('hidden','true');
        // Activo
        divcountryport.removeAttr('hidden');
        // Required
        countryport_orig.attr('required','true');
        countryport_dest.attr('required','true');
        // No required 

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');


    }
}

function activarCountry(act, notchange = true) {
    var divCountry = $(".divcountry");
    var divport = $(".divport");
    var divportcountry = $(".divportcountry");
    var divcountryport = $(".divcountryport");



    var idPortOrig = $("#port_orig");
    var idCountryOrig = $("#country_orig");


    var idPortDest = $("#port_dest");
    var idCountryDest = $("#country_dest");


    var portcountry_orig = $("#portcountry_orig");
    var portcountry_dest = $("#portcountry_dest");

    var countryport_orig = $("#countryport_orig");
    var countryport_dest = $("#countryport_dest");

    if (notchange != true) {
        $("#exceptionPortOrig").select2().val('').trigger('change.select2');
        $("#exceptionPortDest").select2().val('').trigger('change.select2');
        $("#exceptionCountryOrig").select2().val('').trigger('change.select2');
        $("#exceptionCountryDest").select2().val('').trigger('change.select2');


        $('#allOriginPort').prop('checked', false);
        $('#allOriginCountry').prop('checked', false);
        $('#allOriginCountryPort').prop('checked', false);
        $('#allOriginPortCountry').prop('checked', false);

        $('#allDestinationPort').prop('checked', false);
        $('#allDestinationCountry').prop('checked', false);
        $('#allDestinationPortCountry').prop('checked', false);
        $('#allDestinationCountryPort').prop('checked', false);

        $("#port_orig").select2().val('').trigger('change.select2');
        $("#country_orig").select2().val('').trigger('change.select2');
        $("#countryport_orig").select2().val('').trigger('change.select2');
        $("#portcountry_orig").select2().val('').trigger('change.select2');

        $("#port_dest").select2().val('').trigger('change.select2');
        $("#country_dest").select2().val('').trigger('change.select2');
        $("#portcountry_dest").select2().val('').trigger('change.select2');
        $("#countryport_dest").select2().val('').trigger('change.select2');



    }

    if (act == 'divcountry') {
        divport.attr('hidden', 'true');
        divportcountry.attr('hidden', 'true');
        divcountryport.attr('hidden', 'true');

        divCountry.removeAttr('hidden');

        idCountryOrig.attr('required', 'true');
        idCountryDest.attr('required', 'true');

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');

        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('.excepcionCountryDest').attr('hidden', 'true');
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('.excepcionPortDest').attr('hidden', 'true');




    } else if (act == 'divport') {
        divCountry.attr('hidden', 'true');
        divportcountry.attr('hidden', 'true');
        divcountryport.attr('hidden', 'true');

        divport.removeAttr('hidden');

        idPortOrig.attr('required', 'true');
        idPortDest.attr('required', 'true');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');

        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('.excepcionCountryDest').attr('hidden', 'true');
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('.excepcionPortDest').attr('hidden', 'true');



    } else if (act == 'divportcountry') {
        divCountry.attr('hidden', 'true');
        divport.attr('hidden', 'true');
        divcountryport.attr('hidden', 'true');
        // Activo
        divportcountry.removeAttr('hidden');
        // Required
        portcountry_orig.attr('required', 'true');
        portcountry_dest.attr('required', 'true');
        // No required 

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');


        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('.excepcionCountryDest').attr('hidden', 'true');
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('.excepcionPortDest').attr('hidden', 'true');





    } else if (act == 'divcountryport') {
        divCountry.attr('hidden', 'true');
        divport.attr('hidden', 'true');
        divportcountry.attr('hidden', 'true');
        // Activo
        divcountryport.removeAttr('hidden');
        // Required
        countryport_orig.attr('required', 'true');
        countryport_dest.attr('required', 'true');
        // No required 

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');




        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('.excepcionCountryDest').attr('hidden', 'true');
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('.excepcionPortDest').attr('hidden', 'true');




    }



    // Exepciones 

    if (notchange == true) {

        $port_orig = $("#port_orig").val();
        if ($port_orig == '1485') {

            $('#allOriginPort').prop('checked', true);
            $('.excepcionPortOrig').removeAttr('hidden');

            $('#port_orig').attr('disabled', 'true');
            $("#port_orig").select2().val('1485').trigger('change.select2');
        }


        $country_orig = $("#country_orig").val();
        if ($country_orig == '250') {

            $('#allOriginCountry').prop('checked', true);
            $('.excepcionCountryOrig').removeAttr('hidden');
            $('#country_orig').attr('disabled', 'true');
            $("#country_orig").select2().val('250').trigger('change.select2');
        }

        $countryport_orig = $("#countryport_orig").val();
        if ($countryport_orig == '250') {

            $('#allOriginCountryPort').prop('checked', true);
            $('.excepcionCountryOrig').removeAttr('hidden');
            $('#countryport_orig').attr('disabled', 'true');
            $("#countryport_orig").select2().val('250').trigger('change.select2');

        }
        $portcountry_orig = $("#portcountry_orig").val();
        if ($portcountry_orig == '1485') {

            $('#allOriginPortCountry').prop('checked', true);
            $('.excepcionPortOrig').removeAttr('hidden');


            $('#portcountry_orig').attr('disabled', 'true');
            $("#portcountry_orig").select2().val('1485').trigger('change.select2');
        }




        // DESTINATION************************
        $port_dest = $("#port_dest").val();
        if ($port_dest == '1485') {

            $('#allDestinationPort').prop('checked', true);
            $('.excepcionPortDest').removeAttr('hidden');
            $('#port_dest').attr('disabled', 'true');
            $("#port_dest").select2().val('1485').trigger('change.select2');
        }
        $country_dest = $("#country_dest").val();
        if ($country_dest == '250') {

            $('#allDestinationCountry').prop('checked', true);
            $('.excepcionCountryDest').removeAttr('hidden');
            $('#country_dest').attr('disabled', 'true');
            $("#country_dest").select2().val('250').trigger('change.select2');

        }
        $portcountry_dest = $("#portcountry_dest").val();
        if ($portcountry_dest == '250') {
            $('.excepcionCountryDest').removeAttr('hidden');
            $('#portcountry_dest').attr('disabled', 'true');
            $("#portcountry_dest").select2().val('250').trigger('change.select2');
            $('#allDestinationPortCountry').prop('checked', true);
        }
        $countryport_dest = $("#countryport_dest").val();
        if ($countryport_dest == '1485') {

            $('#allDestinationCountryPort').prop('checked', true);
            $('.excepcionPortDest').removeAttr('hidden');
            $('#countryport_dest').attr('disabled', 'true');
            $("#countryport_dest").select2().val('1485').trigger('change.select2');
        }

    }


}

function activarCountry2(act, notchange = true) {
    var divCountry = $(".divcountry");
    var divport = $(".divport");
    var divportcountry = $(".divportcountry");
    var divcountryport = $(".divcountryport");



    var idPortOrig = $("#port_orig");
    var idCountryOrig = $("#country_orig");


    var idPortDest = $("#port_dest");
    var idCountryDest = $("#country_dest");


    var portcountry_orig = $("#portcountry_orig");
    var portcountry_dest = $("#portcountry_dest");

    var countryport_orig = $("#countryport_orig");
    var countryport_dest = $("#countryport_dest");

    if (notchange != true) {
        $("#exceptionPortOrig").select2().val('').trigger('change.select2');
        $("#exceptionPortDest").select2().val('').trigger('change.select2');
        $("#exceptionCountryOrig").select2().val('').trigger('change.select2');
        $("#exceptionCountryDest").select2().val('').trigger('change.select2');


        $('#allOriginPort').prop('checked', false);
        $('#allOriginCountry').prop('checked', false);
        $('#allOriginCountryPort').prop('checked', false);
        $('#allOriginPortCountry').prop('checked', false);

        $('#allDestinationPort').prop('checked', false);
        $('#allDestinationCountry').prop('checked', false);
        $('#allDestinationPortCountry').prop('checked', false);
        $('#allDestinationCountryPort').prop('checked', false);

        $("#port_orig").select2().val('').trigger('change.select2');
        $("#country_orig").select2().val('').trigger('change.select2');
        $("#countryport_orig").select2().val('').trigger('change.select2');
        $("#portcountry_orig").select2().val('').trigger('change.select2');

        $("#port_dest").select2().val('').trigger('change.select2');
        $("#country_dest").select2().val('').trigger('change.select2');
        $("#portcountry_dest").select2().val('').trigger('change.select2');
        $("#countryport_dest").select2().val('').trigger('change.select2');



    }

    if (act == 'divcountry') {
        divport.attr('hidden', 'true');
        divportcountry.attr('hidden', 'true');
        divcountryport.attr('hidden', 'true');

        divCountry.removeAttr('hidden');

        idCountryOrig.attr('required', 'true');
        idCountryDest.attr('required', 'true');

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');

        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('.excepcionCountryDest').attr('hidden', 'true');
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('.excepcionPortDest').attr('hidden', 'true');




    } else if (act == 'divport') {
        divCountry.attr('hidden', 'true');
        divportcountry.attr('hidden', 'true');
        divcountryport.attr('hidden', 'true');

        divport.removeAttr('hidden');

        idPortOrig.attr('required', 'true');
        idPortDest.attr('required', 'true');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');

        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('.excepcionCountryDest').attr('hidden', 'true');
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('.excepcionPortDest').attr('hidden', 'true');



    } else if (act == 'divportcountry') {
        divCountry.attr('hidden', 'true');
        divport.attr('hidden', 'true');
        divcountryport.attr('hidden', 'true');
        // Activo
        divportcountry.removeAttr('hidden');
        // Required
        portcountry_orig.attr('required', 'true');
        portcountry_dest.attr('required', 'true');
        // No required 

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        countryport_orig.removeAttr('required');
        countryport_dest.removeAttr('required');


        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('.excepcionCountryDest').attr('hidden', 'true');
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('.excepcionPortDest').attr('hidden', 'true');





    } else if (act == 'divcountryport') {
        divCountry.attr('hidden', 'true');
        divport.attr('hidden', 'true');
        divportcountry.attr('hidden', 'true');
        // Activo
        divcountryport.removeAttr('hidden');
        // Required
        countryport_orig.attr('required', 'true');
        countryport_dest.attr('required', 'true');
        // No required 

        idPortOrig.removeAttr('required');
        idPortDest.removeAttr('required');

        idCountryOrig.removeAttr('required');
        idCountryDest.removeAttr('required');

        portcountry_orig.removeAttr('required');
        portcountry_dest.removeAttr('required');




        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('.excepcionCountryDest').attr('hidden', 'true');
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('.excepcionPortDest').attr('hidden', 'true');




    }



    // Exepciones 

    if (notchange == true) {

        $port_orig = $("#port_orig").val();
        if ($port_orig == '1485') {

            $('#allOriginPort').prop('checked', true);
            $('.excepcionPortOrig').removeAttr('hidden');

            $('#port_orig').attr('disabled', 'true');
            $("#port_orig").select2().val('1485').trigger('change.select2');
        }


        $country_orig = $("#country_orig").val();
        if ($country_orig == '250') {

            $('#allOriginCountry').prop('checked', true);
            $('.excepcionCountryOrig').removeAttr('hidden');
            $('#country_orig').attr('disabled', 'true');
            $("#country_orig").select2().val('250').trigger('change.select2');
        }

        $countryport_orig = $("#countryport_orig").val();
        if ($countryport_orig == '250') {

            $('#allOriginCountryPort').prop('checked', true);
            $('.excepcionCountryOrig').removeAttr('hidden');
            $('#countryport_orig').attr('disabled', 'true');
            $("#countryport_orig").select2().val('250').trigger('change.select2');

        }
        $portcountry_orig = $("#portcountry_orig").val();
        if ($portcountry_orig == '1485') {

            $('#allOriginPortCountry').prop('checked', true);
            $('.excepcionPortOrig').removeAttr('hidden');


            $('#portcountry_orig').attr('disabled', 'true');
            $("#portcountry_orig").select2().val('1485').trigger('change.select2');
        }




        // DESTINATION************************
        $port_dest = $("#port_dest").val();
        if ($port_dest == '1485') {

            $('#allDestinationPort').prop('checked', true);
            $('.excepcionPortDest').removeAttr('hidden');
            $('#port_dest').attr('disabled', 'true');
            $("#port_dest").select2().val('1485').trigger('change.select2');
        }
        $country_dest = $("#country_dest").val();
        if ($country_dest == '250') {

            $('#allDestinationCountry').prop('checked', true);
            $('.excepcionCountryDest').removeAttr('hidden');
            $('#country_dest').attr('disabled', 'true');
            $("#country_dest").select2().val('250').trigger('change.select2');

        }
        $portcountry_dest = $("#portcountry_dest").val();
        if ($portcountry_dest == '250') {
            $('.excepcionCountryDest').removeAttr('hidden');
            $('#portcountry_dest').attr('disabled', 'true');
            $("#portcountry_dest").select2().val('250').trigger('change.select2');
            $('#allDestinationPortCountry').prop('checked', true);
        }
        $countryport_dest = $("#countryport_dest").val();
        if ($countryport_dest == '1485') {

            $('#allDestinationCountryPort').prop('checked', true);
            $('.excepcionPortDest').removeAttr('hidden');
            $('#countryport_dest').attr('disabled', 'true');
            $("#countryport_dest").select2().val('1485').trigger('change.select2');
        }

    }


}


$('.m-select2-general').select2({
    placeholder: "Select an option"
});

$(document).on('click', '.addS', function() {

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "0",
        "hideDuration": "0",
        "timeOut": "0",
        "extendedTimeOut": "0",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    var surcharge = $('.type');
    if (surcharge.val() == null) {
        toastr.error('You have to first add surcharges terms in order to add surcharges to this global. <a href="surcharges" > <b> Add Surcharge</b> </a>!', 'IMPORTANT MESSAGE!');
    }
});



function activarRegions(act) {

    var divPortOri = $('.divPortRgOri');
    var divCountryOri = $('.divCountryRgOri');
    var divPortDst = $('.divPortRgDst');
    var divCountryDst = $('.divCountryRgDst');
    $('.divPortRgOri').attr('hidden', 'true');
    $('.divCountryRgOri').attr('hidden', 'true');
    $('.divPortRgDst').attr('hidden', 'true');
    $('.divCountryRgDst').attr('hidden', 'true');

    if (act == 'divport') {
        divPortOri.removeAttr('hidden');
        divPortDst.removeAttr('hidden');
    } else if (act == 'divcountry') {
        $('.divCountryRgOri').removeAttr('hidden');
        $('.divCountryRgDst').removeAttr('hidden');
    } else if (act == 'divportcountry') {
        divPortOri.removeAttr('hidden');
        divCountryDst.removeAttr('hidden');
    } else if (act == 'divcountryport') {
        divCountryOri.removeAttr('hidden');
        divPortDst.removeAttr('hidden');
    }
}





$('#allOriginPort').on('click', function() {
    if ($(this).is(':checked')) {
        // Hacer algo si el checkbox ha sido seleccionado
        $('.excepcionPortOrig').removeAttr('hidden');

        $('#port_orig').attr('disabled', 'true');
        $("#port_orig").select2().val('1485').trigger('change.select2');

    } else {

        // Hacer algo si el checkbox ha sido deseleccionado
        $('.excepcionPortOrig').attr('hidden', 'true');
        $('#port_orig').removeAttr('disabled');
        $("#port_orig").select2().val('').trigger('change.select2');
        $('#exceptionPortOrig').val('').trigger('change.select2');
    }
});


$('#allDestinationPort').on('click', function() {
    if ($(this).is(':checked')) {
        // Hacer algo si el checkbox ha sido seleccionado
        $('.excepcionPortDest').removeAttr('hidden');

        $('#port_dest').attr('disabled', 'true');
        $("#port_dest").select2().val('1485').trigger('change.select2');

    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        $('.excepcionPortDest').attr('hidden', 'true');
        $('#port_dest').removeAttr('disabled');
        $("#port_dest").select2().val('').trigger('change.select2');
        $('#exceptionPortDest').val('').trigger('change.select2');

    }
});

$('#allOriginCountry').on('click', function() {
    if ($(this).is(':checked')) {
        // Hacer algo si el checkbox ha sido seleccionado
        $('.excepcionCountryOrig').removeAttr('hidden');
        $('#country_orig').attr('disabled', 'true');
        $("#country_orig").select2().val('250').trigger('change.select2');

    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        $('.excepcionCountryOrig').attr('hidden', 'true');
        $('#country_orig').removeAttr('disabled');
        $("#country_orig").select2().val('').trigger('change.select2');
        $('#exceptionCountryOrig').val('').trigger('change.select2');

    }
});

$('#allDestinationCountry').on('click', function() {
    if ($(this).is(':checked')) {
        // Hacer algo si el checkbox ha sido seleccionado
        $('.excepcionCountryDest').removeAttr('hidden');

        $('#country_dest').attr('disabled', 'true');
        $("#country_dest").select2().val('250').trigger('change.select2');
    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        $('.excepcionCountryDest').attr('hidden', 'true');

        $('#country_dest').removeAttr('disabled');
        $("#country_dest").select2().val('').trigger('change.select2');
        $('#exceptionCountryDest').val('').trigger('change.select2');
    }
});


$('#allOriginPortCountry').on('click', function() {
    if ($(this).is(':checked')) {
        // Hacer algo si el checkbox ha sido seleccionado
        $('.excepcionPortOrig').removeAttr('hidden');
        $('#portcountry_orig').attr('disabled', 'true');
        $("#portcountry_orig").select2().val('1485').trigger('change.select2');

    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        $('.excepcionPortOrig').attr('hidden', 'true');

        $('#portcountry_orig').removeAttr('disabled');
        $("#portcountry_orig").select2().val('').trigger('change.select2');
        $('#exceptionPortOrig').val('').trigger('change.select2');


    }
});

$('#allDestinationPortCountry').on('click', function() {
    if ($(this).is(':checked')) {
        // Hacer algo si el checkbox ha sido seleccionado
        $('.excepcionCountryDest').removeAttr('hidden');

        $('#portcountry_dest').attr('disabled', 'true');
        $("#portcountry_dest").select2().val('250').trigger('change.select2');
    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        $('.excepcionCountryDest').attr('hidden', 'true');

        $('#portcountry_dest').removeAttr('disabled');
        $("#portcountry_dest").select2().val('').trigger('change.select2');
        $('#exceptionCountryDest').val('').trigger('change.select2');
    }
});


$('#allOriginCountryPort').on('click', function() {
    if ($(this).is(':checked')) {
        // Hacer algo si el checkbox ha sido seleccionado
        $('.excepcionCountryOrig').removeAttr('hidden');

        $('#countryport_orig').attr('disabled', 'true');
        $("#countryport_orig").select2().val('250').trigger('change.select2');
    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        $('.excepcionCountryOrig').attr('hidden', 'true');

        $('#countryport_orig').removeAttr('disabled');
        $("#countryport_orig").select2().val('').trigger('change.select2');
        $('#exceptionCountryOrig').val('').trigger('change.select2');

    }
});
$('#allDestinationCountryPort').on('click', function() {
    if ($(this).is(':checked')) {
        // Hacer algo si el checkbox ha sido seleccionado
        $('.excepcionPortDest').removeAttr('hidden');

        $('#countryport_dest').attr('disabled', 'true');
        $("#countryport_dest").select2().val('1485').trigger('change.select2');
    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        $('.excepcionPortDest').attr('hidden', 'true');

        $('#countryport_dest').removeAttr('disabled');
        $("#countryport_dest").select2().val('').trigger('change.select2');
        $('#exceptionPortDest').val('').trigger('change.select2');

    }
});