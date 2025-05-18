@extends('themes/'.themeSlug().'/layouts/account')

@inject('util','App\Classes\Util')

@section('left')
<div class="register-page-form">
	<form id="account-form" class="row sign-in-form">
		<div class="col-md-12">
			<p class="p-sm input-header">New Password</p>
			<div class="wrap-input">
				<span class="btn-show-pass ico-20"><span class="flaticon-visibility eye-pass"></span></span>
				<input class="form-control password" type="password" name="password" placeholder="* * * * * * * * *">
			</div>
		</div>
		<div class="col-md-12">
			<p class="p-sm input-header">Confirm Password</p>
			<div class="wrap-input">
				<span class="btn-show-pass ico-20"><span class="flaticon-visibility eye-pass"></span></span>
				<input class="form-control password" type="password" name="confirm-password" placeholder="* * * * * * * * *">
			</div>
		</div>
		<div class="col-md-12">
			<button type="submit" class="btn btn--theme hover--theme submit">Update Password</button>
		</div>
		<div class="col-md-12">
			<p class="create-account text-center">
				Have an account? <a href="{{ url('/portal/login') }}" class="color--theme">Sign in</a>
			</p>
		</div>
	</form>
</div>
@stop

@section("page-script")
<script>
	let page = 'reset-password';
</script>
@stop