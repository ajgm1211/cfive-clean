
<script type="text/javascript">
    $(document).ready(function() {
        
        $('#containerID').multiselect({
            onInitialized: function(select, container) {
                // alert('Initialized.');
            }
        });
    });
</script>
{!! Form::select('containers[]',$containers,null,['class'=>' form-control multiselect','id'=>'containerID','inpName' => 'Equipments','required','multiple'=>'multiple'])!!}

