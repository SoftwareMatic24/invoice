@inject('shortCodeController', 'App\Http\Controllers\ShortCodeController')

<div class="sidebar">
	<div class="sidebar-header">
		@if ($brandPortalLogo())
		<a href="{{ url('/portal/dashboard') }}"><img src="{{ url('storage/'.$brandPortalLogo()) }}" alt="logo"></a>
		@else
		<a href="{{ url('/portal/dashboard') }}" class="sidebar-brand">{{ $brandName() ?? '' }}</a>
		@endif
	</div>
	<div class="sidebar-body | thin-scroll-bar">
		<ul class="sidebar-list" role="list">
			@foreach($sidebarItems() as $item)

			@if(!empty($item['type']) && $item['type'] === 'separator')
			<li class="{{ $item['rootClass'] ?? '' }}"></li>
			@else

			<li class="{{ $item['rootClass'] ?? '' }}" {{ implode(' ', $item['rootAttributes'] ?? []) }}>
				<a href="{{ $item['href']['url'] ?? '#' }}" onclick="{{ $item['href']['onclick'] ?? '' }}">
					<span class="title">

						<!-- Left icon -->
						@if(isset($item['leftIcon']) && !isset($item['leftIcon']['type']))
						<x-icon.icon name="{{ $item['leftIcon']['name'] ?? '' }}" class="{{ $item['leftIcon']['class'] ?? '' }}" style="{{ $item['leftIcon']['style'] ?? '' }}" />
						@elseif(isset($item['leftIcon']) && isset($item['leftIcon']['type']) && $item['leftIcon']['type'] === 'raw')
						{!! $item['leftIcon']['content'] !!}
						@endif

						<!-- Label -->
						<span>{{ $item['label'] ?? '' }}</span>

						<!-- Right Icon -->
						@if(isset($item['rightIcon']))
						<x-icon.icon name="{{ $item['rightIcon']['name'] ?? '' }}" class="{{ $item['rightIcon']['class'] ?? '' }}" style="{{ $item['rightIcon']['style'] ?? '' }}" />
						@endif

					</span>
				</a>
				@if(!empty($item['children']))
				<ul>
					@foreach($item['children'] as $child)
					<li class="{{ $child['rootClass'] ?? '' }}" {{ implode(' ', $child['rootAttributes'] ?? []) }}><a href="{{ $child['href']['url'] ?? '#' }}" {{ implode(' ', $child['href']['attributes'] ?? []) }}>{{ $child['label'] ?? '' }}</a></li>
					@endforeach
				</ul>
				@endif
			</li>

			@endif

			@endforeach

			@if (Setting::setting('website-preview-option') ?? false)
			<li class="separator"></li>
			<li>
				<a href="{{ url('/') }}" target="_blank">
					<span class="title">
						<x-icon.icon class="icon" name="solid-link" />
						{{ __('website preview') }}
					</span>
				</a>
			</li>
			@endif

		</ul>
		@if (!empty(Setting::setting('portal-nav-copyright')))
		<p class="sidebar-copyright">
			{{ $shortCodeController->parseShortCode(Setting::setting('portal-nav-copyright')) }}
		</p>
		@endif
	</div>
</div>