@extends('layouts.master')

@section('head')
{!! urldecode($settings["global-scripts-head"]["column_value"] ?? "") !!}
@stop

@section('foot')
{!! urldecode($settings["global-scripts-foot"]["column_value"] ?? "") !!}
@stop

@section('script')
	@yield('web-script')
@stop