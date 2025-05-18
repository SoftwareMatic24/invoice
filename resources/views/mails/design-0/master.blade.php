@php
$settings = Cache::get("settings");
@endphp

<body style="box-sizing: border-box;padding: 0;margin: 0;font-family: Arial, Helvetica, sans-serif;color: #333338;background-color: #f1f3f9;">
	<div style="min-height: 50px;box-sizing: border-box;padding: 0;margin: 0;"></div>
	<div style="box-sizing: border-box;padding: 0;margin: 0;">
		<div style="box-sizing: border-box;padding: 0;margin: auto;width: min(600px, 98%);display: block;">
			<div style="box-sizing: border-box;padding: 30px 0;margin: 0;">
				@if(isset($settings["brand-logo"]["column_value"]))
				<a href='{{ url("/") }}' style="box-sizing: border-box;padding: 0;margin: 0;"><img src="{{ url('storage/'.$settings['brand-logo']['column_value']) }}" alt="logo" style="box-sizing: border-box;padding: 0;margin: auto;width: 100%;display: block;max-width: 290px;"></a>
				@else
				<h2 style="text-align: center;font-size:22px;margin:0;">{{ config("app.name") }}</h2>
				@endif
			</div>
		</div>
		{!! $data !!}
	</div>
	{!! $signature ?? "" !!}
	<div style="min-height: 100px;box-sizing: border-box;padding: 0;margin: 0;"></div>
</body>