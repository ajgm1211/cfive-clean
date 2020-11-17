
<style>
	.multiselect-selected-text {
		font-size: small;
		/*color: #9699a2;*/
	}

</style>
<link rel="stylesheet" href="/css/bootstrap-multiselect.css">
{!! Form::select('containers[]',$containers,$containersSelect,['class'=>'b-select form-control','id'=>'containerIDd','required','multiple'=>'multiple'])!!}

<script src="http://malsup.github.com/jquery.form.js"></script>
<script type="text/javascript">
	//$(document).ready(function() {
		$('.b-select').multiselect();
		//$('.multiselect-selected-text').text('Select an option',"title", "my new title" );
	//});

</script>

