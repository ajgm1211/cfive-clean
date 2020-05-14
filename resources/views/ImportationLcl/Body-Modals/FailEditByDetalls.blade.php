{{ Form::model($array,array('route' => 'Show.Multiples.Rates.por.detalles.Lcl', 'method' => 'post', 'id' => 'frrRates')) }}
@foreach($array as $rate)
<input type="hidden" name="idAr[]" value="{{$rate}}">
@endforeach
<input type="hidden" name="contractlcl_id" value="{{$contractlcl_id}}">
{!! Form::close() !!}