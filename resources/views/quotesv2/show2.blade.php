@extends('layouts.app')

@section('content')
    <div id="app">
        <show-component></show-component>
    </div>
@endsection

@section('js')
@parent
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection