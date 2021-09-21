@extends('layouts.app')

@section('content')
    <div id="main"></div>
@endsection

@section('js')
    <script src="{{ asset('js/main/main.js') }}" type="text/javascript"></script>
@parent
@endsection