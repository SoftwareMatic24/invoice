@php
$logoSize = Brand::branding('brand-logo-size') ?? 0;
@endphp

@if(Brand::branding('brand-logo'))
<img style="<?php echo !empty($logoSize) ? 'height:auto;width:' . $logoSize . 'px' : '' ?>" class="img-logo" src="{{ url('/storage/'.(Brand::branding('brand-logo'))) }}" alt="logo">
@endif
