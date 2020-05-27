{{ Form::model($array,array('route' => 'Show.Multiples.Rates.por.detalles.Fcl', 'method' => 'post', 'id' => 'frrRates')) }}
@foreach($array as $rate)
<input type="hidden" name="idAr[]" value="{{$rate}}">
@endforeach
<input type="hidden" name="contract_id" value="{{$contract_id}}">
{!! Form::close() !!}