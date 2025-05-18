@extends('themes/'.themeSlug().'/layouts/account')

@section('left')
<div class="register-page-form">
	<form id="account-form" class="row sign-in-form">
		<div class="col-md-12">
			<p class="p-sm input-header">{{ __('name') }}</p>
			<input class="form-control " type="text" name="name" placeholder="Your Name">
		</div>
		<div class="col-md-12">
			<p class="p-sm input-header">{{ __('email') }}</p>
			<input class="form-control email" type="email" name="email" placeholder="example@example.com">
		</div>
		<div class="col-md-12">
			<p class="p-sm input-header">{{ __('password') }}</p>
			<div class="wrap-input">
				<span class="btn-show-pass ico-20"><span class="flaticon-visibility eye-pass"></span></span>
				<input class="form-control password" type="password" name="password" placeholder="* * * * * * * * *">
			</div>
		</div>
		<div class="col-md-12">
			<p class="p-sm input-header">{{ __('confirm password') }}</p>
			<div class="wrap-input">
				<span class="btn-show-pass ico-20"><span class="flaticon-visibility eye-pass"></span></span>
				<input class="form-control password" type="password" name="confirm-password" placeholder="* * * * * * * * *">
			</div>
		</div>
		<div class="col-md-12">
			<div class="reset-password-link">
				<p class="p-sm"><a href="{{ url('/portal/forgot-password') }}" class="color--theme">{{ __('forgot your password') }}</a></p>
			</div>
		</div>
		<div class="col-md-12">
			<button type="submit" class="btn btn--theme hover--theme submit">{{ __('register') }}</button>
		</div>
		<div class="col-md-12">
			<p class="create-account text-center">
				{{ __('have-account') }} <a href="{{ url('/portal/login') }}" class="color--theme">{{ __('sign in') }}</a>
			</p>
		</div>
	</form>
</div>
@stop

@section("page-script")
<script>
	var page = "register";
</script>
@stop