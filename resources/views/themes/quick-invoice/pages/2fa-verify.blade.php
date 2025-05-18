@extends('themes/'.theme().'/layouts/account')

@inject('util','App\Classes\Util')

@section('left')
<div class="register-page-form">
	<form id="account-form" name="signinform" class="row sign-in-form">
		<div class="col-md-12">
			<p class="p-sm input-header">We have sent 2FA Code to your email</p>
			<input class="form-control " type="text" name="code" placeholder="Enter 2FA Code">
		</div>
		<div class="col-md-12">
			<button type="submit" class="btn btn--theme hover--theme submit">Continue</button>
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
	let page = "2fa-verify";
</script>
@stop