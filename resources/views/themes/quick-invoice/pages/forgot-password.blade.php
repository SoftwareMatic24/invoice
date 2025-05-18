@extends('themes/'.themeSlug().'/layouts/account')
@section('left')
<div class="register-page-form">
	<form id="account-form" name="signinform" class="row sign-in-form">
		<div class="col-md-12">
			<p class="p-sm input-header">{{ __('email') }}</p>
			<input class="form-control email" type="email" name="email" placeholder="example@example.com">
		</div>
		<div class="col-md-12">
			<button type="submit" class="btn btn--theme hover--theme submit">{{ __('continue') }}</button>
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
	page = "forgot-password";
</script>
@stop