@extends('layouts.app')

@section('content')
    <div id="app"></div>
@endsection

@section('js')
    <script src="{{ asset('js/quote/edit.js?v=1') }}" type="text/javascript"></script>
@parent
@endsection
