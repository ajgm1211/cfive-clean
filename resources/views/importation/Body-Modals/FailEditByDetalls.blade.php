{{ Form::model($array,array('route' => 'Show.Multiples.Rates.por.detalles.Fcl', 'method' => 'post', 'id' => 'frmSurcharges')) }}

<p>
	Seleccionaste {{$array_count}} Fail(s) Rate(s). Al terminar de resolverlos regresaremos nuevamente a esta pagina.
</p>
@foreach($array as $rate)

<input type="hidden" name="idAr[]" value="{{$rate}}">

@endforeach
<div class="m-portlet__foot m-portlet__foot--fit">
	<div class="m-form__actions m-form__actions">
		{!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
		<button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">Cancel</span>
		</button>
	</div>
</div>

{!! Form::close() !!}