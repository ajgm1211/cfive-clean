function display_l(id){

    $("#tr_l"+id+" .val").attr('hidden','true');
    $("#tr_l"+id+" .in").removeAttr('hidden');
    $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', false);


    $("#save_l"+id).removeAttr('hidden');
    $("#cancel_l"+id).removeAttr('hidden');
    $("#remove_l"+id).removeAttr('hidden');
    $("#edit_l"+id).attr('hidden','true');

}

function cancel_l(id){

    $("#tr_l"+id+" .val").removeAttr('hidden');
    $("#tr_l"+id+" .in").attr('hidden','true');
    $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', true);

    $("#save_l"+id).attr('hidden','true');
    $("#cancel_l"+id).attr('hidden','true');
    $("#remove_l"+id).attr('hidden','true');
    $("#edit_l"+id).removeAttr('hidden');

}

function save_l(id,idval){

    $.ajax({
        type: 'GET',
        url: 'globalcharges/updateGlobalCharge/' + idval,
        data: {
            'surcharge_id' : $("#type"+id).val(),
            'port_orig' : $("#port_orig"+id).val(),
            'port_dest' : $("#port_dest"+id).val(),
            'changetype' : $("#changetype"+id).val(),
            'carrier_id' : $("#localcarrier"+id).val(),
            'calculationtype_id' : $("#calculationtype"+id).val(),
            'ammount' : $("#ammount"+id).val(),
            'currency_id' : $("#localcurrency"+id).val()

        },
        success: function(data) {


            swal(
                'Updated!',
                'Your local charge has been updated.',
                'success'
            )
            $("#save_l"+id).attr('hidden','true');
            $("#cancel_l"+id).attr('hidden','true');
            $("#remove_l"+id).attr('hidden','true');
            $("#edit_l"+id).removeAttr('hidden');

            $("#tr_l"+id+" .val").removeAttr('hidden');
            $("#tr_l"+id+" .in").attr('hidden','true');
            $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', true);
            var selText ="";
            var porText = "";
            var porTextDest = "";
            $("#localcarrier"+id+" option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    selText += $this.text()+ ", ";

                }
            });
            $("#port_orig"+id+" option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    porText += $this.text()+ ", ";

                }
            });
            $("#port_dest"+id+" option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    porTextDest += $this.text()+ ", ";

                }
            });


            $("#divtype"+id).html($("#type"+id+" option:selected").text());
            $("#divport"+id).html(porText);
            $("#divportDest"+id).html(porTextDest);

            $("#divchangetype"+id).html($("#changetype"+id+" option:selected").text());
            $("#divcarrier"+id).html(selText);
            $("#divcalculation"+id).html($("#calculationtype"+id+" option:selected").text());
            $("#divammount"+id).html($("#ammount"+id).val());
            $("#divcurrency"+id).html($("#localcurrency"+id+" option:selected").text());

        },
        error: function (request, status, error) {
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

    $ids = $( ".port_orig" ).length;
    $myClone.find(".port_orig").attr('name', 'port_orig'+$ids+'[]');
    $myClone.find(".port_dest").attr('name', 'port_dest'+$ids+'[]');
    $myClone.find(".carrier").attr('name', 'localcarrier'+$ids+'[]');
    $("#sample_editable_2").append($myClone);
    // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
    // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$(document).on('click', '.m_sweetalert_demo_8', function (e) {
    var res = $("i",this).attr('id'); 

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


$(document).on('click', '.remove', function () {
    $(this).closest('tr').remove();
    $i = 1;
    $('.closetr').each(function () {
        var res = $(".port_orig",this).removeAttr('name').attr('name', 'port_orig'+$i+'[]');
        var resDest = $(".port_dest",this).removeAttr('name').attr('name', 'port_dest'+$i+'[]');
        var car = $(".carrier",this).removeAttr('name').attr('name', 'localcarrier'+$i+'[]');
        $i++;
    });
});

$(document).on('click', '.cancel', function () {
    $('.closetr').closest('tr').remove();
    $('#buttons').attr('hidden','true');
});

function hola(hi){

    alert(hi);
}

function activarCountry(act){
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


$('.m-select2-general').select2({
    placeholder: "Select an option"
});

$(document).on('click', '.addS', function () {

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
    if(surcharge.val() == null){
        toastr.error('You have to first add surcharges terms in order to add surcharges to this global. <a href="surcharges" > <b> Add Surcharge</b> </a>!','IMPORTANT MESSAGE!');
    }
} );



function activarRegions(act){

    var divPortOri    = $('.divPortRgOri');
    var divCountryOri = $('.divCountryRgOri');
    var divPortDst    = $('.divPortRgDst');
    var divCountryDst = $('.divCountryRgDst');
    $('.divPortRgOri').attr('hidden','true');
    $('.divCountryRgOri').attr('hidden','true');
    $('.divPortRgDst').attr('hidden','true');
    $('.divCountryRgDst').attr('hidden','true');
    
    if(act == 'divport'){
        divPortOri.removeAttr('hidden');
        divPortDst.removeAttr('hidden');
    }else if(act == 'divcountry'){
        $('.divCountryRgOri').removeAttr('hidden');
        $('.divCountryRgDst').removeAttr('hidden');
    }else if(act == 'divportcountry'){
        divPortOri.removeAttr('hidden');
        divCountryDst.removeAttr('hidden');
    }else if(act == 'divcountryport'){
        divCountryOri.removeAttr('hidden');
        divPortDst.removeAttr('hidden');
    }
}



