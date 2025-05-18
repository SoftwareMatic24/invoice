@extends('layouts.web')
@inject('menuController', 'App\Plugins\Menu\Controller\MenuController')
@inject('invoiceConfig', 'App\Plugins\QuickInvoice\Classes\InvoiceConfig')
@inject('util', 'App\Classes\Util')
@inject('appearanceController', 'App\Plugins\Appearance\Controller\AppearanceController')

@php
if($invoiceConfig::$isMultiUser === false) {
header("Location: ". $util->prefixedURL("/login"));
exit;
}
@endphp

@section('style')

<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">

<link href="{{ asset('themes/'.themeSlug().'/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('themes/'.themeSlug().'/css/flaticon.css') }}" rel="stylesheet">
<link href="{{ asset('themes/'.themeSlug().'/css/menu.css') }}" rel="stylesheet">
<link href="{{ asset('themes/'.themeSlug().'/css/dropdown-effects/fade-down.css') }}" media="all" rel="stylesheet">
<link href="{{ asset('themes/'.themeSlug().'/css/skyblue-theme.css') }}" rel="stylesheet">
<link href="{{ asset('themes/'.themeSlug().'/css/responsive.css') }}" rel="stylesheet">

{!! $appearanceController->generateThemeStyle(activeTheme()['options']); !!}
@stop

@section('content')
@yield('page-content')
@stop


@section('web-script')
<script src="{{ asset('themes/'.themeSlug().'/js/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('themes/'.themeSlug().'/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('themes/'.themeSlug().'/js/menu.js') }}"></script>
<script src="{{ asset('themes/'.themeSlug().'/js/custom.js') }}"></script>
@yield('page-script')
@stop