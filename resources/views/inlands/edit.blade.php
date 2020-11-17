@extends('layouts.app')

@section('content')
    <div id="app"></div>
@endsection

@section('js')
    <script src="{{ asset('js/inlands/edit.js') }}" type="text/javascript"></script>
@parent
@endsection