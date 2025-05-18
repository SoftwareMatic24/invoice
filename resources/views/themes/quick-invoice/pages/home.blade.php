@extends('themes/'.themeSlug().'/layouts/web')
@inject("subscriptionPackage", "App\Plugins\Subscription\Model\SubscriptionPackage")
@inject("currency", "App\Plugins\Currency\Model\Currency")


@section('page-content')
<div class="page font--jakarta">
	<header id="header" class="tra-menu navbar-light white-scroll">
		<div class="header-wrapper">
			<div class="wsmobileheader clearfix">
				@if(!empty(Brand::branding('brand-logo')))
				<span class="smllogo">
					<a href="{{ url('/') }}">
						<img src="{{ asset('storage/'.Brand::branding('brand-logo') ?? '') }}" alt="{{ Brand::branding('brand-name') ?? '' }}">
					</a>
				</span>
				@endif
				<a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
			</div>
			<div class="wsmainfull menu clearfix">
				<div class="wsmainwp clearfix">
					<div class="desktoplogo">
						<a href="{{ url('/') }}" class="logo-black">
							@if(!empty(Brand::branding('brand-logo')))
							<img class="light-theme-img" src="{{ asset('storage/'.Brand::branding('brand-logo') ?? '') }}" alt="{{ Brand::branding('brand-name') ?? '' }} logo">
							@endif
						</a>
					</div>
					<div class="desktoplogo">
					@if(!empty(Brand::branding('brand-logo-light')))
						<a href="{{ url('/') }}" class="logo-white"><img src="{{ asset('storage/'.Brand::branding('brand-logo-light')) }}" alt="{{ Brand::branding('brand-name') ?? '' }} logo light"></a>
						@endif
					</div>
					<nav class="wsmenu clearfix">
						<ul class="wsmenu-list nav-theme">
							<li class="nl-simple" aria-haspopup="true"><a href="{{ url('/#header') }}" class="h-link">{{ __('home') }}</a>
							</li>
							<li class="nl-simple" aria-haspopup="true"><a href="{{ url('/#features') }}" class="h-link">{{ __('features')  }}</a></li>
							<li class="nl-simple" aria-haspopup="true"><a href="{{ url('/#pricing') }}" class="h-link">{{ __('pricing') }}</a></li>
							<li class="nl-simple" aria-haspopup="true"><a href="{{ url('/#faqs') }}" class="h-link">{{ __('faqs') }}</a></li>
							<li class="nl-simple" aria-haspopup="true"><a href="{{ url('/#contact') }}" class="h-link">{{ __('contact') }}</a></li>
							<li class="nl-simple reg-fst-link mobile-last-link" aria-haspopup="true">
								<a href="{{ langURL('/portal/login') }}" class="h-link">{{ __('sign in') }}</a>
							</li>
							<li class="nl-simple" aria-haspopup="true">
								<a href="{{ langURL('/portal/register') }}" class="btn r-04 btn--tra-white hover--theme last-link">
									{{ __('sign up') }}
								</a>
							</li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
	</header>
	<section id="hero-14" class="bg--scroll hero-section">
		<div class="container text-center">
			<div class="row justify-content-center">
				<div class="col-md-10 col-lg-9">
					<div class="hero-14-txt color--white wow fadeIn">
						<h1 class="s-60 w-700">{{ Page::title() }}</h1>
						<p class="s-21">
							{{ __('hero-text-1') }}
						</p>
						<form onsubmit="subscribeNewsletter()" class="quick-form form-shadow">
							<div class="input-group">
								<input type="email" name="newsletter-email" class="form-control email r-06" placeholder="{{ __('your-email') }}" autocomplete="off" required>
								<span class="input-group-btn form-btn">
									<button type="submit" class="btn r-06 btn--theme hover--theme submit">
										{{ __('get started') }}
									</button>
								</span>
							</div>
							<div class="quick-form-msg active"><span class="loading"></span></div>
						</form>
						<p class="btn-txt ico-15">
							<span class="flaticon-check"></span>
							{{ __('free-demo-no-credit-card') }}
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="hero-14-img wow FadeIn">
						<img style="border-radius: 20px;" class="img-fluid" src="{{ asset('themes/quick-invoice/images/dashboard-02.jpg') }}" alt="hero-image">
					</div>
				</div>
			</div>
		</div>
		<div class="wave-shape-bottom">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 190">
				<path fill-opacity="1" d="M0,32L120,53.3C240,75,480,117,720,117.3C960,117,1200,75,1320,53.3L1440,32L1440,320L1320,320C1200,320,960,320,720,320C480,320,240,320,120,320L0,320Z">
				</path>
			</svg>
		</div>
	</section>
	<div id="statistic-1" class="py-100 statistic-section division">
		<div class="container">
			<div class="statistic-1-wrapper">
				<div class="row justify-content-md-center row-cols-1 row-cols-md-3">
					<div class="col">
						<div id="sb-1-1" class="wow FadeIn">
							<div class="statistic-block">
								<div class="statistic-block-digit text-center">
									<span class="s-46 statistic-number"><span class="count-element">890</span>+</span>
								</div>
								<div class="statistic-block-txt color--grey">
									<p class="p-md">{{ __('trust-score-text') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div id="sb-1-2" class="wow FadeIn">
							<div class="statistic-block">

								<div class="statistic-block-digit text-center">
									<span class="s-46 statistic-number"><span class="count-element">100</span>%</span>
								</div>
								<div class="statistic-block-txt color--grey">
									<p class="p-md">{{ __('reliable-score-text') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div id="sb-1-3" class="wow FadeIn">
							<div class="statistic-block">
								<div class="statistic-block-digit text-center">
									<span class="s-46 statistic-number">
										<span class="count-element">5</span>.<span class="count-element">0</span>
								</span>
								</div>
								<div class="statistic-block-txt color--grey">
									<p class="p-md">{{ __('quality-score-text') }}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr class="divider">
	<section id="features" class="pt-100 features-section division">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-10 col-lg-9">
					<div class="section-title mb-70">
						<h2 class="s-50 w-700">{{ __('features-heading') }}</h2>
						<p class="s-21 color--grey">{{ __('features-heading-sub') }}</p>
					</div>
				</div>
			</div>
			<div class="fbox-wrapper">
				<div class="row row-cols-1 row-cols-md-2 rows-3">
					<div class="col">
						<div class="fbox-11 fb-1">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-doc"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('proposal') }}</h3>
								<p>
									{{ __('proposal-description') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-11 fb-2 wow FadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-pdf"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('invoices') }}</h3>
								<p>
									{{ __('invoices-description') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-11 fb-3 wow FadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-calculator"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('expense-sheets') }}</h3>
								<p>
									{{ __('expense-sheets-description') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-11 fb-4 wow FadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-paper-sizes"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('delivery-notice') }}</h3>
								<p>
									{{ __('delivery-notice-description') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-11 fb-5 wow FadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-user"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('client-management') }}</h3>
								<p>
									{{ __('client-management-description') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-11 fb-6 wow FadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-search-engine-1"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('multi-business') }}</h3>
								<p>
									{{ __('multi-business-description') }}
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="row row-cols-1 row-cols-md-2 rows-3" style="margin-top: 20px;">
					<div class="col">
						<div class="fbox-11 fb-1 wow fadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-download"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('download-pdf') }}</h3>
								<p>
									{{ __('download-pdf-description') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-11 fb-2 wow FadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-email-1"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('emails') }}</h3>
								<p>
									{{ __('emails-description') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-11 fb-3 wow FadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-bar-chart"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('stats-graphs') }}</h3>
								<p>
									{{ __('stats-graphs-description') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-11 fb-4 wow FadeIn">
							<div class="fbox-ico-wrap">
								<div class="fbox-ico ico-50">
									<div class="shape-ico color--theme">
										<span class="flaticon-workflow-2"></span>
										<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
											<path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
										</svg>
									</div>
								</div>
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('services-products') }}</h3>
								<p>
									{{ __('services-products-description') }}
								</p>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
	<section id="product" class="py-100 features-section division">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-10 col-lg-9">
					<div class="section-title mb-80">
						<h2 class="s-50 w-700">{{ __('hurry-up') }}</h2>
						<p class="s-21 color--grey">{{ __('hurry-up-description') }}</p>
					</div>
				</div>
			</div>
			<div class="fbox-wrapper text-center">
				<div class="row row-cols-1 row-cols-md-3">
					<div class="col">
						<div class="fbox-2 fb-1 wow FadeIn">
							<div class="fbox-img gr--whitesmoke h-175">
								<img class="img-fluid light-theme-img" src="{{ asset('themes/quick-invoice/images') }}/f_01.png" alt="feature-image">
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('intuitive-dashboard') }}</h3>
								<p>{{ __('dashboard-description') }}</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-2 fb-2 wow FadeIn">
							<div class="fbox-img gr--whitesmoke h-175">
								<img class="img-fluid light-theme-img" src="{{ asset('themes/quick-invoice/images') }}/f_05.png" alt="feature-image">
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('continuous-updates') }}</h3>
								<p>{{ __('updates-description') }}</p>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="fbox-2 fb-3 wow FadeIn">
							<div class="fbox-img gr--whitesmoke h-175">
								<img class="img-fluid light-theme-img" src="{{ asset('themes/quick-invoice/images') }}/f_02.png" alt="feature-image">
							</div>
							<div class="fbox-txt">
								<h3 class="s-22 w-700">{{ __('no-missed-payments') }}</h3>
								<p>
									{{ __('no-miss-payment-description') }}
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="ct-01 content-section division">
		<div class="container">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 order-last order-md-2">
					<div class="txt-block left-column ">
						<span class="section-id">{{ __('productivity-focused') }}</span>
						<h2 class="s-46 w-700">{{ __('achieve-more') }}</h2>
						<p>
							{{ __('stay-innovating') }}
						</p>
						<ul class="simple-list">
							<li class="list-item">
								<p>
									{{ __('goals-in-reach') }}
								</p>
							</li>
							<li class="list-item">
								<p class="mb-0">
									{{ __('lifetime-access') }}
								</p>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-6 order-first order-md-2">
					<div class="img-block right-column ">
						<img class="img-fluid" src="{{ asset('themes/quick-invoice/images') }}/hero-18-img.png" alt="content-image">
					</div>
				</div>
			</div>
		</div>
	</section>
	<sectio class="pt-100 reviews-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-10 col-lg-9">
					<div class="section-title mb-70">
						<h2 class="s-50 w-700">{{ __('happy-customers') }}</h2>
						<p class="s-21 color--grey">{{ __('customer-reviews-heading') }}</p>
					</div>
				</div>
			</div>
			<div class="reviews-2-wrapper rel shape--02 shape--whitesmoke">
				<div class="row align-items-center row-cols-1 row-cols-md-2">
					<div class="col">
						<div id="rw-2-1" class="review-2 bg--white-100 block-shadow r-08">
							<div class="review-ico ico-65"><span class="flaticon-quote"></span></div>
							<div class="review-txt">
								<p>
									{{ __('review-1') }}
								</p>
								<div class="author-data clearfix">
									<div class="review-avatar">
										<img src="{{ asset('themes/quick-invoice/images') }}/team-10.jpg" alt="review-avatar">
									</div>
									<div class="review-author">
										<h3 class="s-18 w-700">Scott</h3>
										<p class="p-sm">{{ __('business owner') }}</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div id="rw-2-2" class="review-2 bg--white-100 block-shadow r-08">
							<div class="review-ico ico-65"><span class="flaticon-quote"></span></div>
							<div class="review-txt">
								<p>
									{{ __('review-2') }}
								</p>
								<div class="author-data clearfix">
									<div class="review-avatar">
										<img src="{{ asset('themes/quick-invoice/images') }}/review-author-6.jpg" alt="review-avatar">
									</div>
									<div class="review-author">
										<h3 class="s-18 w-700">Joel Peterson</h3>
										<p class="p-sm">{{ __('online business owner') }}</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div id="rw-2-3" class="review-2 bg--white-100 block-shadow r-08">
							<div class="review-ico ico-65"><span class="flaticon-quote"></span></div>
							<div class="review-txt">
								<p>
									{{ __('review-3') }}
								</p>
								<div class="author-data clearfix">
									<div class="review-avatar">
										<img src="{{ asset('themes/quick-invoice/images') }}/team-11.jpg" alt="review-avatar">
									</div>
									<div class="review-author">
										<h3 class="s-18 w-700">Jennifer Harper</h3>
										<p class="p-sm">{{ __('real-estate-consultant') }}</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div id="rw-2-4" class="review-2 bg--white-100 block-shadow r-08">
							<div class="review-ico ico-65"><span class="flaticon-quote"></span></div>
							<div class="review-txt">
								<p>
									{{ __('review-4') }}
								</p>
								<div class="author-data clearfix">
									<div class="review-avatar">
										<img src="{{ asset('themes/quick-invoice/images') }}/team-4.jpg" alt="review-avatar">
									</div>
									<div class="review-author">
										<h3 class="s-18 w-700">Evelyn Martinez</h3>
										<p class="p-sm">{{ __('event manager') }}</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<div class="py-50 brands-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-10 col-lg-9">
					<div class="brands-title mb-50">
						<p class="s-18">{{ __('trusted-companies') }}</p>
					</div>
				</div>
			</div>

		</div>
	</div>
	<section id="pricing" class="gr--whitesmoke pt-100 pricing-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-10 col-lg-8">
					<div class="section-title mb-70">
						<h2 class="s-50 w-700">{{ __('simple-pricing') }}</h2>
					</div>
				</div>
			</div>
			<div class="pricing-3-wrapper text-center">
				<div class="row row-cols-1 row-cols-md-3">
					@foreach($subscriptionPackage::getPackages() as $packageIndex=>$package)
					<div class="col">
						<div id="pt-3-1" class="p-table pricing-3-table bg--white-100 block-shadow r-12 wow FadeIn">
							<div class="pricing-table-header">
								<h3 class="s-32" style="margin-bottom: 20px;">{{ $package->title }}</h3>
								@foreach($package->details as $row)
								@if($row["included"] == "1")
								<p class="color--grey">{{ $row->name }}</p>
								@else
								<p class="color--red-400">{{ $row->name }} (not inc.)</p>
								@endif
								@endforeach
								<div class="price mt-25">
									<sup class="color--black">{{$currency::getPrimaryCurrency()["symbol"] ?? "error" }}</sup>
									<span class="color--black">{{ explode(".",$package->price)[0] ?? "error" }}</span>
									<sup class="coins color--black">{{ explode(".",$package->price)[1] ?? "error" }}</sup>
									<sup class="validity color--grey">{{ $package->description }}</sup>
								</div>
							</div>
							<a href="{{ url('/portal/login') }}" class="pt-btn btn btn--theme hover--theme">
								@if(strtolower($package->title ?? "") === "free")
								{{ __('get-started-free') }}
								@else
								{{ __('buy now') }}
								@endif
							</a>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</section>
	<section class="pt-100 ct-04 content-section division">
		<div class="container">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 order-last order-md-2">
					<div class="txt-block left-column ">
						<div class="cbox-2 process-step">
							<div class="ico-wrap">
								<div class="cbox-2-ico bg--theme color--white">1</div>
								<span class="cbox-2-line"></span>
							</div>
							<div class="cbox-2-txt">
								<h2 class="s-22 w-700">{{ __('simplify-optimize-automate') }}</h2>
								<p>
									{{ __('simplify-optimize-automate-desc') }}
								</p>
							</div>
						</div>
						<div class="cbox-2 process-step">
							<div class="ico-wrap">
								<div class="cbox-2-ico bg--theme color--white">2</div>
								<span class="cbox-2-line"></span>
							</div>
							<div class="cbox-2-txt">
								<h2 class="s-22 w-700">{{ __('enhanced-security') }}</h2>
								<p>
									{{ __('enhanced-security-desc') }}
								</p>
							</div>
						</div>
						<div class="cbox-2 process-step">
							<div class="ico-wrap">
								<div class="cbox-2-ico bg--theme color--white">3</div>
							</div>
							<div class="cbox-2-txt">
								<h2 class="s-22 w-700">{{ __('no-personal-data') }}</h2>
								<p class="mb-0">
									{{ __('no-personal-data-desc') }}
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6 order-first order-md-2">
					<div class="img-block ">
						<img class="img-fluid" src="{{ asset('themes/quick-invoice/images') }}/tablet-02.png" alt="content-image">
					</div>
				</div>
			</div>
		</div>
	</section>
	<section id="faqs" class="pt-90 faqs-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-10 col-lg-9">
					<div class="section-title mb-70">
						<h2 class="s-50 w-700">{{ __('q-and-a') }}</h2>
						<p class="s-21 color--grey">{{ __('q-and-a-sub') }}</p>
					</div>
				</div>
			</div>
			<div class="faqs-3-questions">
				<div class="row">
					<div class="col-lg-6">
						<div class="questions-holder">
							<div class="question mb-35 wow FadeIn">
								<h3 class="s-22 w-700"><span>1.</span> {{ __('data-secure-question') }}</h3>
								<p class="color--grey">
									{{ __('data-secure-answer') }}
								</p>
							</div>
							<div class="question mb-35 wow FadeIn">
								<h3 class="s-22 w-700"><span>2.</span> {{ __('customize-invoices-question') }}</h3>
								<p class="color--grey">
									{{ __('customize-invoices-answer') }}
								</p>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="questions-holder">
							<div class="question mb-35 wow FadeIn">
								<h3 class="s-22 w-700"><span>3.</span> {{ __('automate-invoicing-question') }}</h3>
								<p class="color--grey">
									{{ __('automate-invoicing-answer') }}
								</p>

							</div>
							<div class="question mb-35 wow FadeIn">
								<h3 class="s-22 w-700"><span>4.</span> {{ __('support-available-question') }}</h3>
								<p class="color--grey">
									{{ __('support-available-answer') }}
								</p>
							</div>

						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
	<section id="banner-7" class="mt-100 bg--03 bg--scroll banner-section">
		<div class="banner-overlay py-100">
			<div class="container">
				<div class="banner-7-wrapper">
					<div class="row justify-content-center">
						<div class="col-md-8">
							<div class="banner-7-txt color--white text-center">
								<h2 class="s-50 w-700">{{ __('getting-started') }}</h2>
								<a href="#pricing" class="btn r-04 btn--theme hover--tra-white">
									{{ __('get-started-free') }}
								</a>
								<p class="p-sm btn-txt ico-15">
									<span class="flaticon-check"></span>
									{{ __('free-demo-no-credit-card') }}
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section id="contact" class="pt-100  contacts-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-10 col-lg-9">
					<div class="section-title text-center mb-80">
						<h2 class="s-52 w-700">{{ __('questions-let-talk') }}</h2>
						<p class="p-lg">
							{{ __('contact-prompt') }}
						</p>
					</div>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-11 col-lg-10 col-xl-8">
					<div class="form-holder">
						<form onsubmit="submitContact()" name="contact-form" class="row contact-form">
							<div class="col-md-12 input-subject">
								<p class="p-lg">{{ __('question-about') }}</p>
								<input type="text" name="subject" class="form-control name" placeholder="{{ __('subject') }}">
							</div>
							<div class="col-md-12">
								<p class="p-lg">{{ __('your-name') }}: </p>
								<input type="text" name="name" class="form-control name" placeholder="{{ __('your-name') }}*">
							</div>
							<div class="col-md-12">
								<p class="p-lg">{{ __('your-email') }}: </p>
								<input type="text" name="email" class="form-control email" placeholder="{{ __('your-email') }}*">
							</div>
							<div class="col-md-12">
								<p class="p-lg">{{ __('question-details') }}: </p>
								<textarea class="form-control message" name="message" rows="6" placeholder="{{ __('msg') }}"></textarea>
							</div>
							<div class="col-md-12 mt-15 form-btn text-right">
								<button type="submit" class="btn btn--theme hover--theme submit">{{ __('submit request') }}</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<footer id="footer-3" class="pt-100 footer">
		<div class="container">
			<div class="row">
				<div class="col-xl-3">
					<div class="footer-info">
						@if(!empty(Brand::branding('brand-logo')))
						<img class="footer-logo" src="{{ asset('storage/'.Brand::branding('brand-logo')) }}" alt="{{ Brand::branding('brand-name') ?? '' }} logo">
						@endif
					</div>
				</div>
				<div class="col-sm-4 col-md-3 col-xl-2">
					<div class="footer-links fl-1">
						<p class="s-17 w-700">{{ __('company') }}</p>
						<ul class="foo-links clearfix">
							<li>
								<p><a href="#">{{ __('about-us') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('careers') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('our-blog') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('contact-us') }}</a></p>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-sm-4 col-md-3 col-xl-2">
					<div class="footer-links fl-2">
						<p class="s-17 w-700">{{ __('product') }}</p>
						<ul class="foo-links clearfix">
							<li>
								<p><a href="#">{{ __('integration') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('customers') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('pricing') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('help-center') }}</a></p>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-sm-4 col-md-3 col-xl-2">
					<div class="footer-links fl-3">
						<p class="s-17 w-700">{{ __('legal') }}</p>
						<ul class="foo-links clearfix">
							<li>
								<p><a href="#">{{ __('terms-of-use') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('privacy-policy') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('cookie-policy') }}</a></p>
							</li>
							<li>
								<p><a href="#">{{ __('site-map') }}</a></p>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="footer-links fl-4">
						<p class="s-17 w-700">{{ __('connect-with-us') }}</p>
						<p class="footer-mail-link ico-25">
							<a href="mailto:yourdomain@mail.com">{{ __('contact-email') }}</a>
						</p>
						<ul class="footer-socials ico-25 text-center clearfix">
							<li><a href="#"><span class="flaticon-facebook"></span></a></li>
							<li><a href="#"><span class="flaticon-twitter"></span></a></li>
							<li><a href="#"><span class="flaticon-github"></span></a></li>
							<li><a href="#"><span class="flaticon-dribbble"></span></a></li>
						</ul>
					</div>
				</div>
			</div>
			<hr>
			<div class="bottom-footer">
				<div class="row row-cols-1 row-cols-md-2 d-flex align-items-center">
					<div class="col">
						<div class="footer-copyright">
							<p class="p-sm">&copy; {{ date('Y') }} {{ Brand::branding('brand-name') ?? '' }}. <span>{{ __('copyright') }}</span></p>
						</div>
					</div>
					<div class="col">
						<div class="bottom-secondary-link ico-15 text-end ">
							<a href="{{ url('/') }}"><img style="width: 30px;" src="{{ asset('assets/languages/en.png') }}" alt="en"></a>
							<a href="{{ url('/de') }}"><img style="width: 30px;margin-left:10px;" src="{{ asset('assets/languages/de.png') }}" alt="de"></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
</div>
@stop

@section("page-script")
<script>

	async function subscribeNewsletter(){
		if(event) event.preventDefault();
		let emailEl = document.querySelector('[name="newsletter-email"]');

		let n = Notification.show({
			text:'Subscribing, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			method:'POST',
			url: BASE_URL + '/api/newsletter/save',
			body: {
				email: emailEl.value
			}
		});

		Notification.hideAndShowDelayed(n.data.id, {
			heading: response.data.msg,
		
			classes: [response.data.status]
		})

		if(response.data.status === 'success') emailEl.value = '';
	}

	async function submitContact(){
		if(event) event.preventDefault();

		let formEl = document.querySelector('.contact-form');
		let subjectEl = formEl.querySelector('[name="subject"]');
		let nameEl = formEl.querySelector('[name="name"]');
		let emailEl = formEl.querySelector('[name="email"]');
		let msgEl = formEl.querySelector('[name="message"]');

		let postData = {
			subject: subjectEl.value,
			name: nameEl.value,
			email: emailEl.value,
			msg: msgEl.value
		};

		let n = Notification.show({
			text: 'Processing, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/contact-message/send',
			body: postData
		});

		Notification.hideAndShowDelayed(n.data.id, {
			heading: response.data.heading,
			description: response.data.description,
			classes: [response.data.status]
		})

		if(response.data.status === 'success') formEl.reset();
	}

</script>
@stop