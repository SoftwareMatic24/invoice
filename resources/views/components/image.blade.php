<img

	@if(!empty($width))
	width="{{ $width }}"
	@endif

	@if(!empty($height))
	height="{{ $height }}"
	@endif
	
	class="{{ $class }}" 
	style="{{ $style }}" 
	data-src="{{ $dataSrc }}" 
	src="{{ $src }}" 
	title="{{ $title }}" />
