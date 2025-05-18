<body style="box-sizing: border-box;padding: 0;margin: 0;font-family: Arial, Helvetica, sans-serif;color: #495057;background-color: #f1f3f9;">

	<div style="min-height: 50px;box-sizing: border-box;padding: 0;margin: 0;"></div>

	<div class="section" style="box-sizing: border-box;padding: 0;margin: 0;">

		<div class="container" style="box-sizing: border-box;padding: 0;margin: auto;width: min(600px, 98%);display: block;">
			<div class="header" style="box-sizing: border-box;padding: 30px 0;margin: 0;">
				<a href="{{ url('/') }}" style="box-sizing: border-box;padding: 0;margin: 0;"><img class="header-logo" src="{{ asset('assets/logo.png') }}" alt="logo" style="box-sizing: border-box;padding: 0;margin: auto;width: 100%;display: block;max-width: 290px;">
				</a>
			</div>
		</div>

		<div class="container | bg-white" style="box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;">

			<div class="hero" style="box-sizing: border-box;padding: 20px;margin: 0;background-color: #333338;background-image: linear-gradient(0deg, rgba(51, 51, 56, 0.9), rgba(51, 51, 56, 0.9)), url('');background-position: center;height: 180px;">
				<h2 class="heading heading-2 | text-align-center" style="box-sizing: border-box;padding: 0;margin: 0;font-size: 28px;line-height: 1.4;text-align: center;color: #fff;width: 100%;padding-top: 10%;">{{ $details["title"] }}</h2>
			</div>

			<div class="inner-section" style="box-sizing: border-box;padding: 40px 40px;margin: 0;">

				@foreach($texts ?? [] as $text)
				<p class="text-1 | margin-top-20" style="box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;">
					{!! $text !!}
				</p>
				@endforeach

			</div>

		</div>

	</div>

	<div style="min-height: 100px;box-sizing: border-box;padding: 0;margin: 0;"></div>

</body>