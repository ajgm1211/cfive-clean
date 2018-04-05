
function AbrirModal(id){

    if(id != "0"){
        var url = '{{ route("users.edit", ":id") }}';
        url = url.replace(':id', id);
        $('.modal-body').val = 'TEst';

        $('.modal-body').load(url,function(){
            $('#m_modal_5').modal({show:true});
        });
    }else{
        var url = '{{ route("users.add") }}';


        $('.modal-body').load(url,function(){
            $('#m_modal_5').modal({show:true});
        });

    }

}
</script>