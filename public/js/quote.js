
$('.m-select2-general').select2({
    placeholder: "Select an option"
});


function display(id){
    var elemento = $("#detail"+id);

    if(elemento.attr('hidden')){

        $("#detail"+id).removeAttr('hidden');

    }else{
        $("#detail"+id).attr('hidden','true');
    }
}




