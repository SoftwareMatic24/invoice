@extends('layouts.account')
@inject('appearanceController', 'App\Plugins\Appearance\Controller\AppearanceController')

@section("style")
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

@section("layout-content")
<div class="page font--jakarta">
	<div id="login" class="bg--scroll login-section division">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-11">
					<div class="register-page-wrapper r-16 bg--fixed">
						<div class="row">
							<div class="col-md-6">
								<div class="register-page-txt color--white">
									@if(isset(Cache::get("settings")["brand-logo"]))
										<img class="img-fluid" src="{{ asset('storage/'.Cache::get('settings')['brand-logo-light']['column_value'] ?? '') }}" alt="logo-image">
									@endif
									
									<h2 class="s-42 w-700">{{ __('welcome') }}</h2>
									<h2 class="s-42 w-700">{{ __('back to') }} {{ config('app.name') }}</h2>
									<p class="p-md mt-25">
										{{ __('welcome-sub') }}
									</p>
									<div class="register-page-copyright">
										<p class="p-sm">&copy; {{ date('Y') }} {{ config('app.name') }}. <span>{{ __('copyright') }}</span></p>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								@yield('left')
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section("layout-script")
<script src="{{ asset('themes/'.themeSlug().'/js/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('themes/'.themeSlug().'/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('themes/'.themeSlug().'/js/menu.js') }}"></script>
<script src="{{ asset('themes/'.themeSlug().'/js/custom.js') }}"></script>
@yield("page-script")
@stop