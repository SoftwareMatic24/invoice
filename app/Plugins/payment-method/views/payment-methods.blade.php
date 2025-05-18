@extends('layouts.portal')
@section('main-content')
<div>
	<div class="tabs-container">
		@if(request()["loggedInUser"]["role_title"] === "admin")
		<div class="tabs-header">
			<ul>
				@if($settings["user-payment-methods"]["column_value"] == 1)
				<li class="active">
					<span>{{ __('my payment methods') }}</span>
				</li>
				@endif
				<li class="{{ $settings['user-payment-methods']['column_value'] == '0' ? 'active' : '' }}">
					<span>{{ __('system payment methods') }}</span>
				</li>
			</ul>
		</div>
		@endif

		<div class="tabs-body | {{request()['loggedInUser']['role_title'] === 'admin' ? 'margin-top-5' : ''}}">
			@if($settings["user-payment-methods"]["column_value"] == 1)
			<div class="active">
				<p>{{ __('select-your-payment-method-to-setup') }}</p>
				<div id="user-payment-methods" class="image-icon-box-container | margin-top-3">
					@foreach($paymentMethods as $paymentMethod)
					<a href="{{ url('/portal/payment-method/methods/user') }}/{{ $paymentMethod['slug'] }}" class="image-icon-box">
						<img class="icon" src="{{ url('plugin/payment-method/assets/'.$paymentMethod['image']) }}" alt="{{ $paymentMethod['title'] }}">
						<p class="heading">{{ ucwords(__(strtolower($paymentMethod["title"]))) }}</p>
					</a>
					@endforeach
				</div>
			</div>
			@endif
			<div class="{{ $settings['user-payment-methods']['column_value'] == '0' ? 'active' : '' }}">
				<p>{{ __('select-system-payment-method-to-setup') }}</p>
				<div id="system-payment-methods" class="image-icon-box-container | margin-top-3">
					@foreach($paymentMethods as $paymentMethod)
					<a href="{{ url('/portal/payment-method/methods/system') }}/{{ $paymentMethod['slug'] }}" class="image-icon-box">
						<img class="icon" src="{{ url('plugin/payment-method/assets/'.$paymentMethod['image']) }}" alt="{{ $paymentMethod['title'] }}">
						<p class="heading">{{ ucwords(__(strtolower($paymentMethod["title"]))) }}</p>
					</a>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
@stop