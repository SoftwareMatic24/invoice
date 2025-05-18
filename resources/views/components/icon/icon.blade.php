<svg {{ $attributes->merge(['style'=>'']) }} >
	<use xlink:href="{{ asset('assets/icons.svg#') }}{{ $name ?? '' }}" />
</svg>