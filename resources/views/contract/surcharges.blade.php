@extends('layouts.app')

@section('content')
    <div id="app2">
        
    </div>
@endsection

@section('js')
@parent
    <script src="{{ asset('js/contracts/app.js') }}" type="text/javascript"></script>
@endsection