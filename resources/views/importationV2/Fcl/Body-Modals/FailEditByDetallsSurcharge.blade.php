{{ Form::model($array,array('route' => 'Show.Multiples.Surc.por.detalles.Fcl', 'method' => 'post', 'id' => 'frrSurcharge')) }}
@foreach($array as $surcharge)
<input type="hidden" name="idAr[]" value="{{$surcharge}}">
@endforeach
<input type="hidden" name="contract_id" value="{{$contract_id}}">
{!! Form::close() !!}