@php
$logoSize = Brand::branding('brand-logo-light-size') ?? 0;
@endphp

@if(Brand::branding('brand-logo-light'))
<img style="<?php echo !empty($logoSize) ? 'height:auto;width:' . $logoSize . 'px' : '' ?>" class="img-logo" src="{{ url('/storage/'.(Brand::branding('brand-logo-light'))) }}" alt="logo">
@endif
