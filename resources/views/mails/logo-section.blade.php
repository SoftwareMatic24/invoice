@inject('setting','App\Plugins\Setting\Model\Setting')

@php

$settings = $setting->getSettings();

@endphp

@if($settings["brand-logo"]["column_value"] ?? false)
<div class="section" style="box-sizing: border-box;padding: 0;margin: 0;">
	<div class="container" style="box-sizing: border-box;padding: 0;margin: auto;width: min(600px, 98%);display: block;">
		<div class="header" style="box-sizing: border-box;padding: 30px 0;margin: 0;">
			<a href="{{ url('/') }}" style="box-sizing: border-box;padding: 0;margin: 0;">
				<img class="header-logo" src="{{ url('/storage/'.$settings['brand-logo']['column_value']) }}" alt="logo" style="box-sizing: border-box;padding: 0;margin: auto;width: 100%;display: block;max-width: 290px;">
			</a>
		</div>
	</div>
</div>
@endif